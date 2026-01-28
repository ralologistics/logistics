<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS job_additional_information");

// Create the table
$sql = "CREATE TABLE job_additional_information (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    insurance_type ENUM(
        'Owners Risk', 
        'Carriers Risk', 
        'All Risk', 
        'Total Loss Only', 
        'Third Party', 
        'Limited Carrier Liability'
    ) DEFAULT 'Owners Risk',
    dg_signatory_id BIGINT UNSIGNED NULL,
    customer_reference_2 VARCHAR(255) NULL,
    receiver_reference_2 VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES job_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (dg_signatory_id) REFERENCES dg_signatories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'job_additional_information' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
