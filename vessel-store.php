<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $country_id = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null;
    $ship_type_id = !empty($_POST['ship_type_id']) ? (int)$_POST['ship_type_id'] : null;
    $imo_number = !empty($_POST['imo_number']) ? trim($_POST['imo_number']) : null;
    $mmsi = !empty($_POST['mmsi']) ? trim($_POST['mmsi']) : null;
    $call_sign = !empty($_POST['call_sign']) ? strtoupper(trim($_POST['call_sign'])) : null;
    $built_year = !empty($_POST['built_year']) ? (int)$_POST['built_year'] : null;
    $length_m = !empty($_POST['length_m']) ? (float)$_POST['length_m'] : null;
    $width_m = !empty($_POST['width_m']) ? (float)$_POST['width_m'] : null;
    $draught_m = !empty($_POST['draught_m']) ? (float)$_POST['draught_m'] : null;
    $gross_tonnage = !empty($_POST['gross_tonnage']) ? (int)$_POST['gross_tonnage'] : null;
    $net_tonnage = !empty($_POST['net_tonnage']) ? (int)$_POST['net_tonnage'] : null;
    $dead_weight = !empty($_POST['dead_weight']) ? (int)$_POST['dead_weight'] : null;

    if (empty($name) || empty($country_id) || empty($ship_type_id)) {
        header("Location: vessel-form.php?error=Required fields are missing");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE vessels SET 
            name=?, 
            country_id=?, 
            ship_type_id=?, 
            imo_number=?, 
            mmsi=?, 
            call_sign=?, 
            built_year=?, 
            length_m=?, 
            width_m=?, 
            draught_m=?, 
            gross_tonnage=?, 
            net_tonnage=?, 
            dead_weight=? 
            WHERE id=?");
        $stmt->bind_param("siisssidddiiii", 
            $name, 
            $country_id, 
            $ship_type_id, 
            $imo_number, 
            $mmsi, 
            $call_sign, 
            $built_year, 
            $length_m, 
            $width_m, 
            $draught_m, 
            $gross_tonnage, 
            $net_tonnage, 
            $dead_weight, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO vessels (
            name, 
            country_id, 
            ship_type_id, 
            imo_number, 
            mmsi, 
            call_sign, 
            built_year, 
            length_m, 
            width_m, 
            draught_m, 
            gross_tonnage, 
            net_tonnage, 
            dead_weight
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siisssidddiii", 
            $name, 
            $country_id, 
            $ship_type_id, 
            $imo_number, 
            $mmsi, 
            $call_sign, 
            $built_year, 
            $length_m, 
            $width_m, 
            $draught_m, 
            $gross_tonnage, 
            $net_tonnage, 
            $dead_weight
        );
    }

    if ($stmt->execute()) {
        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Vessel saved successfully']);
        } else {
            header("Location: vessel-list.php");
        }
        exit;
    } else {
        $error = "Error: " . $stmt->error;
        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => $error]);
        } else {
            echo $error;
            echo "<br><a href='vessel-form.php'>Go Back</a>";
        }
    }

    $stmt->close();
    $conn->close();
}

?>
