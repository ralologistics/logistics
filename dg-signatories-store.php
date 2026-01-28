<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = !empty($_POST['name']) ? trim($_POST['name']) : null;
    $certificate_no = !empty($_POST['certificate_no']) ? trim($_POST['certificate_no']) : null;
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    // Validation
    if (empty($name)) {
        header("Location: dg-signatories-form.php?error=Name is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    // Validate expiry date format if provided
    if ($expiry_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
        header("Location: dg-signatories-form.php?error=Invalid date format" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    // Convert empty string to NULL for expiry_date
    if ($expiry_date === '') {
        $expiry_date = null;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE dg_signatories SET 
            name=?, 
            certificate_no=?, 
            expiry_date=?, 
            status=? 
            WHERE id=?");
        $stmt->bind_param("sssii", 
            $name, 
            $certificate_no, 
            $expiry_date, 
            $status, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO dg_signatories (
            name, 
            certificate_no, 
            expiry_date, 
            status
        ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", 
            $name, 
            $certificate_no, 
            $expiry_date, 
            $status
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: dg-signatories-list.php?success=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: dg-signatories-form.php?error=" . urlencode("Error saving record: " . $error) . ($id ? "&id=" . (int)$id : ""));
        exit;
    }
} else {
    $conn->close();
    header("Location: dg-signatories-list.php");
    exit;
}
?>
