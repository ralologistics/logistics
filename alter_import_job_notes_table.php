<?php
require 'db.php';

// Add booking_id column to import_job_notes table
$alter_sql = "ALTER TABLE `import_job_notes` 
    ADD COLUMN `booking_id` VARCHAR(255) NOT NULL AFTER `import_job_booking_id`";

if ($conn->query($alter_sql) === TRUE) {
    echo "Column booking_id added successfully.<br>";
} else {
    echo "Error adding column: " . $conn->error . "<br>";
}

// Add foreign key constraint
$fk_sql = "ALTER TABLE `import_job_notes` 
    ADD CONSTRAINT `fk_import_job_notes_booking_id` 
    FOREIGN KEY (`booking_id`) 
    REFERENCES `job_bookings`(`booking_id`) 
    ON DELETE CASCADE";

if ($conn->query($fk_sql) === TRUE) {
    echo "Foreign key constraint added successfully.<br>";
} else {
    echo "Warning: Could not add foreign key constraint: " . $conn->error . ". The column was added but foreign key may need to be added manually.<br>";
}

$conn->close();
?>
