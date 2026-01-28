<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $job_type = trim($_POST['job_type']);
    $booking_id = trim($_POST['booking_id']);
    $endorsement_id = (int)$_POST['endorsement_id'];
    $note = !empty($_POST['note']) ? trim($_POST['note']) : null;

    if (empty($job_type) || empty($booking_id) || empty($endorsement_id)) {
        header("Location: import-job-notes-form.php?error=Job Type, Booking ID, and Endorsement are required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE import_job_notes SET 
            job_type=?, 
            booking_id=?,
            endorsement_id=?, 
            note=? 
            WHERE id=?");
        $stmt->bind_param("ssisi", 
            $job_type, 
            $booking_id,
            $endorsement_id, 
            $note, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO import_job_notes (
            job_type, 
            booking_id,
            endorsement_id, 
            note
        ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", 
            $job_type, 
            $booking_id,
            $endorsement_id, 
            $note
        );
    }

    if ($stmt->execute()) {
        header("Location: import-job-notes-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='import-job-notes-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}

?>
