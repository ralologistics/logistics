<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS package_types");

// Create the table
$sql = "CREATE TABLE package_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'package_types' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
