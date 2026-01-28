<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $import_job_id = (int)$_POST['import_job_id'];
    $file_path = trim($_POST['file_path']);

    // Validation
    if (empty($import_job_id) || empty($file_path)) {
        header("Location: import-job-documents-form.php?error=Required fields are missing" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    $conn->begin_transaction();
    try {
        if ($id) {
            // Update existing document
            $stmt = $conn->prepare("UPDATE import_job_documents SET
                import_job_id=?,
                file_path=?
                WHERE id=?");
            $stmt->bind_param("isi", $import_job_id, $file_path, $id);

            if (!$stmt->execute()) {
                throw new Exception("Document update failed: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // Insert new document
            $stmt = $conn->prepare("INSERT INTO import_job_documents (
                import_job_id,
                file_path
            ) VALUES (?, ?)");
            $stmt->bind_param("is", $import_job_id, $file_path);

            if (!$stmt->execute()) {
                throw new Exception("Document insert failed: " . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();
        header("Location: import-job-documents-list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<br><a href='import-job-documents-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    } finally {
        $conn->close();
    }
}
?>
