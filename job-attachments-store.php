<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    // Handle BIGINT UNSIGNED for booking_id
    $booking_id = !empty($_POST['booking_id']) ? filter_var($_POST['booking_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : null;
    
    // Validation
    if (empty($booking_id) || $booking_id === false) {
        header("Location: job-attachments-form.php?error=Job booking is required" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    $file_path = null;
    $old_file_path = null;

    // Get old file path if editing
    if ($id) {
        $stmt_check = $conn->prepare("SELECT file_path FROM job_attachments WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($row = $result_check->fetch_assoc()) {
            $old_file_path = $row['file_path'];
        }
        $stmt_check->close();
    }

    // Handle file upload
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'job_attachments';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $file_name = basename($_FILES['file_path']['name']);
        
        // Generate safe filename (preserve original extension)
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_base = pathinfo($file_name, PATHINFO_FILENAME);
        
        // Sanitize filename while preserving extension
        $safe_base = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_base);
        $safe_ext = preg_replace('/[^a-zA-Z0-9]/', '', $file_ext);
        $safe_name = time() . '_' . $safe_base . ($safe_ext ? '.' . $safe_ext : '');
        $target_file = $uploadDir . DIRECTORY_SEPARATOR . $safe_name;

        if (move_uploaded_file($_FILES['file_path']['tmp_name'], $target_file)) {
            $file_path = 'uploads/job_attachments/' . $safe_name;
            
            // Delete old file if exists and new file uploaded successfully
            if ($old_file_path && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $old_file_path)) {
                @unlink(__DIR__ . DIRECTORY_SEPARATOR . $old_file_path);
            }
        } else {
            header("Location: job-attachments-form.php?error=File upload failed" . ($id ? "&id=" . (int)$id : ""));
            exit;
        }
    } else {
        // If editing and no new file uploaded, keep old file
        if ($id && $old_file_path) {
            $file_path = $old_file_path;
        } else {
            // New record without file
            $file_path = null;
        }
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE job_attachments SET 
            booking_id=?, 
            file_path=? 
            WHERE id=?");
        $stmt->bind_param("isi", 
            $booking_id, 
            $file_path, 
            $id
        );
    } else {
        // INSERT
        if (empty($file_path)) {
            header("Location: job-attachments-form.php?error=File upload is required for new records");
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO job_attachments (
            booking_id, 
            file_path
        ) VALUES (?, ?)");
        $stmt->bind_param("is", 
            $booking_id, 
            $file_path
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: job-attachments-list.php?success=1");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: job-attachments-form.php?error=" . urlencode("Error saving record: " . $error) . ($id ? "&id=" . (int)$id : ""));
        exit;
    }
} else {
    $conn->close();
    header("Location: job-attachments-list.php");
    exit;
}
?>
