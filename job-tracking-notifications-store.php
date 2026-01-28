<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    // Handle BIGINT UNSIGNED for job_id
    $job_id = !empty($_POST['job_id']) ? filter_var($_POST['job_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : null;
    $communication_type = !empty($_POST['communication_type']) ? trim($_POST['communication_type']) : null;
    $contact = !empty($_POST['contact']) ? trim($_POST['contact']) : null;
    $notification_type_id = !empty($_POST['notification_type_id']) ? (int)$_POST['notification_type_id'] : null;

    // Valid ENUM values for communication_type
    $valid_communication_types = ['EMAIL', 'PHONE', 'SMS', 'WHATSAPP', 'PUSH'];

    if (empty($job_id) || $job_id === false) {
        header("Location: job-tracking-notifications-form.php?error=Job booking is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if (empty($communication_type) || !in_array($communication_type, $valid_communication_types)) {
        header("Location: job-tracking-notifications-form.php?error=Valid communication type is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if (empty($notification_type_id)) {
        header("Location: job-tracking-notifications-form.php?error=Notification type is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE job_tracking_notifications SET 
            job_id=?, 
            communication_type=?, 
            contact=?, 
            notification_type_id=? 
            WHERE id=?");
        // Using 's' for ENUM string, 'i' for BIGINT UNSIGNED and INT
        $stmt->bind_param("issii", 
            $job_id, 
            $communication_type, 
            $contact, 
            $notification_type_id, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO job_tracking_notifications (
            job_id, 
            communication_type, 
            contact, 
            notification_type_id
        ) VALUES (?, ?, ?, ?)");
        // Using 's' for ENUM string, 'i' for BIGINT UNSIGNED and INT
        $stmt->bind_param("issi", 
            $job_id, 
            $communication_type, 
            $contact, 
            $notification_type_id
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: job-tracking-notifications-list.php?success=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-tracking-notifications-form.php?error=" . urlencode("Error saving record: " . $error) . ($id ? "&id=" . (int)$id : ""));
        exit;
    }
} else {
    header("Location: job-tracking-notifications-list.php");
    exit;
}

$conn->close();
?>
