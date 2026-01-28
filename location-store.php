<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $location_code = !empty($_POST['location_code']) ? trim($_POST['location_code']) : null;
    $company_id = (int)$_POST['company_id'];
    $location_type_id = (int)$_POST['location_type_id'];
    $door_type_id = (int)$_POST['door_type_id'];
    $lift_type_id = (int)$_POST['lift_type_id'];
    $building = !empty($_POST['building']) ? trim($_POST['building']) : null;
    $street_no = !empty($_POST['street_no']) ? trim($_POST['street_no']) : null;
    $street = !empty($_POST['street']) ? trim($_POST['street']) : null;
    $suburb = !empty($_POST['suburb']) ? trim($_POST['suburb']) : null;
    $city = !empty($_POST['city']) ? trim($_POST['city']) : null;
    $state = !empty($_POST['state']) ? trim($_POST['state']) : null;
    $postcode = !empty($_POST['postcode']) ? trim($_POST['postcode']) : null;
    $country_id = (int)$_POST['country_id'];
    $contact_person = !empty($_POST['contact_person']) ? trim($_POST['contact_person']) : null;
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $mobile = !empty($_POST['mobile']) ? trim($_POST['mobile']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $send_tracking_email = isset($_POST['send_tracking_email']) ? (int)$_POST['send_tracking_email'] : 0;
    $special_instruction = !empty($_POST['special_instruction']) ? trim($_POST['special_instruction']) : null;

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Location name is required']);
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE locations SET 
            name=?, 
            location_code=?, 
            company_id=?, 
            location_type_id=?, 
            door_type_id=?, 
            lift_type_id=?, 
            building=?, 
            street_no=?, 
            street=?, 
            suburb=?, 
            city=?, 
            state=?, 
            postcode=?, 
            country_id=?, 
            contact_person=?, 
            phone=?, 
            mobile=?, 
            email=?, 
            send_tracking_email=?, 
            special_instruction=? 
            WHERE id=?");
        $stmt->bind_param("ssiiiissssssssisssisi", 
            $name, 
            $location_code, 
            $company_id, 
            $location_type_id, 
            $door_type_id, 
            $lift_type_id, 
            $building, 
            $street_no, 
            $street, 
            $suburb, 
            $city, 
            $state, 
            $postcode, 
            $country_id, 
            $contact_person, 
            $phone, 
            $mobile, 
            $email, 
            $send_tracking_email, 
            $special_instruction, 
            $id
        );
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO locations (
            name, 
            location_code, 
            company_id, 
            location_type_id, 
            door_type_id, 
            lift_type_id, 
            building, 
            street_no, 
            street, 
            suburb, 
            city, 
            state, 
            postcode, 
            country_id, 
            contact_person, 
            phone, 
            mobile, 
            email, 
            send_tracking_email, 
            special_instruction
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiiissssssssisssis", 
            $name, 
            $location_code, 
            $company_id, 
            $location_type_id, 
            $door_type_id, 
            $lift_type_id, 
            $building, 
            $street_no, 
            $street, 
            $suburb, 
            $city, 
            $state, 
            $postcode, 
            $country_id, 
            $contact_person, 
            $phone, 
            $mobile, 
            $email, 
            $send_tracking_email, 
            $special_instruction
        );
    }

    if ($stmt->execute()) {
        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true]);
        } else {
            header("Location: location-list.php");
            exit;
        }
    } else {
        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        } else {
            echo "Error: " . $stmt->error;
            echo "<br><a href='location-form.php'>Go Back</a>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>