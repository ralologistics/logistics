<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS addresses");

// Create the table
$sql = "CREATE TABLE addresses (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    building VARCHAR(255),
    street_no VARCHAR(50),
    street VARCHAR(255),
    suburb VARCHAR(255),
    find_address TEXT,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postcode VARCHAR(20),
    contact_person VARCHAR(255),
    mobile VARCHAR(30),
    phone VARCHAR(30),
    email VARCHAR(255),
    pickup_instruction TEXT,
    signature_required TINYINT DEFAULT 0,
    delivery_instruction TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'addresses' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>