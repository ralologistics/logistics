<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name)) {
        header("Location: package-types-form.php?error=Package type name is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE package_types SET 
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
        $stmt = $conn->prepare("INSERT INTO package_types (
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
        header("Location: package-types-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='package-types-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: package-types-list.php");
    exit;
}

$conn->close();
?>
