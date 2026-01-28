<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $code = !empty($_POST['code']) ? trim($_POST['code']) : null;
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($name)) {
        header("Location: lift-type-form.php?error=Lift type name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE lift_types SET 
            name=?, 
            code=?, 
            description=?, 
            status=? 
            WHERE id=?");
        $stmt->bind_param("sssii", 
            $name, 
            $code, 
            $description, 
            $status, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO lift_types (
            name, 
            code, 
            description, 
            status
        ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", 
            $name, 
            $code, 
            $description, 
            $status
        );
    }

    if ($stmt->execute()) {
        header("Location: lift-type-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='lift-type-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}
?>