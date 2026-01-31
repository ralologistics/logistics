<?php
session_start();
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_code = trim($_POST['customer_code'] ?? '');
$role = strtoupper(trim($_POST['role'] ?? ''));
if (!in_array($role, ['SENDER', 'RECEIVER'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

// If no customer selected, create new customer – code auto-generate (C00001, C00002, …)
if ($customer_id <= 0) {
    if ($customer_name === '') {
        echo json_encode(['success' => false, 'message' => 'Customer name required for new customer']);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO customers (name, code, email, phone, address) VALUES (?, '', '', '', '')");
    $stmt->bind_param('s', $customer_name);
    $stmt->execute();
    $customer_id = (int)$conn->insert_id;
    $stmt->close();
    if ($customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Failed to create customer']);
        exit;
    }
    $customer_code = 'C' . str_pad((string)$customer_id, 5, '0', STR_PAD_LEFT);
    $conn->query("UPDATE customers SET code = '" . $conn->real_escape_string($customer_code) . "' WHERE id = " . $customer_id);
} else {
    $r = $conn->prepare("SELECT name, code FROM customers WHERE id = ?");
    $r->bind_param('i', $customer_id);
    $r->execute();
    $res = $r->get_result();
    $cust = $res->fetch_assoc();
    $r->close();
    if (!$cust) {
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit;
    }
    $customer_name = $cust['name'];
}

$prefix = $role === 'SENDER' ? 'sender_' : 'receiver_';
$country_id = (int)($_POST[$prefix . 'country_id'] ?? 0);
$name = trim($_POST[$prefix . 'name'] ?? '');
if ($name === '') $name = $customer_name;
$building = trim($_POST[$prefix . 'building'] ?? '');
$street_no = trim($_POST[$prefix . 'street_no'] ?? '');
$street = trim($_POST[$prefix . 'street'] ?? '');
$suburb = trim($_POST[$prefix . 'suburb'] ?? '');
$find_address = trim($_POST[$prefix . 'find_address'] ?? '');
$city = trim($_POST[$prefix . 'city'] ?? '');
$state = trim($_POST[$prefix . 'state'] ?? '');
$postcode = trim($_POST[$prefix . 'postcode'] ?? '');
$contact_person = trim($_POST[$prefix . 'contact_person'] ?? '');
$mobile = trim($_POST[$prefix . 'mobile'] ?? '');
$phone = trim($_POST[$prefix . 'phone'] ?? '');
$email = trim($_POST[$prefix . 'email'] ?? '');
$pickup_instruction = $role === 'SENDER' ? trim($_POST[$prefix . 'pickup_instruction'] ?? '') : '';
$delivery_instruction = $role === 'RECEIVER' ? trim($_POST[$prefix . 'delivery_instruction'] ?? '') : '';
$signature_required = ($role === 'RECEIVER' && isset($_POST[$prefix . 'signature_required']) && $_POST[$prefix . 'signature_required']) ? 1 : 0;

if ($country_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select country']);
    exit;
}
if ($city === '') {
    echo json_encode(['success' => false, 'message' => 'City is required']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO addresses (country_id, name, building, street_no, street, suburb, find_address, city, state, postcode, contact_person, mobile, phone, email, pickup_instruction, signature_required, delivery_instruction)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('issssssssssssssis', $country_id, $name, $building, $street_no, $street, $suburb, $find_address, $city, $state, $postcode, $contact_person, $mobile, $phone, $email, $pickup_instruction, $signature_required, $delivery_instruction);
$stmt->execute();
$address_id = (int)$conn->insert_id;
$stmt->close();
if ($address_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Failed to save address']);
    exit;
}

// Upsert job_addresses: customer default (booking_id NULL, customer_id set) – one per customer per role
$conn->query("DELETE FROM job_addresses WHERE customer_id = $customer_id AND party_role = '$role' AND booking_id IS NULL");
$ins = $conn->prepare("INSERT INTO job_addresses (booking_id, customer_id, address_id, party_role, instructions, signature_required) VALUES (NULL, ?, ?, ?, ?, ?)");
$instructions = $role === 'SENDER' ? $pickup_instruction : $delivery_instruction;
$ins->bind_param('iissi', $customer_id, $address_id, $role, $instructions, $signature_required);
$ins->execute();
$ins->close();

$r = $conn->prepare("SELECT name, code FROM customers WHERE id = ?");
$r->bind_param('i', $customer_id);
$r->execute();
$res = $r->get_result();
$c = $res->fetch_assoc();
$r->close();
$label = $c['name'] . (!empty($c['code']) ? ' (' . $c['code'] . ')' : '');
$code = $c['code'] ?? '';

$conn->close();
echo json_encode(['success' => true, 'customer_id' => $customer_id, 'customer_label' => $label, 'customer_code' => $code]);
