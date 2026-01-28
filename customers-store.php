<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $code = !empty($_POST['code']) ? trim($_POST['code']) : null;
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $document_received_at = trim($_POST['document_received_at'] ?? '');
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $address = !empty($_POST['address']) ? trim($_POST['address']) : null;

    if (empty($name)) {
        header("Location: customers-form.php?error=Customer name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE customers SET 
            name=?, 
            code=?, 
            email=?, 
            phone=?, 
            address=? 
            WHERE id=?");
        $stmt->bind_param("sssssi", 
            $name, 
            $code, 
            $email, 
            $phone, 
            $address, 
            $id
        );
    } else {
        // INSERT - Auto-increment ID
        $stmt = $conn->prepare("INSERT INTO customers (
            name,
            code,
            customer_id,
            document_received_at,
            email,
            phone,
            address
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss",
            $name,
            $code,
            $customer_id,
            $document_received_at,
            $email,
            $phone,
            $address
        );
    }

    if ($stmt->execute()) {
        // Get the inserted ID for new records
        if (!$id) {
            $new_id = $conn->insert_id;
        }
        header("Location: customers-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='customers-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}

?>
