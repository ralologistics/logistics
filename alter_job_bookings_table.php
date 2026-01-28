<?php
require 'db.php';

echo "Altering job_bookings table to add booking_id field...<br>";

// ALTER TABLE to add booking_id column
$alter_sql = "ALTER TABLE job_bookings ADD COLUMN booking_id VARCHAR(255) UNIQUE NOT NULL AFTER id";

if ($conn->query($alter_sql) === TRUE) {
    echo "Column booking_id added successfully to job_bookings table.<br>";
} else {
    echo "Error adding column: " . $conn->error . "<br>";
}

$conn->close();
?>
