<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM swing_job_bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: swing-job-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: swing-job-list.php");
    exit;
}

$conn->close();
?>
