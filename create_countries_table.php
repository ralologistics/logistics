<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `countries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `iso_alpha2` CHAR(2) UNIQUE,
  `iso_alpha3` CHAR(3) UNIQUE,
  `numeric_code` CHAR(3),
  `phone_code` VARCHAR(10),
  `currency` VARCHAR(10),
  `continent` VARCHAR(50),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  INDEX `idx_name` (`name`),
  INDEX `idx_iso_alpha2` (`iso_alpha2`),
  INDEX `idx_iso_alpha3` (`iso_alpha3`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table countries created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
