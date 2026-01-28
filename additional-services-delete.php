<?php
session_start();

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM additional_services WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: additional-services-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: additional-services-list.php");
    exit;
}

$conn->close();
?>
