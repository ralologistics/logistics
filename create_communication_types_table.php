<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS communication_types");

// Create the table
$sql = "CREATE TABLE communication_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    requires_contact TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'communication_types' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
