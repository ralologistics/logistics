<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS import_job_services");

// Create the table
$sql = "CREATE TABLE import_job_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('import','cart','export','swing') NOT NULL,
    service_id INT NOT NULL,
    container_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'import_job_services' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
