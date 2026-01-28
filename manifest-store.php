<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $manifest_id = $_POST['manifest_id'] ?? null;
    $company_id = trim($_POST['company_id'] ?? '');
    $customer_id = !empty($_POST['customer_id']) ? trim($_POST['customer_id']) : null;
    $manifest_date = $_POST['manifest_date'] ?? '';
    $manifest_type = trim($_POST['manifest_type'] ?? '');

    if ($manifest_id) {
        // UPDATE
        if ($customer_id) {
            $stmt = $conn->prepare("UPDATE manifests SET 
                company_id=?, 
                customer_id=?, 
                manifest_date=?, 
                manifest_type=? 
                WHERE manifest_id=?");
            $stmt->bind_param("sssss", $company_id, $customer_id, $manifest_date, $manifest_type, $manifest_id);
        } else {
            $stmt = $conn->prepare("UPDATE manifests SET 
                company_id=?, 
                customer_id=NULL, 
                manifest_date=?, 
                manifest_type=? 
                WHERE manifest_id=?");
            $stmt->bind_param("ssss", $company_id, $manifest_date, $manifest_type, $manifest_id);
        }
    } else {
        // INSERT - Generate UUID for MySQL
        $new_manifest_id = bin2hex(random_bytes(16));
        $new_manifest_id = substr($new_manifest_id, 0, 8) . '-' . 
                          substr($new_manifest_id, 8, 4) . '-' . 
                          substr($new_manifest_id, 12, 4) . '-' . 
                          substr($new_manifest_id, 16, 4) . '-' . 
                          substr($new_manifest_id, 20, 12);
        
        if ($customer_id) {
            $stmt = $conn->prepare("INSERT INTO manifests (
                manifest_id, 
                company_id, 
                customer_id, 
                manifest_date, 
                manifest_type
            ) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $new_manifest_id, $company_id, $customer_id, $manifest_date, $manifest_type);
        } else {
            $stmt = $conn->prepare("INSERT INTO manifests (
                manifest_id, 
                company_id, 
                customer_id, 
                manifest_date, 
                manifest_type
            ) VALUES (?, ?, NULL, ?, ?)");
            $stmt->bind_param("ssss", $new_manifest_id, $company_id, $manifest_date, $manifest_type);
        }
    }

    if ($stmt->execute()) {
        header("Location: manifest-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>
