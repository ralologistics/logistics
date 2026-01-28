<?php
require 'db.php';

// Create footer_settings table
$sql = "CREATE TABLE IF NOT EXISTS footer_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255) NOT NULL,
    copyright_start_year YEAR NOT NULL,
    version VARCHAR(20) NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table footer_settings created successfully!";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
