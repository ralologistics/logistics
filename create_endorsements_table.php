<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS endorsements");

// Create the table
$sql = "CREATE TABLE endorsements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status TINYINT DEFAULT 1
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'endorsements' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
