<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `shippings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(50),
  `status` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table shippings created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
