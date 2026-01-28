<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = !empty($_POST['name']) ? trim($_POST['name']) : null;
    $un_number = !empty($_POST['un_number']) ? trim($_POST['un_number']) : null;

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE dg_types SET 
            name=?, 
            un_number=? 
            WHERE id=?");
        $stmt->bind_param("ssi", 
            $name, 
            $un_number, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO dg_types (
            name, 
            un_number
        ) VALUES (?, ?)");
        $stmt->bind_param("ss", 
            $name, 
            $un_number
        );
    }

    if ($stmt->execute()) {
        header("Location: dg-types-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='dg-types-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: dg-types-list.php");
    exit;
}

$conn->close();
?>
