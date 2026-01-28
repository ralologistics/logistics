<?php
require 'db.php';

echo "Checking import_job_bookings table...<br><br>";

// Check if import_job_bookings table exists
$result = $conn->query("SHOW TABLES LIKE 'import_job_bookings'");
if ($result->num_rows == 0) {
    echo "Error: import_job_bookings table does not exist!<br>";
    $conn->close();
    exit;
}

// Describe import_job_bookings table
echo "Import Job Bookings table structure:<br>";
$result = $conn->query("DESCRIBE import_job_bookings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " " . $row['Type'] . " " . ($row['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . " " . ($row['Key'] == 'PRI' ? 'PRIMARY KEY' : '') . "<br>";
    }
} else {
    echo "Error describing import_job_bookings table: " . $conn->error . "<br>";
}

// Check if there are any records
$result = $conn->query("SELECT COUNT(*) as count FROM import_job_bookings");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<br>Number of import job bookings: " . $row['count'] . "<br>";
} else {
    echo "Error counting import job bookings: " . $conn->error . "<br>";
}

// If no records, create a test record
if ($row['count'] == 0) {
    echo "<br>No import job bookings found. Creating a test record...<br>";
    $insert_sql = "INSERT INTO import_job_bookings (booking_no, customer_id, document_received_at) VALUES ('TEST001', 1, '2024-01-01 10:00:00')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "Test import job booking created successfully with ID: " . $conn->insert_id . "<br>";
    } else {
        echo "Error creating test record: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
