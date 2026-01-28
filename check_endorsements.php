<?php
require 'db.php';

echo "Table Structure:\n";
$result = $conn->query('DESCRIBE endorsements');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
