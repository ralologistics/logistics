<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;

    // Collect form data - matching new table structure
    $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $shipping__id = !empty($_POST['shipping__id']) ? (int)$_POST['shipping__id'] : null;
    $vessel_id = !empty($_POST['vessel_id']) ? (int)$_POST['vessel_id'] : 0;
    $voyage = trim($_POST['voyage'] ?? '');
    $from_location = trim($_POST['from_location'] ?? '');
    $to_location = trim($_POST['to_location'] ?? '');
    $document_received_at = $_POST['document_received_at'] ?? '';

    // Get containers, services, and notes
    $containers = $_POST['containers'] ?? [];
    $service_ids = $_POST['service_ids'] ?? [];
    $service_booking_ids = $_POST['service_booking_ids'] ?? [];
    $booking_ids = $_POST['booking_ids'] ?? [];
    $endorsement_ids = $_POST['endorsement_ids'] ?? [];
    $notes = $_POST['notes'] ?? [];

    // Validation
    if (empty($customer_id) || empty($booking_id) || empty($vessel_id) || empty($from_location) || empty($to_location) || empty($document_received_at)) {
        header("Location: export-job-form.php?error=Required fields are missing" . ($id ? "&id=" . (int)$id : ""));
        exit;
    }

    $conn->begin_transaction();
    try {
        if ($id) {
            // UPDATE - don't update job_no, it's auto-generated
            $stmt = $conn->prepare("UPDATE export_job_bookings SET
                customer_id=?,
                booking_id=?,
                shipping__id=?,
                vessel_id=?,
                voyage=?,
                from_location=?,
                to_location=?,
                document_received_at=?
                WHERE id=?");

            $stmt->bind_param("iiissssi",
                $customer_id,
                $booking_id,
                $shipping__id,
                $vessel_id,
                $voyage,
                $from_location,
                $to_location,
                $document_received_at,
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Export job update failed: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // INSERT - job_no will be auto-generated after insert
            $stmt = $conn->prepare("INSERT INTO export_job_bookings (
                customer_id,
                booking_id,
                shipping__id,
                vessel_id,
                voyage,
                from_location,
                to_location,
                document_received_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("iiisssss",
                $customer_id,
                $booking_id,
                $shipping__id,
                $vessel_id,
                $voyage,
                $from_location,
                $to_location,
                $document_received_at
            );

            if (!$stmt->execute()) {
                throw new Exception("Export job insert failed: " . $stmt->error);
            }
            $id = (int)$conn->insert_id;
            $stmt->close();
            
            // Generate job_no using format: EXPORT-YYYY-N (e.g., EXPORT-2026-1)
            $current_year = date('Y');
            
            // Get the maximum number for this year to determine next number
            $maxQuery = $conn->prepare("SELECT job_no FROM export_job_bookings WHERE job_no LIKE ? ORDER BY job_no DESC LIMIT 1");
            $likePattern = 'EXPORT-' . $current_year . '-%';
            $maxQuery->bind_param("s", $likePattern);
            $maxQuery->execute();
            $maxResult = $maxQuery->get_result();
            $maxRow = $maxResult->fetch_assoc();
            $maxQuery->close();
            
            $nextNumber = 1;
            if ($maxRow && !empty($maxRow['job_no'])) {
                // Extract number from job_no (e.g., EXPORT-2026-5 -> 5)
                $parts = explode('-', $maxRow['job_no']);
                if (count($parts) >= 3) {
                    $lastNumber = (int)$parts[2];
                    $nextNumber = $lastNumber + 1;
                }
            }
            
            $job_no = 'EXPORT-' . $current_year . '-' . $nextNumber;
            
            $stmtUpdate = $conn->prepare("UPDATE export_job_bookings SET job_no = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $job_no, $id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        // -----------------------------
        // Containers (replace all)
        // -----------------------------
        $stmtDel = $conn->prepare("DELETE FROM containers WHERE job_type = 'export' AND job_id = ?");
        $stmtDel->bind_param("i", $id);
        if (!$stmtDel->execute()) {
            throw new Exception("Containers delete failed: " . $stmtDel->error);
        }
        $stmtDel->close();

        if (!empty($containers)) {
            $stmtIns = $conn->prepare("INSERT INTO containers (
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
                available_date,
                door_type_id,
                random_number,
                release_ecn_number,
                port_pin_no,
                xray,
                dgs,
                live_ul
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($containers as $c) {
                $container_booking_id = trim((string)($c['booking_id'] ?? ''));
                $reference = trim((string)($c['reference'] ?? ''));
                $container_no = trim((string)($c['container_no'] ?? ''));
                $iso_code_id = !empty($c['iso_code_id']) ? (int)$c['iso_code_id'] : null;
                $weight = (float)($c['weight'] ?? 0);
                $no_of_containers = !empty($c['no_of_containers']) ? (int)$c['no_of_containers'] : 1;
                $cut_off_date = !empty($c['cut_off_date']) ? (string)$c['cut_off_date'] : null;
                $grid_position = trim((string)($c['grid_position'] ?? ''));
                $available_date = !empty($c['available_date']) ? (string)$c['available_date'] : null;
                $door_type_id = !empty($c['door_type_id']) ? (int)$c['door_type_id'] : null;
                $random_number = trim((string)($c['random_number'] ?? ''));
                $release_ecn_number = trim((string)($c['release_ecn_number'] ?? ''));
                $port_pin_no = trim((string)($c['port_pin_no'] ?? ''));
                $xray = !empty($c['xray']) ? 1 : 0;
                $dgs = !empty($c['dgs']) ? 1 : 0;
                $live_ul = !empty($c['live_ul']) ? 1 : 0;

                // minimal validation for required fields
                if (empty($reference) || empty($iso_code_id) || empty($weight) || empty($cut_off_date) || empty($door_type_id) || empty($release_ecn_number) || empty($port_pin_no)) {
                    continue;
                }

                $job_type = 'export';
                $job_id = $id;
                
                $stmtIns->bind_param(
                    "sisssisississssiii",
                    $job_type,              // s
                    $job_id,                // i
                    $container_booking_id,  // s
                    $cut_off_date,          // s
                    $grid_position,         // s
                    $no_of_containers,      // i
                    $reference,             // s
                    $container_no,          // s
                    $iso_code_id,           // i
                    $weight,                // d
                    $available_date,        // s
                    $door_type_id,          // i
                    $random_number,         // s
                    $release_ecn_number,    // s
                    $port_pin_no,           // s
                    $xray,                  // i
                    $dgs,                   // i
                    $live_ul                // i
                );

                if (!$stmtIns->execute()) {
                    throw new Exception("Container insert failed: " . $stmtIns->error);
                }
            }
            $stmtIns->close();
        }

        // -----------------------------
        // Services (replace all)
        // -----------------------------
        // Get all container IDs for this booking (after containers were inserted)
        $containerIds = [];
        if (!empty($containers)) {
            $stmtGetContainers = $conn->prepare("SELECT id FROM containers WHERE job_type = 'export' AND job_id = ? ORDER BY id ASC");
            $stmtGetContainers->bind_param("i", $id);
            $stmtGetContainers->execute();
            $result = $stmtGetContainers->get_result();
            while ($row = $result->fetch_assoc()) {
                $containerIds[] = (int)$row['id'];
            }
            $stmtGetContainers->close();
        }
        
        // Delete services by job_type='export' and container_id matching this booking's containers
        if (!empty($containerIds)) {
            $containerIdsStr = implode(',', array_map('intval', $containerIds));
            $deleteQuery = "DELETE FROM import_job_services WHERE job_type = 'export' AND container_id IN ($containerIdsStr)";
            if (!$conn->query($deleteQuery)) {
                throw new Exception("Services delete failed: " . $conn->error);
            }
        }

        if (!empty($service_ids) && !empty($containerIds)) {
            $stmtSrv = $conn->prepare("INSERT INTO import_job_services (job_type, service_id, container_id) VALUES (?, ?, ?)");
            $jobType = 'export';
            foreach ($service_ids as $sid) {
                $sid = (int)$sid;
                if ($sid <= 0) continue;
                
                // Insert service for each container
                foreach ($containerIds as $containerId) {
                    $stmtSrv->bind_param("sii", $jobType, $sid, $containerId);
                    if (!$stmtSrv->execute()) {
                        throw new Exception("Service insert failed: " . $stmtSrv->error);
                    }
                }
            }
            $stmtSrv->close();
        }

        // -----------------------------
        // Notes (replace all)
        // -----------------------------
        $nCount = min(count($booking_ids), count($endorsement_ids), count($notes));
        if ($nCount > 0) {
            $stmtNote = $conn->prepare("INSERT INTO import_job_notes (job_type, booking_id, endorsement_id, note) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < $nCount; $i++) {
                $bid = trim((string)$booking_ids[$i]);
                $eid = (int)$endorsement_ids[$i];
                $noteText = trim((string)$notes[$i]);
                if (empty($bid) || $eid <= 0) continue;
                $jobType = 'export';
                $stmtNote->bind_param("ssis", $jobType, $bid, $eid, $noteText);
                if (!$stmtNote->execute()) {
                    throw new Exception("Note insert failed: " . $stmtNote->error);
                }
            }
            $stmtNote->close();
        }

        $conn->commit();
        header("Location: export-job-list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<br><a href='export-job-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    } finally {
        $conn->close();
    }
}
?>
