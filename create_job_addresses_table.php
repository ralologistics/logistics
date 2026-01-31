<?php
require 'db.php';

// Referenced tables: job_bookings(id INT UNSIGNED), addresses(id INT UNSIGNED), customers(id INT UNSIGNED)
// Types exactly match – FK tabhi chalega jab ye tables pehle se maujood hon

$conn->query("DROP TABLE IF EXISTS job_addresses");

$sql = "CREATE TABLE job_addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NULL,
    address_id INT UNSIGNED NOT NULL,
    party_role ENUM('SENDER','RECEIVER') NOT NULL,
    instructions TEXT,
    signature_required TINYINT(1) DEFAULT 0,

    INDEX idx_booking_id (booking_id),
    INDEX idx_customer_id (customer_id),
    UNIQUE KEY uk_customer_role (customer_id, party_role),
    INDEX idx_address_id (address_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
    $conn->close();
    exit;
}

echo "Table job_addresses created.<br>";

// FKs alag se add – agar referenced table nahi hai ya type mismatch to skip, table phir bhi kaam karegi
$fks = [
    ['fk_ja_booking', 'booking_id', 'job_bookings', 'id', 'CASCADE', 'CASCADE'],
    ['fk_ja_address', 'address_id', 'addresses', 'id', 'CASCADE', 'CASCADE'],
    ['fk_ja_customer', 'customer_id', 'customers', 'id', 'SET NULL', 'CASCADE'],
];

foreach ($fks as $fk) {
    list($name, $col, $refTable, $refCol, $onDel, $onUpd) = $fk;
    $check = $conn->query("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '" . $conn->real_escape_string($refTable) . "'");
    if ($check->num_rows === 0) {
        echo "Skip FK $name: table $refTable not found.<br>";
        continue;
    }
    try {
        $conn->query("ALTER TABLE job_addresses ADD CONSTRAINT $name FOREIGN KEY ($col) REFERENCES $refTable($refCol) ON DELETE $onDel ON UPDATE $onUpd");
        echo "FK $name added.<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Skip FK $name: " . $e->getMessage() . "<br>";
    }
}

$conn->close();
echo "Done.";
?>
