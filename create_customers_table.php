<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `customers` (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(50),
  `email` VARCHAR(100),
  `phone` VARCHAR(50),
  `address` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table customers created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
