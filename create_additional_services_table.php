<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS additional_services");

// Create the table
$sql = "CREATE TABLE additional_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(150) NOT NULL,
    status TINYINT DEFAULT 1
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'additional_services' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
