<?php
require 'db.php';

// Check table structure
echo "Table Structure:\n";
$result = $conn->query('DESCRIBE additional_services');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
