<?php
require 'db.php';

// Drop the old table and recreate with new structure
$drop_query = "DROP TABLE IF EXISTS companies";
if ($conn->query($drop_query) === TRUE) {
    echo "Old companies table dropped.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create the new companies table
$sql = "CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_code VARCHAR(50) UNIQUE,
    name VARCHAR(150) NOT NULL,
    legal_name VARCHAR(200),
    email VARCHAR(150),
    phone VARCHAR(50),
    mobile VARCHAR(50),
    website VARCHAR(150),
    country_id INT,
    state VARCHAR(100),
    city VARCHAR(100),
    postcode VARCHAR(20),
    address TEXT,
    logo VARCHAR(255),
    timezone VARCHAR(50) DEFAULT 'Pacific/Auckland',
    currency VARCHAR(10) DEFAULT 'NZD',
    status TINYINT DEFAULT 1,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "New companies table created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?></content>
<parameter name="filePath">c:\xampp\htdocs\ralo\alter_companies_table.php