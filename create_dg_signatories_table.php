<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS dg_signatories");

// Create the table
$sql = "CREATE TABLE dg_signatories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    certificate_no VARCHAR(100),
    expiry_date DATE,
    status BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'dg_signatories' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
