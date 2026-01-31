<?php
require 'db.php';

// 🔥 VERY IMPORTANT
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop dependent tables FIRST
$conn->query("DROP TABLE IF EXISTS job_tracking_notifications");

// Drop parent table
$conn->query("DROP TABLE IF EXISTS job_bookings");

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create job_bookings table
$sql = "
CREATE TABLE job_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    job_type ENUM('SWING','EXPORT','IMPORT','CART','GENERAL') NOT NULL,

    customer_id INT UNSIGNED NOT NULL,
    company_id INT UNSIGNED NOT NULL,

    customer_reference VARCHAR(255),
    receiver_reference VARCHAR(255),

    freight_ready_by DATETIME,
    job_number VARCHAR(100),

    status ENUM(
        'DRAFT','SUBMITTED','APPROVED','REJECTED',
        'COMPLETED','PENDING_TBA','ACTIVE','IN_PROCESS','URGENT'
    ) DEFAULT 'DRAFT',

    booking_id VARCHAR(255) NOT NULL UNIQUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_job_type (job_type),
    INDEX idx_status (status),
    INDEX idx_customer (customer_id),
    INDEX idx_company (company_id)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
";

if ($conn->query($sql)) {
    echo "✅ job_bookings table created successfully<br>";
} else {
    echo "❌ Error creating job_bookings: " . $conn->error;
}

$conn->close();
?>
