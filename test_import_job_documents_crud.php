<?php
require 'db.php';

echo "Testing Import Job Documents CRUD Operations<br><br>";

// Test CREATE
echo "1. Testing CREATE operation...<br>";
$insert_sql = "INSERT INTO import_job_documents (import_job_id, file_path) VALUES (1, 'uploads/test_document.pdf')";
if ($conn->query($insert_sql) === TRUE) {
    $last_id = $conn->insert_id;
    echo "✓ Document created successfully with ID: $last_id<br>";
} else {
    echo "✗ Error creating document: " . $conn->error . "<br>";
    $conn->close();
    exit;
}

// Test READ
echo "<br>2. Testing READ operation...<br>";
$select_sql = "SELECT d.*, ijb.booking_no FROM import_job_documents d LEFT JOIN import_job_bookings ijb ON d.import_job_id = ijb.id WHERE d.id = $last_id";
$result = $conn->query($select_sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Document retrieved: " . htmlspecialchars($row['file_path']) . " - Import Job: " . htmlspecialchars($row['booking_no'] ?? 'N/A') . "<br>";
} else {
    echo "✗ Error reading document: " . $conn->error . "<br>";
}

// Test UPDATE
echo "<br>3. Testing UPDATE operation...<br>";
$update_sql = "UPDATE import_job_documents SET file_path = 'uploads/updated_document.pdf' WHERE id = $last_id";
if ($conn->query($update_sql) === TRUE) {
    echo "✓ Document updated successfully<br>";
} else {
    echo "✗ Error updating document: " . $conn->error . "<br>";
}

// Test DELETE
echo "<br>4. Testing DELETE operation...<br>";
$delete_sql = "DELETE FROM import_job_documents WHERE id = $last_id";
if ($conn->query($delete_sql) === TRUE) {
    echo "✓ Document deleted successfully<br>";
} else {
    echo "✗ Error deleting document: " . $conn->error . "<br>";
}

$conn->close();
echo "<br>All CRUD operations completed successfully!";
?>
