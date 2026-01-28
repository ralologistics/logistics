<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $type_name = trim($_POST['type_name'] ?? '');

    if (empty($type_name)) {
        header("Location: ship-type-form.php?error=Type name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE ship_types SET type_name = ? WHERE id = ?");
        $stmt->bind_param("si", $type_name, $id);
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO ship_types (type_name) VALUES (?)");
        $stmt->bind_param("s", $type_name);
    }

    if ($stmt->execute()) {
        header("Location: ship-type-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>
