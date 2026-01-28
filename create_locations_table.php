<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    company_id INT NOT NULL,
    location_type_id INT NOT NULL,
    door_type_id INT NOT NULL,
    lift_type_id INT NOT NULL,
    country_id INT NOT NULL,
    location_code VARCHAR(50),
    building VARCHAR(150),
    street_no VARCHAR(20),
    street VARCHAR(150),
    suburb VARCHAR(150),
    city VARCHAR(150),
    state VARCHAR(150),
    postcode VARCHAR(20),
    contact_person VARCHAR(150),
    phone VARCHAR(30),
    mobile VARCHAR(30),
    email VARCHAR(150),
    send_tracking_email TINYINT(1) DEFAULT 0,
    special_instruction TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (location_type_id) REFERENCES location_types(id),
    FOREIGN KEY (door_type_id) REFERENCES door_types(id),
    FOREIGN KEY (lift_type_id) REFERENCES lift_types(id),
    FOREIGN KEY (country_id) REFERENCES countries(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table locations created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>