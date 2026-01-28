<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        header("Location: door-type-form.php?error=Name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE door_types SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO door_types (name) VALUES (?)");
        $stmt->bind_param("s", $name);
    }

    if ($stmt->execute()) {
        header("Location: door-type-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>
