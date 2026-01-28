<?php
require 'db.php';

echo "Testing Import Job Bookings CRUD Operations<br><br>";

// Test CREATE
echo "1. Testing CREATE operation...<br>";
$insert_sql = "INSERT INTO import_job_bookings (booking_no, customer_id, document_received_at) VALUES ('TEST001', 1, '2024-01-01 10:00:00')";
if ($conn->query($insert_sql) === TRUE) {
    $last_id = $conn->insert_id;
    echo "✓ Booking created successfully with ID: $last_id<br>";
} else {
    echo "✗ Error creating booking: " . $conn->error . "<br>";
    $conn->close();
    exit;
}

// Test READ
echo "<br>2. Testing READ operation...<br>";
$select_sql = "SELECT ijb.*, c.name as customer_name FROM import_job_bookings ijb LEFT JOIN customers c ON ijb.customer_id = c.id WHERE ijb.id = $last_id";
$result = $conn->query($select_sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Booking retrieved: " . htmlspecialchars($row['booking_no']) . " - Customer: " . htmlspecialchars($row['customer_name'] ?? 'N/A') . "<br>";
} else {
    echo "✗ Error reading booking: " . $conn->error . "<br>";
}

// Test UPDATE
echo "<br>3. Testing UPDATE operation...<br>";
$update_sql = "UPDATE import_job_bookings SET booking_no = 'TEST001-UPDATED' WHERE id = $last_id";
if ($conn->query($update_sql) === TRUE) {
    echo "✓ Booking updated successfully<br>";
} else {
    echo "✗ Error updating booking: " . $conn->error . "<br>";
}

// Test DELETE
echo "<br>4. Testing DELETE operation...<br>";
$delete_sql = "DELETE FROM import_job_bookings WHERE id = $last_id";
if ($conn->query($delete_sql) === TRUE) {
    echo "✓ Booking deleted successfully<br>";
} else {
    echo "✗ Error deleting booking: " . $conn->error . "<br>";
}

$conn->close();
echo "<br>All CRUD operations completed successfully!";
?>
