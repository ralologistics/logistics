<?php
/**
 * store.php (Job Booking - CREATE)
 * Uses mysqli + prepared statements + transaction
 *
 * ✅ Inserts in this order:
 *  1) job_addresses  (Sender)
 *  2) job_addresses  (Receiver)
 *  3) job_bookings
 *  4) job_tracking_notifications  (communication_type + contact_value + notification_type)
 *  5) job_additional_information  (insurance_type + dg_signatory)
 *  6) job_packages (multiple)
 *  7) job_additional_services (pivot, multiple)
 *  8) job_attachments (single file)
 */

session_start();
require "db.php"; // must provide $conn = new mysqli(...)

// if (!isset($_SESSION['user_id'])) {
//   header("Location: login.php");
//   exit;
// }

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// helper
function post($key, $default = null) {
  return isset($_POST[$key]) && $_POST[$key] !== '' ? trim($_POST[$key]) : $default;
}

// Logged-in user
// $created_by = (int)$_SESSION['user_id'];

/* ===============================
   1️⃣ COLLECT FORM DATA
   =============================== */

// Customer Details (your form currently has text fields)
$customer_code  = post('customer_name_code');     // text
$company_name        = post('company');                // text (or id if you change)
$customer_reference  = post('customer_reference');
$receiver_reference  = post('receiver_reference');
$freight_ready_by    = post('freight_ready_by');       // datetime-local string

// Sender (address)
$sender_country        = post('sender_country');
$sender_name           = post('sender_name');
$sender_find_address   = post('sender_find_address');  // not stored in DB by default (optional)
$sender_building       = post('sender_building');
$sender_street_no      = post('sender_street_no');
$sender_street         = post('sender_street');
$sender_suburb         = post('sender_suburb');
$sender_city_town      = post('sender_city_town');
$sender_state          = post('sender_state');
$sender_postcode       = post('sender_postcode');
$sender_contact_person = post('sender_contact_person');
$sender_mobile         = post('sender_mobile');
$sender_phone          = post('sender_phone');
$sender_email          = post('sender_email');
$pickup_instruction    = post('pickup_instruction');

// Receiver (address)
$receiver_country        = post('receiver_country');
$receiver_name           = post('receiver_name');
$receiver_find_address   = post('receiver_find_address'); // optional
$receiver_building       = post('receiver_building');
$receiver_street_no      = post('receiver_street_no');
$receiver_street         = post('receiver_street');
$receiver_suburb         = post('receiver_suburb');
$receiver_city_town      = post('receiver_city_town');
$receiver_state          = post('receiver_state');
$receiver_postcode       = post('receiver_postcode');
$receiver_contact_person = post('receiver_contact_person');
$receiver_mobile         = post('receiver_mobile');
$receiver_phone          = post('receiver_phone');
$receiver_email          = post('receiver_email');
$delivery_instruction    = post('delivery_instruction');

// Checkbox
$signature_required = isset($_POST['signature_required']) ? 1 : 0;

// Tracking Notification (as per your latest requirement)
$tracking_communication_type = post('tracking_communication_type');  // dropdown Email/Phone
$tracking_contact_value      = post('tracking_contact_value');       // email OR phone
$tracking_notification_type  = post('tracking_notification_type');   // dropdown SMS/Email/Push

// Additional Information
$insurance_type = post('insurance_type'); // dropdown
$dg_signatory   = post('dg_signatory');   // dropdown Yes/No

// Additional services (multi) => MUST be IDs in option value
$additional_services = $_POST['additional_services'] ?? []; // array

// Packages (array)
$packages = $_POST['packages'] ?? [];

/* ===============================
   2️⃣ FILE UPLOAD
   =============================== */
$attachment_path = null;
$attachment_name = null;
$attachment_mime = null;
$attachment_size = null;

if (!empty($_FILES['attachment']['name'])) {
  $uploadDir = __DIR__ . "/uploads/job_attachments/";
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $attachment_name = basename($_FILES['attachment']['name']);
  $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $attachment_name);
  $targetFile = $uploadDir . $safeName;

  if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
    $attachment_path = "uploads/job_attachments/" . $safeName;
    $attachment_mime = $_FILES['attachment']['type'] ?? null;
    $attachment_size = $_FILES['attachment']['size'] ?? null;
  }
}

/* ===============================
   3️⃣ INSERT USING TRANSACTION
   =============================== */

try {
  $conn->begin_transaction();

  // 3.1 Insert Sender Address
  $sqlAddr = "
    INSERT INTO job_addresses
    (country_code, name, building, street_no, street, suburb, city_town, state, postcode,
     contact_person, mobile, phone, email)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
  ";
  $stmtAddr = $conn->prepare($sqlAddr);
  $stmtAddr->bind_param(
    "sssssssssssss",
    $sender_country,
    $sender_name,
    $sender_building,
    $sender_street_no,
    $sender_street,
    $sender_suburb,
    $sender_city_town,
    $sender_state,
    $sender_postcode,
    $sender_contact_person,
    $sender_mobile,
    $sender_phone,
    $sender_email
  );
  $stmtAddr->execute();
  $sender_address_id = $conn->insert_id;

  // 3.2 Insert Receiver Address (reuse same prepared statement)
  $stmtAddr->bind_param(
    "sssssssssssss",
    $receiver_country,
    $receiver_name,
    $receiver_building,
    $receiver_street_no,
    $receiver_street,
    $receiver_suburb,
    $receiver_city_town,
    $receiver_state,
    $receiver_postcode,
    $receiver_contact_person,
    $receiver_mobile,
    $receiver_phone,
    $receiver_email
  );
  $stmtAddr->execute();
  $receiver_address_id = $conn->insert_id;

  // 3.3 Insert Job Booking
  /**
   * ⚠️ IMPORTANT:
   * Your earlier schema had customer_id/company_id.
   * But your HTML is text-based (customer_name_code, company).
   * So this insert assumes your job_bookings table includes:
   *   customer_name_code (VARCHAR), company (VARCHAR)
   *
   * If your table uses customer_id/company_id instead,
   * replace columns & bind types accordingly.
   */



// Check if customer exists
$checkCustomerSql = "SELECT id FROM customers WHERE code = ?";
$stmt = mysqli_prepare($conn, $checkCustomerSql);
mysqli_stmt_bind_param($stmt, "s", $customer_code);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $customer_id);

if (mysqli_stmt_fetch($stmt)) {
    // Customer exists, $customer_id already available
    mysqli_stmt_close($stmt);
} else {
    // Customer does not exist, insert new
    mysqli_stmt_close($stmt);

    $insertCustomer = "INSERT INTO customers (name, code) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertCustomer);
    mysqli_stmt_bind_param($stmt, "ss", $customer_code, $customer_code);
    mysqli_stmt_execute($stmt);

    // Get newly inserted customer ID
    $customer_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}

// Check if company already exists
$checkSql = "SELECT id FROM companies WHERE name = ?";
$stmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($stmt, "s", $company_name);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $company_id);

if (mysqli_stmt_fetch($stmt)) {
    // Company exists, $company_id already available
    mysqli_stmt_close($stmt);
} else {
    // Company does not exist, insert new
    mysqli_stmt_close($stmt);

    $insertCompany = "INSERT INTO companies (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $insertCompany);
    mysqli_stmt_bind_param($stmt, "s", $company_name);
    mysqli_stmt_execute($stmt);

    // Get newly inserted company ID
    $company_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}



 $booking_id = 'JB-' . uniqid();
  $sqlJob = "
    INSERT INTO job_bookings
    ( customer_id, company_id, customer_reference, receiver_reference, freight_ready_by,
     sender_address_id, receiver_address_id, pickup_instruction, delivery_instruction, signature_required, status, booking_id)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
  ";
  $status = "draft";
  $stmtJob = $conn->prepare($sqlJob);
  $stmtJob->bind_param(
    "iisssiiisiss",
    $customer_id,
    $company_id,
    $customer_reference,
    $receiver_reference,
    $freight_ready_by,
    $sender_address_id,
    $receiver_address_id,
    $pickup_instruction,
    $delivery_instruction,
    $signature_required,
    $status,
    $booking_id
  );
  $stmtJob->execute();
  $job_id = $conn->insert_id;

/* 
  $sqlTrack = "
    INSERT INTO job_tracking_notifications
    (job_booking_id, communication_type, contact_value, notification_type, enabled)
    VALUES (?,?,?,?,?)
  ";
  $enabled = 1;
  $stmtTrack = $conn->prepare($sqlTrack);
  $stmtTrack->bind_param(
    "isssi",
    $job_id,
    $tracking_communication_type,
    $tracking_contact_value,
    $tracking_notification_type,
    $enabled
  );
  $stmtTrack->execute();


  $sqlAddInfo = "
    INSERT INTO job_additional_information
    (job_booking_id, insurance_type, dg_signatory)
    VALUES (?,?,?)
  ";
  $stmtAdd = $conn->prepare($sqlAddInfo);
  $stmtAdd->bind_param("iss", $job_id, $insurance_type, $dg_signatory);
  $stmtAdd->execute();


  if (is_array($packages) && count($packages) > 0) {
    $sqlPkg = "
      INSERT INTO job_packages
      (job_booking_id, package_name, units, weight_kg, length_cm, width_cm, height_cm,
       cubic_m3, package_type_id, dg_type_id, remarks)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ";
    $stmtPkg = $conn->prepare($sqlPkg);

    foreach ($packages as $p) {
      $package_name   = isset($p['package_name']) ? trim($p['package_name']) : null;
      $units          = isset($p['units']) ? (int)$p['units'] : 0;
      $weight_kg_pkg  = isset($p['weight_kg']) ? (float)$p['weight_kg'] : 0;

      $length_cm      = $p['length_cm'] ?? null;
      $width_cm       = $p['width_cm'] ?? null;
      $height_cm      = $p['height_cm'] ?? null;
      $cubic_m3       = $p['cubic_m3'] ?? null;

      $package_type_id = isset($p['package_type']) ? (int)$p['package_type'] : 0;
      $dg_type_id      = isset($p['dg_type']) && $p['dg_type'] !== "" ? (int)$p['dg_type'] : null;

      $remarks        = isset($p['remarks']) ? trim($p['remarks']) : null;


      if ($package_name === null && $units === 0 && $weight_kg_pkg == 0) {
        continue;
      }


      $stmtPkg->bind_param(
        "isidddddiiis",
        $job_id,
        $package_name,
        $units,
        $weight_kg_pkg,
        $length_cm,
        $width_cm,
        $height_cm,
        $cubic_m3,
        $package_type_id,
        $dg_type_id,
        $remarks
      );
      $stmtPkg->execute();
    }
  }


  if (is_array($additional_services) && count($additional_services) > 0) {
    $sqlSvc = "INSERT INTO job_additional_services (job_booking_id, service_id) VALUES (?,?)";
    $stmtSvc = $conn->prepare($sqlSvc);

    foreach ($additional_services as $serviceId) {
      if ($serviceId === "" || $serviceId === null) continue;
      $serviceId = (int)$serviceId;
      $stmtSvc->bind_param("ii", $job_id, $serviceId);
      $stmtSvc->execute();
    }
  }


  if ($attachment_path !== null) {
    $sqlAtt = "
      INSERT INTO job_attachments
      (job_booking_id, file_name, file_path, mime_type, file_size_bytes)
      VALUES (?,?,?,?,?)
    ";
    $stmtAtt = $conn->prepare($sqlAtt);
    $stmtAtt->bind_param(
      "isssi",
      $job_id,
      $attachment_name,
      $attachment_path,
      $attachment_mime,
      $attachment_size
    );
    $stmtAtt->execute();
  }

*/
  $conn->commit();

  header("Location: job-queue.php?success=1");
  exit;

} catch (Throwable $e) {
  $conn->rollback();
  die("Store failed: " . $e->getMessage());
}
