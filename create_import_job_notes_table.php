<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS import_job_notes");

// Create the table
$sql = "CREATE TABLE import_job_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('import','cart','export','swing') NOT NULL,
    booking_id VARCHAR(255) NOT NULL,
    endorsement_id INT NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id)
        REFERENCES job_bookings(booking_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table 'import_job_notes' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
