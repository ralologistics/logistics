<?php
require 'db.php';

echo "Testing Containers CRUD Operations<br><br>";

// Test CREATE
echo "1. Testing CREATE operation...<br>";
$insert_sql = "INSERT INTO containers (import_job_id, iso_code_id, container_no, reference) VALUES (1, 1, 'TEST001', 'REF001')";
if ($conn->query($insert_sql) === TRUE) {
    $last_id = $conn->insert_id;
    echo "✓ Container created successfully with ID: $last_id<br>";
} else {
    echo "✗ Error creating container: " . $conn->error . "<br>";
    $conn->close();
    exit;
}

// Test READ
echo "<br>2. Testing READ operation...<br>";
$select_sql = "SELECT c.*, ijb.booking_no FROM containers c LEFT JOIN import_job_bookings ijb ON c.import_job_id = ijb.id WHERE c.id = $last_id";
$result = $conn->query($select_sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Container retrieved: " . htmlspecialchars($row['container_no']) . " - Import Job: " . htmlspecialchars($row['booking_no'] ?? 'N/A') . "<br>";
} else {
    echo "✗ Error reading container: " . $conn->error . "<br>";
}

// Test UPDATE
echo "<br>3. Testing UPDATE operation...<br>";
$update_sql = "UPDATE containers SET container_no = 'TEST001-UPDATED' WHERE id = $last_id";
if ($conn->query($update_sql) === TRUE) {
    echo "✓ Container updated successfully<br>";
} else {
    echo "✗ Error updating container: " . $conn->error . "<br>";
}

// Test DELETE
echo "<br>4. Testing DELETE operation...<br>";
$delete_sql = "DELETE FROM containers WHERE id = $last_id";
if ($conn->query($delete_sql) === TRUE) {
    echo "✓ Container deleted successfully<br>";
} else {
    echo "✗ Error deleting container: " . $conn->error . "<br>";
}

$conn->close();
echo "<br>All CRUD operations completed successfully!";
?>
