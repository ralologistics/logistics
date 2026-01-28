<?php
require 'db.php';
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM footer_settings WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: footer-settings-list.php?success=1");
    } else {
        header("Location: footer-settings-list.php?error=" . urlencode($stmt->error));
    }
    $stmt->close();
} else {
    header("Location: footer-settings-list.php");
}
$conn->close();
?>
