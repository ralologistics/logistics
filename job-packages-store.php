<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    // Handle BIGINT UNSIGNED for booking_id
    $booking_id = !empty($_POST['booking_id']) ? filter_var($_POST['booking_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : null;
    $units = !empty($_POST['units']) ? (int)$_POST['units'] : null;
    $weight_kg = !empty($_POST['weight_kg']) ? filter_var($_POST['weight_kg'], FILTER_VALIDATE_FLOAT) : null;
    $length_cm = !empty($_POST['length_cm']) ? filter_var($_POST['length_cm'], FILTER_VALIDATE_FLOAT) : null;
    $width_cm = !empty($_POST['width_cm']) ? filter_var($_POST['width_cm'], FILTER_VALIDATE_FLOAT) : null;
    $height_cm = !empty($_POST['height_cm']) ? filter_var($_POST['height_cm'], FILTER_VALIDATE_FLOAT) : null;
    $cubic_m3 = !empty($_POST['cubic_m3']) ? filter_var($_POST['cubic_m3'], FILTER_VALIDATE_FLOAT) : null;
    $package_type_id = !empty($_POST['package_type_id']) ? (int)$_POST['package_type_id'] : null;
    $dg_type_id = !empty($_POST['dg_type_id']) ? (int)$_POST['dg_type_id'] : null;
    $remarks = !empty($_POST['remarks']) ? trim($_POST['remarks']) : null;

    // Validation
    if (empty($booking_id) || $booking_id === false) {
        header("Location: job-packages-form.php?error=Job booking is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    // Convert empty strings to NULL for optional fields
    if ($units === '') $units = null;
    if ($weight_kg === false || $weight_kg === '') $weight_kg = null;
    if ($length_cm === false || $length_cm === '') $length_cm = null;
    if ($width_cm === false || $width_cm === '') $width_cm = null;
    if ($height_cm === false || $height_cm === '') $height_cm = null;
    if ($cubic_m3 === false || $cubic_m3 === '') $cubic_m3 = null;
    if ($package_type_id === '') $package_type_id = null;
    if ($dg_type_id === '') $dg_type_id = null;
    if ($remarks === '') $remarks = null;

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE job_packages SET 
            booking_id=?, 
            units=?, 
            weight_kg=?, 
            length_cm=?, 
            width_cm=?, 
            height_cm=?, 
            cubic_m3=?, 
            package_type_id=?, 
            dg_type_id=?, 
            remarks=? 
            WHERE id=?");
        // Using 'i' for INT/BIGINT, 'd' for DECIMAL, 's' for VARCHAR
        $stmt->bind_param("iiddddiisi", 
            $booking_id, 
            $units, 
            $weight_kg, 
            $length_cm, 
            $width_cm, 
            $height_cm, 
            $cubic_m3, 
            $package_type_id, 
            $dg_type_id, 
            $remarks, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO job_packages (
            booking_id, 
            units, 
            weight_kg, 
            length_cm, 
            width_cm, 
            height_cm, 
            cubic_m3, 
            package_type_id, 
            dg_type_id, 
            remarks
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Using 'i' for INT/BIGINT, 'd' for DECIMAL, 's' for VARCHAR
        $stmt->bind_param("iiddddiiss", 
            $booking_id, 
            $units, 
            $weight_kg, 
            $length_cm, 
            $width_cm, 
            $height_cm, 
            $cubic_m3, 
            $package_type_id, 
            $dg_type_id, 
            $remarks
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: job-packages-list.php?success=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-packages-form.php?error=" . urlencode("Error saving record: " . $error) . ($id ? "&id=" . (int)$id : ""));
        exit;
    }
} else {
    $conn->close();
    header("Location: job-packages-list.php");
    exit;
}
?>
