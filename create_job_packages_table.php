<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS job_packages");

// Create the table
$sql = "CREATE TABLE job_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    units INT,
    weight_kg DECIMAL(10,2),
    length_cm DECIMAL(10,2),
    width_cm DECIMAL(10,2),
    height_cm DECIMAL(10,2),
    cubic_m3 DECIMAL(10,3),
    package_type_id INT,
    dg_type_id INT,
    remarks VARCHAR(255),
    FOREIGN KEY (booking_id) REFERENCES job_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (package_type_id) REFERENCES package_types(id) ON DELETE SET NULL,
    FOREIGN KEY (dg_type_id) REFERENCES dg_types(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'job_packages' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
