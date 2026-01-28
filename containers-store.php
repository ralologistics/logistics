<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;

    // Get form data
    $import_job_id = (int)$_POST['import_job_id'];
    $job_type = 'import';
    $job_id = $import_job_id;
    $booking_id = trim($_POST['booking_id']);
    $cut_off_date = !empty($_POST['cut_off_date']) ? $_POST['cut_off_date'] : null;
    $grid_position = trim($_POST['grid_position'] ?? '');
    $no_of_containers = !empty($_POST['no_of_containers']) ? (int)$_POST['no_of_containers'] : 1;
    $reference = trim($_POST['reference'] ?? '');
    $container_no = trim($_POST['container_no'] ?? '');
    $iso_code_id = (int)$_POST['iso_code_id'];
    $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;

    $from_location = trim($_POST['from_location'] ?? '');
    $to_location = trim($_POST['to_location'] ?? '');
    $return_to = trim($_POST['return_to'] ?? '');
    $customer_location = trim($_POST['customer_location'] ?? '');
    $door_type_id = !empty($_POST['door_type_id']) ? (int)$_POST['door_type_id'] : null;

    $security_check = trim($_POST['security_check'] ?? '');
    $random_number = trim($_POST['random_number'] ?? '');
    $release_ecn_number = trim($_POST['release_ecn_number'] ?? '');
    $port_pin_no = trim($_POST['port_pin_no'] ?? '');

    $available_date = !empty($_POST['available_date']) ? $_POST['available_date'] : null;
    $vb_slot_date = !empty($_POST['vb_slot_date']) ? $_POST['vb_slot_date'] : null;
    $demurrage_date = !empty($_POST['demurrage_date']) ? $_POST['demurrage_date'] : null;
    $detention_days = (int)($_POST['detention_days'] ?? 0);

    $shipping_id = !empty($_POST['shipping_id']) ? (int)$_POST['shipping_id'] : null;
    $vessel_id = !empty($_POST['vessel_id']) ? (int)$_POST['vessel_id'] : null;
    $ship_type_id = !empty($_POST['ship_type_id']) ? (int)$_POST['ship_type_id'] : null;
    $voyage = trim($_POST['voyage'] ?? '');

    $xray = isset($_POST['xray']) ? 1 : 0;
    $dgs = isset($_POST['dgs']) ? 1 : 0;
    $live_ul = isset($_POST['live_ul']) ? 1 : 0;
    $hold_sh = isset($_POST['hold_sh']) ? 1 : 0;
    $hold_customs = isset($_POST['hold_customs']) ? 1 : 0;
    $hold_mpi = isset($_POST['hold_mpi']) ? 1 : 0;

    // Validation
    if (empty($import_job_id) || empty($booking_id) || empty($iso_code_id)) {
        header("Location: containers-form.php?error=Required fields are missing" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    $conn->begin_transaction();
    try {
        if ($id) {
            // Update existing container
            $stmt = $conn->prepare("UPDATE containers SET
                job_type=?,
                job_id=?,
                booking_id=?,
                cut_off_date=?,
                grid_position=?,
                no_of_containers=?,
                reference=?,
                container_no=?,
                iso_code_id=?,
                weight=?,
                from_location=?,
                to_location=?,
                return_to=?,
                customer_location=?,
                door_type_id=?,
                security_check=?,
                random_number=?,
                release_ecn_number=?,
                port_pin_no=?,
                available_date=?,
                vb_slot_date=?,
                demurrage_date=?,
                detention_days=?,
                shipping_id=?,
                vessel_id=?,
                ship_type_id=?,
                voyage=?,
                xray=?,
                dgs=?,
                live_ul=?,
                hold_sh=?,
                hold_customs=?,
                hold_mpi=?
                WHERE id=?");

            $stmt->bind_param("sisssisissidsssssissssssiiisiiiiiiii",
                $job_type,
                $job_id,
                $booking_id,
                $cut_off_date,
                $grid_position,
                $no_of_containers,
                $reference,
                $container_no,
                $iso_code_id,
                $weight,
                $from_location,
                $to_location,
                $return_to,
                $customer_location,
                $door_type_id,
                $security_check,
                $random_number,
                $release_ecn_number,
                $port_pin_no,
                $available_date,
                $vb_slot_date,
                $demurrage_date,
                $detention_days,
                $shipping_id,
                $vessel_id,
                $ship_type_id,
                $voyage,
                $xray,
                $dgs,
                $live_ul,
                $hold_sh,
                $hold_customs,
                $hold_mpi,
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Container update failed: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // Insert new container
            $stmt = $conn->prepare("INSERT INTO containers (
                job_type,
                job_id,
                booking_id,
                cut_off_date,
                grid_position,
                no_of_containers,
                reference,
                container_no,
                iso_code_id,
                weight,
                from_location,
                to_location,
                return_to,
                customer_location,
                door_type_id,
                security_check,
                random_number,
                release_ecn_number,
                port_pin_no,
                available_date,
                vb_slot_date,
                demurrage_date,
                detention_days,
                shipping_id,
                vessel_id,
                ship_type_id,
                voyage,
                xray,
                dgs,
                live_ul,
                hold_sh,
                hold_customs,
                hold_mpi
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sisssisissidsssssissssssiiisiiiiiii",
                $job_type,
                $job_id,
                $booking_id,
                $cut_off_date,
                $grid_position,
                $no_of_containers,
                $reference,
                $container_no,
                $iso_code_id,
                $weight,
                $from_location,
                $to_location,
                $return_to,
                $customer_location,
                $door_type_id,
                $security_check,
                $random_number,
                $release_ecn_number,
                $port_pin_no,
                $available_date,
                $vb_slot_date,
                $demurrage_date,
                $detention_days,
                $shipping_id,
                $vessel_id,
                $ship_type_id,
                $voyage,
                $xray,
                $dgs,
                $live_ul,
                $hold_sh,
                $hold_customs,
                $hold_mpi
            );

            if (!$stmt->execute()) {
                throw new Exception("Container insert failed: " . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();
        header("Location: containers-list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<br><a href='containers-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    } finally {
        $conn->close();
    }
}
?>
