<?php
require 'db.php';

$result = $conn->query('SHOW TABLES');
$tables = [];
while($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "Existing tables:\n";
foreach($tables as $table) {
    echo "- $table\n";
}

$conn->close();
?>
