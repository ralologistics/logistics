<?php
require 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS `door_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table door_types created successfully.<br>";
    
    // Insert default values
    $default_values = [
        "Doors TSA",
        "Doors Either",
        "Door Rear",
        "Door Front",
        "Door Forward"
    ];
    
    foreach ($default_values as $name) {
        $check = $conn->query("SELECT id FROM door_types WHERE name = '" . $conn->real_escape_string($name) . "'");
        if ($check->num_rows == 0) {
            $insert = "INSERT INTO door_types (name) VALUES ('" . $conn->real_escape_string($name) . "')";
            if ($conn->query($insert)) {
                echo "Inserted: " . htmlspecialchars($name) . "<br>";
            }
        }
    }
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
