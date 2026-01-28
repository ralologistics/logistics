<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $service_name = trim($_POST['service_name'] ?? '');
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if (empty($service_name)) {
        header("Location: additional-services-form.php?error=Service name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE additional_services SET 
            service_name=?, 
            status=? 
            WHERE id=?");
        $stmt->bind_param("sii", 
            $service_name, 
            $status, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO additional_services (
            service_name, 
            status
        ) VALUES (?, ?)");
        $stmt->bind_param("si", 
            $service_name, 
            $status
        );
    }

    if ($stmt->execute()) {
        header("Location: additional-services-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='additional-services-form.php'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: additional-services-list.php");
    exit;
}

$conn->close();
?>
