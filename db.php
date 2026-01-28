<?php
$host     = 'localhost';
$user     = 'root';          // your DB username
$password = '';              // your DB password
$dbname   = 'navbridge'; // your DB name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
