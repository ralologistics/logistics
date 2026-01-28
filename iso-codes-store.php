<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $code = trim($_POST['code'] ?? '');
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;

    if (empty($code)) {
        header("Location: iso-codes-form.php?error=Code is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE iso_codes SET 
            code=?, 
            description=? 
            WHERE id=?");
        $stmt->bind_param("ssi", 
            $code, 
            $description, 
            $id
        );
    } else {
        // INSERT - Auto-increment ID
        $stmt = $conn->prepare("INSERT INTO iso_codes (
            code, 
            description
        ) VALUES (?, ?)");
        $stmt->bind_param("ss", 
            $code, 
            $description
        );
    }

    if ($stmt->execute()) {
        // Get the inserted ID for new records
        if (!$id) {
            $new_id = $conn->insert_id;
        }
        header("Location: iso-codes-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='iso-codes-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}

?>
