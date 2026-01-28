<?php
require 'db.php';

echo "Checking job_bookings table structure...<br><br>";

// Check if job_bookings table exists
$result = $conn->query("SHOW TABLES LIKE 'job_bookings'");
if ($result->num_rows == 0) {
    echo "Error: job_bookings table does not exist!<br>";
    $conn->close();
    exit;
}

// Describe job_bookings table
echo "Job_bookings table structure:<br>";
$result = $conn->query("DESCRIBE job_bookings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " " . $row['Type'] . " " . ($row['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . " " . ($row['Key'] == 'PRI' ? 'PRIMARY KEY' : '') . "<br>";
    }
} else {
    echo "Error describing job_bookings table: " . $conn->error . "<br>";
}

// Check if there are any records
$result = $conn->query("SELECT COUNT(*) as count FROM job_bookings");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<br>Number of job_bookings: " . $row['count'] . "<br>";
} else {
    echo "Error counting job_bookings: " . $conn->error . "<br>";
}

$conn->close();
?>
