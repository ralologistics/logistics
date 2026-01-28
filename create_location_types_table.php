<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS location_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) UNIQUE,
    description TEXT,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'location_types' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>