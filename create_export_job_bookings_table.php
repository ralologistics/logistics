<?php
require 'db.php';

echo "Dropping export_job_bookings table if exists...<br>";
$drop_query = "DROP TABLE IF EXISTS export_job_bookings";

if ($conn->query($drop_query) === TRUE) {
    echo "Table export_job_bookings dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

echo "Creating export_job_bookings table...<br>";
$create_sql = "
CREATE TABLE export_job_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NOT NULL,
    job_no VARCHAR(50) UNIQUE,
    customer_id BIGINT UNSIGNED NOT NULL,
    shipping__id INT,
    vessel_id INT NOT NULL,
    voyage VARCHAR(100),
    from_location VARCHAR(255) NOT NULL,
    to_location VARCHAR(255) NOT NULL,
    document_received_at DATETIME NOT NULL,
    FOREIGN KEY (shipping__id) REFERENCES shippings(id),
    FOREIGN KEY (booking_id) REFERENCES job_bookings(id),
    FOREIGN KEY (vessel_id) REFERENCES vessels(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";

if ($conn->query($create_sql) === TRUE) {
    echo "Table export_job_bookings created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
