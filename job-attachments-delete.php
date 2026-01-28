<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get file path before deleting
    $stmt_file = $conn->prepare("SELECT file_path FROM job_attachments WHERE id = ?");
    $stmt_file->bind_param("i", $id);
    $stmt_file->execute();
    $result_file = $stmt_file->get_result();
    $file_path = null;
    if ($row = $result_file->fetch_assoc()) {
        $file_path = $row['file_path'];
    }
    $stmt_file->close();
    
    // Verify record exists before deleting
    $check_stmt = $conn->prepare("SELECT id FROM job_attachments WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        $check_stmt->close();
        $conn->close();
        header("Location: job-attachments-list.php?error=Record not found");
        exit;
    }
    $check_stmt->close();
    
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM job_attachments WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete physical file if exists
        if ($file_path && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $file_path)) {
            @unlink(__DIR__ . DIRECTORY_SEPARATOR . $file_path);
        }
        
        $stmt->close();
        $conn->close();
        header("Location: job-attachments-list.php?deleted=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-attachments-list.php?error=" . urlencode("Error deleting record: " . $error));
        exit;
    }
} else {
    $conn->close();
    header("Location: job-attachments-list.php");
    exit;
}
?>
