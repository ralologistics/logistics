<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $requires_contact = isset($_POST['requires_contact']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name)) {
        header("Location: communication-types-form.php?error=Communication type name is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE communication_types SET 
            name=?, 
            requires_contact=?, 
            is_active=? 
            WHERE id=?");
        $stmt->bind_param("siii", 
            $name, 
            $requires_contact, 
            $is_active, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO communication_types (
            name, 
            requires_contact, 
            is_active
        ) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", 
            $name, 
            $requires_contact, 
            $is_active
        );
    }

    if ($stmt->execute()) {
        header("Location: communication-types-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='communication-types-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: communication-types-list.php");
    exit;
}

$conn->close();
?>
