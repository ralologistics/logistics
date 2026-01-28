<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `iso_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL,
  `description` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table iso_codes created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
