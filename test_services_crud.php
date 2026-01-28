<?php
require 'db.php';

// Test INSERT
echo "Testing INSERT...<br>";
$name = "Test Service";
$description = "This is a test service";
$status = 1;

$stmt = $conn->prepare("INSERT INTO services (name, description, status) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $name, $description, $status);
if ($stmt->execute()) {
    $insert_id = $stmt->insert_id;
    echo "INSERT successful, ID: $insert_id<br>";
} else {
    echo "INSERT failed: " . $stmt->error . "<br>";
}
$stmt->close();

// Test SELECT
echo "Testing SELECT...<br>";
$result = $conn->query("SELECT * FROM services WHERE name = 'Test Service'");
if ($result && $result->num_rows > 0) {
    $service = $result->fetch_assoc();
    echo "SELECT successful: " . htmlspecialchars($service['name']) . "<br>";
} else {
    echo "SELECT failed or no data<br>";
}

// Test UPDATE
echo "Testing UPDATE...<br>";
$new_name = "Updated Test Service";
$update_stmt = $conn->prepare("UPDATE services SET name = ? WHERE id = ?");
$update_stmt->bind_param("si", $new_name, $insert_id);
if ($update_stmt->execute()) {
    echo "UPDATE successful<br>";
} else {
    echo "UPDATE failed: " . $update_stmt->error . "<br>";
}
$update_stmt->close();

// Verify UPDATE
$result = $conn->query("SELECT name FROM services WHERE id = $insert_id");
if ($result && $result->num_rows > 0) {
    $service = $result->fetch_assoc();
    echo "Verified UPDATE: " . htmlspecialchars($service['name']) . "<br>";
}

// Test DELETE
echo "Testing DELETE...<br>";
$delete_stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
$delete_stmt->bind_param("i", $insert_id);
if ($delete_stmt->execute()) {
    echo "DELETE successful<br>";
} else {
    echo "DELETE failed: " . $delete_stmt->error . "<br>";
}
$delete_stmt->close();

// Verify DELETE
$result = $conn->query("SELECT * FROM services WHERE id = $insert_id");
if ($result && $result->num_rows == 0) {
    echo "Verified DELETE: Service removed<br>";
} else {
    echo "DELETE verification failed<br>";
}

$conn->close();
echo "CRUD testing completed.";
?>
