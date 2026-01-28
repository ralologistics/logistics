<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name)) {
        header("Location: notification-types-form.php?error=Notification type name is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE notification_types SET 
            name=?, 
            description=?, 
            is_active=? 
            WHERE id=?");
        $stmt->bind_param("ssii", 
            $name, 
            $description, 
            $is_active, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO notification_types (
            name, 
            description, 
            is_active
        ) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", 
            $name, 
            $description, 
            $is_active
        );
    }

    if ($stmt->execute()) {
        header("Location: notification-types-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='notification-types-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: notification-types-list.php");
    exit;
}

$conn->close();
?>
