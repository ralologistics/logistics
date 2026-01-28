<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Verify record exists before deleting
    $check_stmt = $conn->prepare("SELECT id FROM job_additional_information WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        $check_stmt->close();
        $conn->close();
        header("Location: job-additional-information-list.php?error=Record not found");
        exit;
    }
    $check_stmt->close();
    
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM job_additional_information WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: job-additional-information-list.php?deleted=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-additional-information-list.php?error=" . urlencode("Error deleting record: " . $error));
        exit;
    }
} else {
    $conn->close();
    header("Location: job-additional-information-list.php");
    exit;
}
?>
