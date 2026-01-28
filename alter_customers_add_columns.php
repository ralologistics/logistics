<?php
require 'db.php';

$errors = [];
$successes = [];

// Add customer_id column if it doesn't exist
$check_customer_id_sql = "SHOW COLUMNS FROM customers LIKE 'customer_id'";
$result_customer_id = $conn->query($check_customer_id_sql);

if ($result_customer_id->num_rows == 0) {
    $add_customer_id_sql = "ALTER TABLE customers ADD COLUMN customer_id INT NOT NULL";
    if ($conn->query($add_customer_id_sql) === TRUE) {
        $successes[] = "Column 'customer_id' added successfully.";
    } else {
        $errors[] = "Error adding 'customer_id' column: " . $conn->error;
    }
} else {
    $successes[] = "Column 'customer_id' already exists.";
}

// Add document_received_at column if it doesn't exist
$check_document_received_at_sql = "SHOW COLUMNS FROM customers LIKE 'document_received_at'";
$result_document_received_at = $conn->query($check_document_received_at_sql);

if ($result_document_received_at->num_rows == 0) {
    $add_document_received_at_sql = "ALTER TABLE customers ADD COLUMN document_received_at DATETIME NOT NULL";
    if ($conn->query($add_document_received_at_sql) === TRUE) {
        $successes[] = "Column 'document_received_at' added successfully.";
    } else {
        $errors[] = "Error adding 'document_received_at' column: " . $conn->error;
    }
} else {
    $successes[] = "Column 'document_received_at' already exists.";
}

// Output results
if (!empty($successes)) {
    echo "Successes:<br>";
    foreach ($successes as $success) {
        echo "- $success<br>";
    }
}

if (!empty($errors)) {
    echo "<br>Errors:<br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
} else {
    echo "<br>All operations completed successfully.";
}

$conn->close();
?>
