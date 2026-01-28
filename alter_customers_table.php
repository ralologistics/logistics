<?php
require 'db.php';

$errors = [];
$successes = [];

// Check if customer_code exists
$check_customer_code_sql = "SHOW COLUMNS FROM customers LIKE 'customer_code'";
$result = $conn->query($check_customer_code_sql);

if ($result->num_rows > 0) {
    // Check if 'code' already exists
    $check_code_sql = "SHOW COLUMNS FROM customers LIKE 'code'";
    $result_code = $conn->query($check_code_sql);

    if ($result_code->num_rows == 0) {
        // Rename customer_code to code
        $rename_sql = "ALTER TABLE customers CHANGE customer_code code VARCHAR(50)";
        if ($conn->query($rename_sql) === TRUE) {
            $successes[] = "'customer_code' renamed to 'code' successfully.";
        } else {
            $errors[] = "Error renaming column: " . $conn->error;
        }
    } else {
        // If 'code' exists, drop customer_code to avoid duplicate
        $drop_sql = "ALTER TABLE customers DROP COLUMN customer_code";
        if ($conn->query($drop_sql) === TRUE) {
            $successes[] = "'customer_code' dropped because 'code' already exists.";
        } else {
            $errors[] = "Error dropping 'customer_code': " . $conn->error;
        }
    }
} else {
    $successes[] = "'customer_code' does not exist, nothing to rename.";
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
