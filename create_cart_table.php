<?php
require 'db.php';

$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop child table first
$conn->query("DROP TABLE IF EXISTS cart_job_bookings");

// Create table with EXACT matching types
$sql = "
CREATE TABLE cart_job_bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_no VARCHAR(50) UNIQUE,
    customer_id BIGINT UNSIGNED NOT NULL,
    job_booking_id BIGINT UNSIGNED NOT NULL,
    from_location VARCHAR(255) NOT NULL,
    to_location VARCHAR(255) NOT NULL,
    document_received_at DATETIME NOT NULL,

    INDEX idx_customer_id (customer_id),
    INDEX idx_job_booking_id (job_booking_id),

    CONSTRAINT fk_cart_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_cart_job
        FOREIGN KEY (job_booking_id)
        REFERENCES job_bookings(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if ($conn->query($sql)) {
    echo "✅ cart_job_bookings table created successfully.";
} else {
    echo "❌ Error creating table: " . $conn->error;
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$conn->close();
?>
