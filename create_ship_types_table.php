<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `ship_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table ship_types created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
