<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS import_job_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_job_id INT,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_job_id) REFERENCES import_job_bookings(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table import_job_documents created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
