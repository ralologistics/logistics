<?php
require 'db.php';

$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS job_tracking_notifications");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$sql = "
CREATE TABLE job_tracking_notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    job_id INT UNSIGNED NOT NULL,
    notification_type_id INT UNSIGNED NULL,

    communication_type ENUM(
        'EMAIL','PHONE','SMS','WHATSAPP','PUSH'
    ) NOT NULL,

    contact VARCHAR(150),
    message TEXT,

    is_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_job (job_id),
    INDEX idx_notification_type (notification_type_id),

    CONSTRAINT fk_jtn_job
        FOREIGN KEY (job_id)
        REFERENCES job_bookings(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_jtn_notification_type
        FOREIGN KEY (notification_type_id)
        REFERENCES notification_types(id)
        ON DELETE SET NULL
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
";

if ($conn->query($sql)) {
    echo "✅ job_tracking_notifications created successfully";
} else {
    echo "❌ " . $conn->error;
}

$conn->close();
?>
