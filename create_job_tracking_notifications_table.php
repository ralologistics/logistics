<?php
require 'db.php';

// Drop existing table if it exists
$conn->query("DROP TABLE IF EXISTS job_tracking_notifications");

// Create the table
$sql = "CREATE TABLE job_tracking_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED NOT NULL,
    communication_type ENUM('EMAIL','PHONE','SMS','WHATSAPP','PUSH') NOT NULL,
    contact VARCHAR(150),
    notification_type_id INT,
    FOREIGN KEY (job_id) REFERENCES job_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (notification_type_id) REFERENCES notification_types(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'job_tracking_notifications' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
