<?php
require 'db.php';

$conn->query("SET FOREIGN_KEY_CHECKS = 0");
// DROP and CREATE TABLE import_job_bookings
$drop_sql = "DROP TABLE IF EXISTS import_job_bookings";
$create_sql = "
CREATE TABLE import_job_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    job_no VARCHAR(50) UNIQUE,
    document_received_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
";

if ($conn->query($drop_sql) === TRUE) {
    echo "Table import_job_bookings dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

if ($conn->query($create_sql) === TRUE) {
    echo "Table import_job_bookings created successfully.<br>";
    // Try to add foreign key
    $alter_sql = "ALTER TABLE import_job_bookings ADD CONSTRAINT fk_customer_id FOREIGN KEY (customer_id) REFERENCES customers(id)";
    if ($conn->query($alter_sql) === TRUE) {
        echo "Foreign key constraint added successfully.";
    } else {
        echo "Warning: Could not add foreign key constraint: " . $conn->error . ". CRUD operations will still work.";
    }
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$conn->close();
?>
