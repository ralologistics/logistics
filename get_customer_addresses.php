<?php
session_start();
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
if ($customer_id <= 0) {
    echo json_encode(['sender' => null, 'receiver' => null, 'customer' => null]);
    exit;
}

// Customer basic info
$cust = null;
$r = $conn->prepare("SELECT id, name, code, email, phone FROM customers WHERE id = ?");
$r->bind_param('i', $customer_id);
$r->execute();
$res = $r->get_result();
if ($row = $res->fetch_assoc()) $cust = ['id' => (int)$row['id'], 'name' => $row['name'], 'code' => $row['code'] ?? '', 'email' => $row['email'] ?? '', 'phone' => $row['phone'] ?? ''];
$r->close();
if (!$cust) {
    echo json_encode(['sender' => null, 'receiver' => null, 'customer' => null]);
    exit;
}

// Saved sender/receiver from job_addresses (booking_id NULL = customer default) + addresses
$sender = null;
$receiver = null;
$stmt = $conn->prepare("
    SELECT a.country_id, a.name, a.building, a.street_no, a.street, a.suburb, a.find_address, a.city, a.state, a.postcode,
           a.contact_person, a.mobile, a.phone, a.email, a.pickup_instruction, a.signature_required, a.delivery_instruction,
           ja.party_role
    FROM job_addresses ja
    INNER JOIN addresses a ON a.id = ja.address_id
    WHERE ja.customer_id = ? AND ja.booking_id IS NULL
");
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $addr = [
        'country_id' => (int)$row['country_id'],
        'name' => $row['name'],
        'building' => $row['building'] ?? '',
        'street_no' => $row['street_no'] ?? '',
        'street' => $row['street'] ?? '',
        'suburb' => $row['suburb'] ?? '',
        'find_address' => $row['find_address'] ?? '',
        'city' => $row['city'] ?? '',
        'state' => $row['state'] ?? '',
        'postcode' => $row['postcode'] ?? '',
        'contact_person' => $row['contact_person'] ?? '',
        'mobile' => $row['mobile'] ?? '',
        'phone' => $row['phone'] ?? '',
        'email' => $row['email'] ?? '',
        'pickup_instruction' => $row['pickup_instruction'] ?? '',
        'signature_required' => (int)$row['signature_required'],
        'delivery_instruction' => $row['delivery_instruction'] ?? ''
    ];
    if ($row['party_role'] === 'SENDER') $sender = $addr;
    else $receiver = $addr;
}
$stmt->close();

// If no saved addresses, use customer name/email/phone for both
if (!$sender) $sender = ['country_id' => 0, 'name' => $cust['name'], 'building' => '', 'street_no' => '', 'street' => '', 'suburb' => '', 'find_address' => '', 'city' => '', 'state' => '', 'postcode' => '', 'contact_person' => '', 'mobile' => $cust['phone'] ?? '', 'phone' => $cust['phone'] ?? '', 'email' => $cust['email'] ?? '', 'pickup_instruction' => '', 'signature_required' => 0, 'delivery_instruction' => ''];
if (!$receiver) $receiver = ['country_id' => 0, 'name' => $cust['name'], 'building' => '', 'street_no' => '', 'street' => '', 'suburb' => '', 'find_address' => '', 'city' => '', 'state' => '', 'postcode' => '', 'contact_person' => '', 'mobile' => $cust['phone'] ?? '', 'phone' => $cust['phone'] ?? '', 'email' => $cust['email'] ?? '', 'pickup_instruction' => '', 'signature_required' => 0, 'delivery_instruction' => ''];

// Last job booking for this customer – company, customer_reference, receiver_reference auto-fill
$last_booking = null;
$lb = $conn->prepare("SELECT jb.company_id, jb.customer_reference, jb.receiver_reference, co.name AS company_name FROM job_bookings jb LEFT JOIN companies co ON co.id = jb.company_id WHERE jb.customer_id = ? ORDER BY jb.id DESC LIMIT 1");
$lb->bind_param('i', $customer_id);
$lb->execute();
$lbRes = $lb->get_result();
if ($row = $lbRes->fetch_assoc()) {
    $last_booking = [
        'company_id' => (int)($row['company_id'] ?? 0),
        'company_name' => $row['company_name'] ?? '',
        'customer_reference' => $row['customer_reference'] ?? '',
        'receiver_reference' => $row['receiver_reference'] ?? ''
    ];
}
$lb->close();

$conn->close();
echo json_encode(['sender' => $sender, 'receiver' => $receiver, 'customer' => $cust, 'last_booking' => $last_booking]);
