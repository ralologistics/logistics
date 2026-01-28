<?php
require 'db.php';

// Disable foreign key checks temporarily
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// DROP and CREATE TABLE containers
$drop_sql = "DROP TABLE IF EXISTS containers";
$create_sql = "
CREATE TABLE containers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('import','cart','swing','export') NOT NULL,
    job_id INT NOT NULL,
    booking_id VARCHAR(255) NOT NULL,
    cut_off_date DATE,
    grid_position VARCHAR(50),
    no_of_containers INT DEFAULT 1,
    reference VARCHAR(100),
    container_no VARCHAR(50),
    iso_code_id INT NOT NULL,
    weight DECIMAL(10,2),

    from_location VARCHAR(150),
    to_location VARCHAR(150),
    return_to VARCHAR(150),

    customer_location VARCHAR(150),
    door_type_id INT,

    security_check VARCHAR(100),
    random_number VARCHAR(100),
    release_ecn_number VARCHAR(100),
    port_pin_no VARCHAR(100),

    available_date DATE,
    vb_slot_date DATE,
    demurrage_date DATE,
    detention_days INT DEFAULT 0,

    shipping_id INT,
    vessel_id INT,
    ship_type_id INT,
    voyage VARCHAR(100),
    container_status ENUM('EMPTY', 'FULL', 'PARTIAL', 'IN_TRANSIT', 'DELIVERED', 'RETURNED') DEFAULT 'EMPTY',

    xray TINYINT DEFAULT 0,
    dgs TINYINT DEFAULT 0,
    dgs_status ENUM('PENDING', 'APPROVED', 'REJECTED', 'IN_PROGRESS', 'COMPLETED') DEFAULT 'PENDING',
    live_ul TINYINT DEFAULT 0,
    live_ul_status ENUM('PENDING', 'APPROVED', 'REJECTED', 'IN_PROGRESS', 'COMPLETED') DEFAULT 'PENDING',

    hold_sh TINYINT DEFAULT 0,
    hold_customs TINYINT DEFAULT 0,
    hold_mpi TINYINT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_containers_booking_id
        FOREIGN KEY (booking_id)
        REFERENCES job_bookings(booking_id)
        ON DELETE CASCADE,
    FOREIGN KEY (iso_code_id) REFERENCES iso_codes(id),
    FOREIGN KEY (door_type_id) REFERENCES door_types(id),
    FOREIGN KEY (shipping_id) REFERENCES shippings(id),
    FOREIGN KEY (vessel_id) REFERENCES vessels(id),
    FOREIGN KEY (ship_type_id) REFERENCES ship_types(id)
)
";

if ($conn->query($drop_sql) === TRUE) {
    echo "Table containers dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

if ($conn->query($create_sql) === TRUE) {
    echo "Table containers created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$conn->close();
?>
