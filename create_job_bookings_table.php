<?php
require 'db.php';

echo "Creating job_bookings table...<br>";
$drop_query = "DROP TABLE IF EXISTS job_bookings";
// CREATE TABLE job_bookings
$create_sql = "
CREATE TABLE IF NOT EXISTS job_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('SWING', 'EXPORT', 'IMPORT', 'CART', 'GENERAL') NOT NULL,
    customer_id INT,
    company_id INT,
    customer_reference VARCHAR(255),
    receiver_reference VARCHAR(255),
    freight_ready_by DATETIME,
    trip_code VARCHAR(100),
    job_number VARCHAR(100),
    sender_address_id INT,
    receiver_address_id INT,
    pickup_instruction TEXT,
    delivery_instruction TEXT,
    signature_required TINYINT DEFAULT 0,
    status ENUM('DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'COMPLETED', 'PENDING_TBA', 'ACTIVE', 'IN_PROCESS', 'URGENT') DEFAULT 'DRAFT',
    booking_id VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (sender_address_id) REFERENCES job_addresses(id),
    FOREIGN KEY (receiver_address_id) REFERENCES job_addresses(id),
    INDEX idx_status (status),
    INDEX idx_job_type (job_type),
    INDEX idx_customer_id (customer_id),
    INDEX idx_company_id (company_id),
    INDEX idx_sender_address_id (sender_address_id),
    INDEX idx_receiver_address_id (receiver_address_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
)";

if ($conn->query($create_sql) === TRUE) {
    echo "Table job_bookings created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
