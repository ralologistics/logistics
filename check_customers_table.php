<?php
require 'db.php';

echo "Checking customers table structure...<br><br>";

// Check if customers table exists
$result = $conn->query("SHOW TABLES LIKE 'customers'");
if ($result->num_rows == 0) {
    echo "Error: customers table does not exist!<br>";
    $conn->close();
    exit;
}

// Describe customers table
echo "Customers table structure:<br>";
$result = $conn->query("DESCRIBE customers");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " " . $row['Type'] . " " . ($row['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . " " . ($row['Key'] == 'PRI' ? 'PRIMARY KEY' : '') . "<br>";
    }
} else {
    echo "Error describing customers table: " . $conn->error . "<br>";
}

// Check if there are any records
$result = $conn->query("SELECT COUNT(*) as count FROM customers");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<br>Number of customers: " . $row['count'] . "<br>";
} else {
    echo "Error counting customers: " . $conn->error . "<br>";
}

$conn->close();
?>
