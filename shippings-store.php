<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $code = !empty($_POST['code']) ? trim($_POST['code']) : null;
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($name)) {
        header("Location: shippings-form.php?error=Shipping name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE shippings SET 
            name=?, 
            code=?, 
            status=? 
            WHERE id=?");
        $stmt->bind_param("ssii", 
            $name, 
            $code, 
            $status, 
            $id
        );
    } else {
        // INSERT - Auto-increment ID
        $stmt = $conn->prepare("INSERT INTO shippings (
            name, 
            code, 
            status
        ) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", 
            $name, 
            $code, 
            $status
        );
    }

    if ($stmt->execute()) {
        // Get the inserted ID for new records
        if (!$id) {
            $new_id = $conn->insert_id;
        }
        header("Location: shippings-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='shippings-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}

?>
