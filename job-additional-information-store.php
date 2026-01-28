<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    // Handle BIGINT UNSIGNED for booking_id
    $booking_id = !empty($_POST['booking_id']) ? filter_var($_POST['booking_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : null;
    $insurance_type = !empty($_POST['insurance_type']) ? trim($_POST['insurance_type']) : 'Owners Risk';
    $dg_signatory_id = !empty($_POST['dg_signatory_id']) ? filter_var($_POST['dg_signatory_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : null;
    $customer_reference_2 = !empty($_POST['customer_reference_2']) ? trim($_POST['customer_reference_2']) : null;
    $receiver_reference_2 = !empty($_POST['receiver_reference_2']) ? trim($_POST['receiver_reference_2']) : null;

    // Valid ENUM values for insurance_type
    $valid_insurance_types = [
        'Owners Risk',
        'Carriers Risk',
        'All Risk',
        'Total Loss Only',
        'Third Party',
        'Limited Carrier Liability'
    ];

    // Validation
    if (empty($booking_id) || $booking_id === false) {
        header("Location: job-additional-information-form.php?error=Job booking is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if (!in_array($insurance_type, $valid_insurance_types)) {
        header("Location: job-additional-information-form.php?error=Invalid insurance type" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    // Convert empty string to NULL for optional fields
    if ($dg_signatory_id === false || $dg_signatory_id === '') {
        $dg_signatory_id = null;
    }
    if ($customer_reference_2 === '') {
        $customer_reference_2 = null;
    }
    if ($receiver_reference_2 === '') {
        $receiver_reference_2 = null;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE job_additional_information SET 
            booking_id=?, 
            insurance_type=?, 
            dg_signatory_id=?, 
            customer_reference_2=?, 
            receiver_reference_2=? 
            WHERE id=?");
        // Using 'i' for BIGINT UNSIGNED, 's' for ENUM/VARCHAR
        $stmt->bind_param("isissi", 
            $booking_id, 
            $insurance_type, 
            $dg_signatory_id, 
            $customer_reference_2, 
            $receiver_reference_2, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO job_additional_information (
            booking_id, 
            insurance_type, 
            dg_signatory_id, 
            customer_reference_2, 
            receiver_reference_2
        ) VALUES (?, ?, ?, ?, ?)");
        // Using 'i' for BIGINT UNSIGNED, 's' for ENUM/VARCHAR
        $stmt->bind_param("isiss", 
            $booking_id, 
            $insurance_type, 
            $dg_signatory_id, 
            $customer_reference_2, 
            $receiver_reference_2
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: job-additional-information-list.php?success=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-additional-information-form.php?error=" . urlencode("Error saving record: " . $error) . ($id ? "&id=" . (int)$id : ""));
        exit;
    }
} else {
    $conn->close();
    header("Location: job-additional-information-list.php");
    exit;
}
?>
