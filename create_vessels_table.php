<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `vessels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `country_id` INT NOT NULL,
  `ship_type_id` INT NOT NULL,
  `imo_number` VARCHAR(20) UNIQUE,
  `mmsi` VARCHAR(20),
  `call_sign` VARCHAR(20),
  `built_year` YEAR,
  `length_m` DECIMAL(6,2),
  `width_m` DECIMAL(6,2),
  `draught_m` DECIMAL(5,2),
  `gross_tonnage` INT,
  `net_tonnage` INT,
  `dead_weight` INT,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CONSTRAINT `fk_vessel_country`
    FOREIGN KEY (`country_id`)
    REFERENCES `countries`(`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vessel_ship_type`
    FOREIGN KEY (`ship_type_id`)
    REFERENCES `ship_types`(`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  INDEX `idx_name` (`name`),
  INDEX `idx_mmsi` (`mmsi`),
  INDEX `idx_call_sign` (`call_sign`),
  INDEX `idx_country_id` (`country_id`),
  INDEX `idx_ship_type_id` (`ship_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "✅ Table vessels created successfully.";
} else {
    echo "❌ Error creating table: " . $conn->error;
}

$conn->close();
?>
