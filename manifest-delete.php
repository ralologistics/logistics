<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM manifests WHERE manifest_id = ?");
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        header("Location: manifest-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: manifest-list.php");
    exit;
}

$conn->close();
?>
