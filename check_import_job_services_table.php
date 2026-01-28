<?php
require 'db.php';

echo "Checking import_job_services table structure...<br><br>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'import_job_services'");
if ($result->num_rows == 0) {
    echo "Error: import_job_services table does not exist!<br>";
    $conn->close();
    exit;
}

// Describe table
echo "Import Job Services table structure:<br>";
$result = $conn->query("DESCRIBE import_job_services");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing table: " . $conn->error . "<br>";
}

// Check specifically for booking_id column
echo "<br><br>Checking for booking_id column:<br>";
$check_sql = "SHOW COLUMNS FROM `import_job_services` LIKE 'booking_id'";
$result = $conn->query($check_sql);
if ($result->num_rows > 0) {
    echo "✓ booking_id column EXISTS<br>";
    $row = $result->fetch_assoc();
    echo "Type: " . htmlspecialchars($row['Type']) . "<br>";
    echo "Null: " . htmlspecialchars($row['Null']) . "<br>";
} else {
    echo "✗ booking_id column DOES NOT EXIST<br>";
    echo "<br>You need to run alter_import_job_services_table.php to add it.<br>";
}

$conn->close();
?>
