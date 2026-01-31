<?php
require 'db.php';

// job_addresses ko update: customer default addresses bhi isi table mein (booking_id NULL, customer_id set)
$ok = true;

// 1. booking_id nullable (customer default rows ke liye booking_id = NULL)
if ($conn->query("ALTER TABLE job_addresses MODIFY COLUMN booking_id INT UNSIGNED NULL") !== TRUE) {
    echo "Error making booking_id nullable: " . $conn->error . "<br>";
    $ok = false;
}

// 2. customer_id column add – customers.id jaisa type use karo (FK ke liye match)
$check = $conn->query("SHOW COLUMNS FROM job_addresses LIKE 'customer_id'");
if ($check->num_rows === 0) {
    $custCol = $conn->query("SHOW COLUMNS FROM customers WHERE Field = 'id'")->fetch_assoc();
    $idType = $custCol ? ($custCol['Type'] ?? 'INT') : 'INT';
    $sql = "ALTER TABLE job_addresses ADD COLUMN customer_id $idType NULL AFTER booking_id";
    if ($conn->query($sql) !== TRUE) {
        echo "Error adding customer_id: " . $conn->error . "<br>";
        $ok = false;
    }
}

// 3. Unique pehle (customer_id + party_role) – isse customer_id par index bhi ban jata hai
$uk = $conn->query("SHOW INDEX FROM job_addresses WHERE Key_name = 'uk_customer_role'");
if ($uk->num_rows === 0) {
    if ($conn->query("ALTER TABLE job_addresses ADD UNIQUE KEY uk_customer_role (customer_id, party_role)") !== TRUE) {
        echo "Error adding unique key: " . $conn->error . "<br>";
        $ok = false;
    }
}

// 4. FK optional – skip karo (PHP 8+ mein query fail par exception aata hai; bina FK bhi kaam chalega)
$fk = $conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'job_addresses' AND COLUMN_NAME = 'customer_id' AND REFERENCED_TABLE_NAME = 'customers'");
if ($fk->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE job_addresses ADD CONSTRAINT fk_ja_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE");
    } catch (mysqli_sql_exception $e) {
        echo "Note: Foreign key to customers skip (" . $e->getMessage() . "). Column + unique key applied, app chal jayegi.<br>";
    }
}

if ($ok) {
    echo "job_addresses table updated. Customer default addresses ab isi table mein (booking_id NULL, customer_id set).";
} else {
    echo "Kuch steps fail ho gaye.";
}
$conn->close();
