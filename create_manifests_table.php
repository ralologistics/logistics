<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `manifests` (
  `manifest_id` CHAR(36) PRIMARY KEY,
  `company_id` CHAR(36) NOT NULL,
  `customer_id` CHAR(36) DEFAULT NULL,
  `manifest_date` DATE NOT NULL,
  `manifest_type` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_manifest_date` (`manifest_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table manifests created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
