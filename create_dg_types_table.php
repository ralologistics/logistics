<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS dg_types");

// Create the table
$sql = "CREATE TABLE dg_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    un_number VARCHAR(50)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'dg_types' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
