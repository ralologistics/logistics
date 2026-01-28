<?php
require 'db.php';

echo "<h2>Adding booking_id column to containers table</h2><br>";

// Check if table exists
$table_check = $conn->query("SHOW TABLES LIKE 'containers'");
if ($table_check->num_rows == 0) {
    echo "ERROR: containers table does not exist!<br>";
    echo "Please create the table first using create_containers_table.php<br>";
    $conn->close();
    exit;
}

// Check if column already exists
$check_sql = "SHOW COLUMNS FROM `containers` LIKE 'booking_id'";
$result = $conn->query($check_sql);

if ($result && $result->num_rows > 0) {
    echo "✓ Column booking_id already exists. No changes needed.<br>";
} else {
    echo "Adding booking_id column...<br>";
    
    // Check if there's existing data
    $count_result = $conn->query("SELECT COUNT(*) as cnt FROM containers");
    $count_row = $count_result->fetch_assoc();
    $has_data = $count_row['cnt'] > 0;
    
    if ($has_data) {
        echo "Warning: Table has " . $count_row['cnt'] . " existing records.<br>";
        echo "Adding column as nullable first...<br>";
        
        // Add as nullable first
        $alter_sql = "ALTER TABLE `containers` 
            ADD COLUMN `booking_id` VARCHAR(255) NULL AFTER `import_job_id`";
        
        if ($conn->query($alter_sql) === TRUE) {
            echo "✓ Column booking_id added (nullable).<br>";
            echo "Please update existing rows with booking_id values, then run this script again to make it NOT NULL.<br>";
        } else {
            echo "✗ Error adding column: " . $conn->error . "<br>";
            $conn->close();
            exit;
        }
    } else {
        // No existing data, can add as NOT NULL directly
        $alter_sql = "ALTER TABLE `containers` 
            ADD COLUMN `booking_id` VARCHAR(255) NOT NULL AFTER `import_job_id`";
        
        if ($conn->query($alter_sql) === TRUE) {
            echo "✓ Column booking_id added successfully (NOT NULL).<br>";
        } else {
            echo "✗ Error adding column: " . $conn->error . "<br>";
            $conn->close();
            exit;
        }
    }
}

// Try to add foreign key constraint (only if column is NOT NULL or has no NULL values)
$check_null = $conn->query("SELECT COUNT(*) as cnt FROM containers WHERE booking_id IS NULL");
$null_row = $check_null->fetch_assoc();
$has_nulls = $null_row['cnt'] > 0;

if ($has_nulls) {
    echo "<br>Warning: There are " . $null_row['cnt'] . " rows with NULL booking_id.<br>";
    echo "Foreign key constraint cannot be added until all booking_id values are set.<br>";
} else {
    // Check if foreign key already exists
    $fk_check_sql = "SELECT CONSTRAINT_NAME 
                     FROM information_schema.TABLE_CONSTRAINTS 
                     WHERE TABLE_SCHEMA = DATABASE() 
                     AND TABLE_NAME = 'containers' 
                     AND CONSTRAINT_NAME = 'fk_containers_booking_id'";
    $fk_result = $conn->query($fk_check_sql);
    
    if ($fk_result && $fk_result->num_rows > 0) {
        echo "<br>✓ Foreign key constraint already exists.<br>";
    } else {
        echo "<br>Adding foreign key constraint...<br>";
        $fk_sql = "ALTER TABLE `containers` 
            ADD CONSTRAINT `fk_containers_booking_id` 
            FOREIGN KEY (`booking_id`) 
            REFERENCES `job_bookings`(`booking_id`) 
            ON DELETE CASCADE";
        
        if ($conn->query($fk_sql) === TRUE) {
            echo "✓ Foreign key constraint added successfully.<br>";
        } else {
            echo "✗ Warning: Could not add foreign key constraint: " . $conn->error . "<br>";
            echo "Make sure all booking_id values in containers exist in job_bookings table.<br>";
        }
    }
}

echo "<br><br>Done!<br>";

$conn->close();
?>
