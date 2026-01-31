<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS notification_types");

// Create the table
$sql = "CREATE TABLE notification_types (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'notification_types' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
