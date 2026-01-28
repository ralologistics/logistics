<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // First, get the file_path before deleting
    $stmt = $conn->prepare("SELECT file_path FROM import_job_documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
    
    // Delete the database record
    $stmt = $conn->prepare("DELETE FROM import_job_documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // If file exists in uploads directory, try to delete it
        if ($document && !empty($document['file_path'])) {
            $filePath = (string)$document['file_path'];
            // Only delete if path starts with uploads/ (relative path)
            if (strpos($filePath, 'uploads/') === 0 || strpos($filePath, 'uploads\\') === 0) {
                $fullPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
        
        header("Location: import-job-documents-list.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: import-job-documents-list.php");
    exit;
}

$conn->close();
?>
