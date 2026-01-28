<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $code = !empty($_POST['code']) ? trim($_POST['code']) : null;
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $status = isset($_POST['status']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if (empty($name)) {
        header("Location: location-type-form.php?error=Location type name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE location_types SET 
            name=?, 
            code=?, 
            description=?, 
            status=?, 
            sort_order=? 
            WHERE id=?");
        $stmt->bind_param("sssiii", 
            $name, 
            $code, 
            $description, 
            $status, 
            $sort_order, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO location_types (
            name, 
            code, 
            description, 
            status, 
            sort_order
        ) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", 
            $name, 
            $code, 
            $description, 
            $status, 
            $sort_order
        );
    }

    if ($stmt->execute()) {
        header("Location: location-type-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='location-type-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}
?>