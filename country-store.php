<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $iso_alpha2 = !empty($_POST['iso_alpha2']) ? strtoupper(trim($_POST['iso_alpha2'])) : null;
    $iso_alpha3 = !empty($_POST['iso_alpha3']) ? strtoupper(trim($_POST['iso_alpha3'])) : null;
    $numeric_code = !empty($_POST['numeric_code']) ? trim($_POST['numeric_code']) : null;
    $phone_code = !empty($_POST['phone_code']) ? trim($_POST['phone_code']) : null;
    $currency = !empty($_POST['currency']) ? trim($_POST['currency']) : null;
    $continent = !empty($_POST['continent']) ? trim($_POST['continent']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name)) {
        header("Location: country-form.php?error=Country name is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE countries SET 
            name=?, 
            iso_alpha2=?, 
            iso_alpha3=?, 
            numeric_code=?, 
            phone_code=?, 
            currency=?, 
            continent=?, 
            is_active=? 
            WHERE id=?");
        $stmt->bind_param("sssssssii", 
            $name, 
            $iso_alpha2, 
            $iso_alpha3, 
            $numeric_code, 
            $phone_code, 
            $currency, 
            $continent, 
            $is_active, 
            $id
        );
    } else {
        // INSERT - Auto-increment ID
        $stmt = $conn->prepare("INSERT INTO countries (
            name, 
            iso_alpha2, 
            iso_alpha3, 
            numeric_code, 
            phone_code, 
            currency, 
            continent, 
            is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", 
            $name, 
            $iso_alpha2, 
            $iso_alpha3, 
            $numeric_code, 
            $phone_code, 
            $currency, 
            $continent, 
            $is_active
        );
    }

    if ($stmt->execute()) {
        // Get the inserted ID for new records
        if (!$id) {
            $new_id = $conn->insert_id;
        }
        header("Location: country-list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='country-form.php'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
}

?>
