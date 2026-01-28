<?php
session_start();
require "db.php"; // mysqli connection

/* ===============================
   1️⃣ COLLECT ALL VARIABLES FIRST
   =============================== */

// Logged-in Manager
$created_by = $_SESSION['user_id'];

// Customer details
$customer_name_code   = $_POST['customer_name_code'] ?? null;
$document_received_at = $_POST['document_received_at'] ?? null;

// Container details
$reference_no = $_POST['reference_no'] ?? null;
$container_no = $_POST['container_no'] ?? null;
$iso_code     = $_POST['iso_code'] ?? null;
$weight_kg    = $_POST['weight_kg'] ?? null;

// Locations
$from_location      = $_POST['from_location'] ?? null;
$to_location        = $_POST['to_location'] ?? null;
$return_to_location = $_POST['return_to_location'] ?? null;

// Additional container info
$customer_location_grid = $_POST['customer_location_grid'] ?? null;
$door_type              = $_POST['door_type'] ?? null;
$security_check         = $_POST['security_check'] ?? null;
$random_number          = $_POST['random_number'] ?? null;
$release_ecn_number     = $_POST['release_ecn_number'] ?? null;
$port_pin_no            = $_POST['port_pin_no'] ?? null;

// Dates
$available_date  = $_POST['available_date'] ?? null;
$vb_slot_date    = $_POST['vb_slot_date'] ?? null;
$demurrage_date  = $_POST['demurrage_date'] ?? null;

// Other details
$detention_days = $_POST['detention_days'] ?? 0;
$shipping       = $_POST['shipping'] ?? null;
$vessel         = $_POST['vessel'] ?? null;
$voyage         = $_POST['voyage'] ?? null;

// Checkboxes (default 0)
$is_xray    = isset($_POST['is_xray']) ? 1 : 0;
$is_dgs     = isset($_POST['is_dgs']) ? 1 : 0;
$is_live_ul = isset($_POST['is_live_ul']) ? 1 : 0;

$hold_sh      = isset($_POST['hold_sh']) ? 1 : 0;
$hold_customs = isset($_POST['hold_customs']) ? 1 : 0;
$hold_mpi     = isset($_POST['hold_mpi']) ? 1 : 0;

// Extra fields
$add_notes           = $_POST['add_notes'] ?? null;
$additional_services = $_POST['additional_services'] ?? null;

/* ===============================
   2️⃣ FILE UPLOAD HANDLING
   =============================== */

$document_upload_path = null;

if (!empty($_FILES['document_upload_path']['name'])) {
    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['document_upload_path']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['document_upload_path']['tmp_name'], $targetFile)) {
        $document_upload_path = $fileName;
    }
}

/* ===============================
   3️⃣ PREPARED INSERT QUERY
   =============================== */

$sql = "
INSERT INTO import_job_bookings (
    created_by,
    customer_name_code,
    document_received_at,
    document_upload_path,
    reference_no,
    container_no,
    iso_code,
    weight_kg,
    from_location,
    to_location,
    return_to_location,
    customer_location_grid,
    door_type,
    security_check,
    random_number,
    release_ecn_number,
    port_pin_no,
    available_date,
    vb_slot_date,
    demurrage_date,
    detention_days,
    shipping,
    vessel,
    voyage,
    is_xray,
    is_dgs,
    is_live_ul,
    hold_sh,
    hold_customs,
    hold_mpi,
    add_notes,
    additional_services
) VALUES (
    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

/* ===============================
   4️⃣ BIND & EXECUTE
   =============================== */

$stmt->bind_param(
    "issssssssssssssssssisssiiiiissss",
    $created_by,
    $customer_name_code,
    $document_received_at,
    $document_upload_path,
    $reference_no,
    $container_no,
    $iso_code,
    $weight_kg,
    $from_location,
    $to_location,
    $return_to_location,
    $customer_location_grid,
    $door_type,
    $security_check,
    $random_number,
    $release_ecn_number,
    $port_pin_no,
    $available_date,
    $vb_slot_date,
    $demurrage_date,
    $detention_days,
    $shipping,
    $vessel,
    $voyage,
    $is_xray,
    $is_dgs,
    $is_live_ul,
    $hold_sh,
    $hold_customs,
    $hold_mpi,
    $add_notes,
    $additional_services
);

$stmt->execute();

/* ===============================
   5️⃣ REDIRECT
   =============================== */

header("Location: job-queue.php?success=1");
exit;
