<?php
require 'db.php';

$tables = ['customers', 'job_bookings', 'vessels', 'shippings'];

foreach ($tables as $table) {
    echo "Describing table: $table<br>";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "<br>";
        }
    } else {
        echo "Error describing table: " . $conn->error . "<br>";
    }
    echo "<br>";
}

$conn->close();
?>
