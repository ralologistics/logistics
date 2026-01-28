<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM containers WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: containers-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
        echo "<br><a href='containers-list.php'>Go Back</a>";
    }
    
    $stmt->close();
} else {
    header("Location: containers-list.php");
    exit;
}

$conn->close();
?>
