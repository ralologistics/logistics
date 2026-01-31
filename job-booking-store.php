<?php
session_start();
require 'db.php';
require 'functions.php';

header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . URL . '/job-booking-form.php');
    exit;
}

$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$company_id = isset($_POST['company_id']) ? (int)$_POST['company_id'] : 0;
$customer_reference = trim($_POST['customer_reference'] ?? '');
$receiver_reference = trim($_POST['receiver_reference'] ?? '');
$freight_ready_by = !empty($_POST['freight_ready_by']) ? $_POST['freight_ready_by'] : null;
$insurance_type = $_POST['insurance_type'] ?? 'Owners Risk';
$dg_signatory_id = !empty($_POST['dg_signatory_id']) ? (int)$_POST['dg_signatory_id'] : null;
$action = $_POST['action'] ?? 'save';

// Validate required
if (!$customer_id || !$company_id) {
    $_SESSION['job_booking_error'] = 'Customer and Company are required.';
    header('Location: ' . URL . '/job-booking-form.php');
    exit;
}

// Generate unique booking_id
$booking_id = 'JB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

$conn->begin_transaction();
try {
    // 1. Insert job_bookings
    $stmt = $conn->prepare("
        INSERT INTO job_bookings (job_type, customer_id, company_id, customer_reference, receiver_reference, freight_ready_by, booking_id, status)
        VALUES ('GENERAL', ?, ?, ?, ?, ?, ?, 'DRAFT')
    ");
    $stmt->bind_param('iissss', $customer_id, $company_id, $customer_reference, $receiver_reference, $freight_ready_by, $booking_id);
    $stmt->execute();
    $booking_pk = (int)$conn->insert_id;
    $stmt->close();
    if ($booking_pk <= 0) throw new Exception('Failed to create job booking.');

    // 2. Sender address
    $stmt_addr = $conn->prepare("
        INSERT INTO addresses (country_id, name, building, street_no, street, suburb, find_address, city, state, postcode, contact_person, mobile, phone, email, pickup_instruction, signature_required, delivery_instruction)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NULL)
    ");
    $country_id = (int)($_POST['sender_country_id'] ?? 0);
    $name = trim($_POST['sender_name'] ?? '');
    $building = trim($_POST['sender_building'] ?? '');
    $street_no = trim($_POST['sender_street_no'] ?? '');
    $street = trim($_POST['sender_street'] ?? '');
    $suburb = trim($_POST['sender_suburb'] ?? '');
    $find_address = trim($_POST['sender_find_address'] ?? '');
    $city = trim($_POST['sender_city'] ?? '');
    $state = trim($_POST['sender_state'] ?? '');
    $postcode = trim($_POST['sender_postcode'] ?? '');
    $contact_person = trim($_POST['sender_contact_person'] ?? '');
    $mobile = trim($_POST['sender_mobile'] ?? '');
    $phone = trim($_POST['sender_phone'] ?? '');
    $email = trim($_POST['sender_email'] ?? '');
    $pickup_instruction = trim($_POST['sender_pickup_instruction'] ?? '');
    $stmt_addr->bind_param('issssssssssssss', $country_id, $name, $building, $street_no, $street, $suburb, $find_address, $city, $state, $postcode, $contact_person, $mobile, $phone, $email, $pickup_instruction);
    $stmt_addr->execute();
    $sender_address_id = (int)$conn->insert_id;
    $stmt_addr->close();

    $stmt_ja = $conn->prepare("INSERT INTO job_addresses (booking_id, address_id, party_role, instructions, signature_required) VALUES (?, ?, 'SENDER', ?, 0)");
    $stmt_ja->bind_param('iis', $booking_pk, $sender_address_id, $pickup_instruction);
    $stmt_ja->execute();
    $stmt_ja->close();

    // 3. Receiver address
    $country_id_r = (int)($_POST['receiver_country_id'] ?? 0);
    $name_r = trim($_POST['receiver_name'] ?? '');
    $building_r = trim($_POST['receiver_building'] ?? '');
    $street_no_r = trim($_POST['receiver_street_no'] ?? '');
    $street_r = trim($_POST['receiver_street'] ?? '');
    $suburb_r = trim($_POST['receiver_suburb'] ?? '');
    $find_address_r = trim($_POST['receiver_find_address'] ?? '');
    $city_r = trim($_POST['receiver_city'] ?? '');
    $state_r = trim($_POST['receiver_state'] ?? '');
    $postcode_r = trim($_POST['receiver_postcode'] ?? '');
    $contact_person_r = trim($_POST['receiver_contact_person'] ?? '');
    $mobile_r = trim($_POST['receiver_mobile'] ?? '');
    $phone_r = trim($_POST['receiver_phone'] ?? '');
    $email_r = trim($_POST['receiver_email'] ?? '');
    $delivery_instruction = trim($_POST['receiver_delivery_instruction'] ?? '');
    $signature_required = isset($_POST['receiver_signature_required']) ? 1 : 0;

    $stmt_addr2 = $conn->prepare("
        INSERT INTO addresses (country_id, name, building, street_no, street, suburb, find_address, city, state, postcode, contact_person, mobile, phone, email, pickup_instruction, signature_required, delivery_instruction)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?)
    ");
    $stmt_addr2->bind_param('isssssssssssssis', $country_id_r, $name_r, $building_r, $street_no_r, $street_r, $suburb_r, $find_address_r, $city_r, $state_r, $postcode_r, $contact_person_r, $mobile_r, $phone_r, $email_r, $signature_required, $delivery_instruction);
    $stmt_addr2->execute();
    $receiver_address_id = (int)$conn->insert_id;
    $stmt_addr2->close();

    $stmt_ja2 = $conn->prepare("INSERT INTO job_addresses (booking_id, address_id, party_role, instructions, signature_required) VALUES (?, ?, 'RECEIVER', ?, ?)");
    $stmt_ja2->bind_param('iisi', $booking_pk, $receiver_address_id, $delivery_instruction, $signature_required);
    $stmt_ja2->execute();
    $stmt_ja2->close();

    // 4. Job packages
    if (!empty($_POST['packages']) && is_array($_POST['packages'])) {
        $stmt_pkg = $conn->prepare("INSERT INTO job_packages (booking_id, units, weight_kg, length_cm, width_cm, height_cm, cubic_m3, package_type_id, dg_type_id, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($_POST['packages'] as $p) {
            $units = (int)($p['units'] ?? 0);
            $weight_kg = isset($p['weight_kg']) ? (float)$p['weight_kg'] : null;
            $length_cm = !empty($p['length_cm']) ? (float)$p['length_cm'] : null;
            $width_cm = !empty($p['width_cm']) ? (float)$p['width_cm'] : null;
            $height_cm = !empty($p['height_cm']) ? (float)$p['height_cm'] : null;
            $cubic_m3 = isset($p['cubic_m3']) ? (float)$p['cubic_m3'] : null;
            $package_type_id = !empty($p['package_type_id']) ? (int)$p['package_type_id'] : null;
            $dg_type_id = !empty($p['dg_type_id']) ? (int)$p['dg_type_id'] : null;
            $remarks = trim($p['remarks'] ?? '');
            if ($units > 0 && $weight_kg !== null) {
                $stmt_pkg->bind_param('iidddddiis', $booking_pk, $units, $weight_kg, $length_cm, $width_cm, $height_cm, $cubic_m3, $package_type_id, $dg_type_id, $remarks);
                $stmt_pkg->execute();
            }
        }
        $stmt_pkg->close();
    }

    // 5. Job tracking notifications
    if (!empty($_POST['notifications']) && is_array($_POST['notifications'])) {
        $stmt_not = $conn->prepare("INSERT INTO job_tracking_notifications (job_id, notification_type_id, communication_type, contact, message, is_sent) VALUES (?, ?, ?, ?, '', 0)");
        foreach ($_POST['notifications'] as $n) {
            $comm = trim($n['communication_type'] ?? '');
            if (!in_array($comm, ['EMAIL', 'PHONE', 'SMS', 'WHATSAPP', 'PUSH'], true)) continue;
            $contact = trim($n['contact'] ?? '');
            $notif_type_id = !empty($n['notification_type_id']) ? (int)$n['notification_type_id'] : null;
            $stmt_not->bind_param('iiss', $booking_pk, $notif_type_id, $comm, $contact);
            $stmt_not->execute();
        }
        $stmt_not->close();
    }

    // 6. Job additional information
    $allowed_insurance = ['Owners Risk', 'Carriers Risk', 'All Risk', 'Total Loss Only', 'Third Party', 'Limited Carrier Liability'];
    if (!in_array($insurance_type, $allowed_insurance)) $insurance_type = 'Owners Risk';
    $stmt_ai = $conn->prepare("INSERT INTO job_additional_information (booking_id, insurance_type, dg_signatory_id, customer_reference_2, receiver_reference_2) VALUES (?, ?, ?, ?, ?)");
    $stmt_ai->bind_param('isiss', $booking_pk, $insurance_type, $dg_signatory_id, $customer_reference, $receiver_reference);
    $stmt_ai->execute();
    $stmt_ai->close();

    // 7. File attachments
    $upload_dir = __DIR__ . '/uploads/job_attachments/';
    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
    if (!empty($_FILES['attachments']['name'][0])) {
        $stmt_att = $conn->prepare("INSERT INTO job_attachments (booking_id, file_path) VALUES (?, ?)");
        foreach ($_FILES['attachments']['name'] as $i => $fname) {
            if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = pathinfo($fname, PATHINFO_EXTENSION);
            $saved = $upload_dir . $booking_pk . '_' . time() . '_' . $i . '.' . $ext;
            if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $saved)) {
                $rel = 'uploads/job_attachments/' . basename($saved);
                $stmt_att->bind_param('is', $booking_pk, $rel);
                $stmt_att->execute();
            }
        }
        $stmt_att->close();
    }

    $conn->commit();
    unset($_SESSION['job_booking_error']);
    $_SESSION['job_booking_success'] = 'Job booking saved. Booking ID: ' . $booking_id;
    if ($action === 'save_print') {
        header('Location: ' . URL . '/job-booking-print.php?id=' . $booking_pk);
    } else {
        header('Location: ' . URL . '/job-booking-form.php');
    }
    exit;
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['job_booking_error'] = $e->getMessage();
    header('Location: ' . URL . '/job-booking-form.php');
    exit;
}
