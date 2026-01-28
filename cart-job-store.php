<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $customer_id = (int)$_POST['customer_id'];
    $from_location = trim($_POST['from_location']);
    $to_location = trim($_POST['to_location']);
    $job_booking_id = !empty($_POST['job_booking_id']) ? (int)$_POST['job_booking_id'] : null;
    // datetime-local posts as "YYYY-MM-DDTHH:MM" -> MySQL DATETIME expects space
    $document_received_at = str_replace('T', ' ', (string)($_POST['document_received_at'] ?? ''));

    if (empty($customer_id) || empty($from_location) || empty($to_location) || empty($document_received_at)) {
        header("Location: cart-job-form.php?error=Required fields are missing");
        exit;
    }

    // Nested arrays from form
    $containers = (isset($_POST['containers']) && is_array($_POST['containers'])) ? $_POST['containers'] : [];
    $service_ids = (isset($_POST['service_ids']) && is_array($_POST['service_ids'])) ? $_POST['service_ids'] : [];
    $service_booking_ids = (isset($_POST['service_booking_ids']) && is_array($_POST['service_booking_ids'])) ? $_POST['service_booking_ids'] : [];
    $booking_ids = (isset($_POST['booking_ids']) && is_array($_POST['booking_ids'])) ? $_POST['booking_ids'] : [];
    $endorsement_ids = (isset($_POST['endorsement_ids']) && is_array($_POST['endorsement_ids'])) ? $_POST['endorsement_ids'] : [];
    $notes = (isset($_POST['notes']) && is_array($_POST['notes'])) ? $_POST['notes'] : [];

    $conn->begin_transaction();
    try {
        if ($id) {
            // Update existing booking
            $stmt = $conn->prepare("UPDATE cart_job_bookings SET
                customer_id=?,
                from_location=?,
                to_location=?,
                document_received_at=?
                WHERE id=?");
            $stmt->bind_param("isssi", $customer_id, $from_location, $to_location, $document_received_at, $id);
            if (!$stmt->execute()) {
                throw new Exception("Booking update failed: " . $stmt->error);
            }
            $stmt->close();
            $id = (int)$id;
        } else {
            // Insert new booking - get job_booking_id from form or from first container's booking_id
            if (!$job_booking_id && !empty($containers) && !empty($containers[0]['booking_id'])) {
                // Try to find job_booking by booking_id
                $booking_id_str = trim($containers[0]['booking_id']);
                $stmtFind = $conn->prepare("SELECT id FROM job_bookings WHERE booking_id = ? LIMIT 1");
                $stmtFind->bind_param("s", $booking_id_str);
                $stmtFind->execute();
                $result = $stmtFind->get_result();
                if ($row = $result->fetch_assoc()) {
                    $job_booking_id = (int)$row['id'];
                }
                $stmtFind->close();
            }

            if (!$job_booking_id || $job_booking_id <= 0) {
                throw new Exception("Job booking ID is required. Please select a booking ID in the form or ensure containers have a valid booking_id.");
            }

            $stmt = $conn->prepare("INSERT INTO cart_job_bookings (
                customer_id,
                job_booking_id,
                from_location,
                to_location,
                document_received_at
            ) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $customer_id, $job_booking_id, $from_location, $to_location, $document_received_at);
            if (!$stmt->execute()) {
                throw new Exception("Booking insert failed: " . $stmt->error);
            }
            $id = (int)$conn->insert_id;
            $stmt->close();
            
            // Generate job_no using format: CART-YYYY-N (e.g., CART-2026-1)
            $current_year = date('Y');
            
            // Get the maximum number for this year to determine next number
            $maxQuery = $conn->prepare("SELECT job_no FROM cart_job_bookings WHERE job_no LIKE ? ORDER BY job_no DESC LIMIT 1");
            $likePattern = 'CART-' . $current_year . '-%';
            $maxQuery->bind_param("s", $likePattern);
            $maxQuery->execute();
            $maxResult = $maxQuery->get_result();
            $maxRow = $maxResult->fetch_assoc();
            $maxQuery->close();
            
            $nextNumber = 1;
            if ($maxRow && !empty($maxRow['job_no'])) {
                // Extract number from job_no (e.g., CART-2026-5 -> 5)
                $parts = explode('-', $maxRow['job_no']);
                if (count($parts) >= 3) {
                    $lastNumber = (int)$parts[2];
                    $nextNumber = $lastNumber + 1;
                }
            }
            
            $job_no = 'CART-' . $current_year . '-' . $nextNumber;
            
            $stmtUpdate = $conn->prepare("UPDATE cart_job_bookings SET job_no = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $job_no, $id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        // -----------------------------
        // Containers (replace all)
        // -----------------------------
        $stmtDel = $conn->prepare("DELETE FROM containers WHERE job_type = 'cart' AND job_id = ?");
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
                reference,
                container_no,
                iso_code_id,
                weight,
                available_date,
                door_type_id,
                xray,
                dgs,
                live_ul
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($containers as $c) {
                $container_booking_id = trim((string)($c['booking_id'] ?? ''));
                $reference = trim((string)($c['reference'] ?? ''));
                $container_no = trim((string)($c['container_no'] ?? ''));
                $iso_code_id = !empty($c['iso_code_id']) ? (int)$c['iso_code_id'] : null;
                $weight = (float)($c['weight'] ?? 0);
                $available_date = !empty($c['available_date']) ? (string)$c['available_date'] : null;
                $door_type_id = !empty($c['door_type_id']) ? (int)$c['door_type_id'] : null;
                $xray = !empty($c['xray']) ? 1 : 0;
                $dgs = !empty($c['dgs']) ? 1 : 0;
                $live_ul = 0; // Not in form but required by table

                // minimal validation for required fields
                if (empty($reference) || empty($container_no) || empty($weight) || empty($available_date) || empty($door_type_id)) {
                    continue;
                }

                $job_type = 'cart';
                $job_id = $id;
                
                $stmtIns->bind_param(
                    "sisssidssiii",
                    $job_type,              // s
                    $job_id,                // i
                    $container_booking_id,  // s
                    $reference,             // s
                    $container_no,          // s
                    $iso_code_id,           // i (nullable)
                    $weight,                // d
                    $available_date,        // s
                    $door_type_id,          // i
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
            $stmtGetContainers = $conn->prepare("SELECT id FROM containers WHERE job_type = 'cart' AND job_id = ? ORDER BY id ASC");
            $stmtGetContainers->bind_param("i", $id);
            $stmtGetContainers->execute();
            $result = $stmtGetContainers->get_result();
            while ($row = $result->fetch_assoc()) {
                $containerIds[] = (int)$row['id'];
            }
            $stmtGetContainers->close();
        }
        
        // Delete services by job_type='cart' and container_id matching this booking's containers
        if (!empty($containerIds)) {
            $containerIdsStr = implode(',', array_map('intval', $containerIds));
            $deleteQuery = "DELETE FROM import_job_services WHERE job_type = 'cart' AND container_id IN ($containerIdsStr)";
            if (!$conn->query($deleteQuery)) {
                throw new Exception("Services delete failed: " . $conn->error);
            }
        }

        if (!empty($service_ids) && !empty($containerIds)) {
            $stmtSrv = $conn->prepare("INSERT INTO import_job_services (job_type, service_id, container_id) VALUES (?, ?, ?)");
            $jobType = 'cart';
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
                $jobType = 'cart';
                $stmtNote->bind_param("ssis", $jobType, $bid, $eid, $noteText);
                if (!$stmtNote->execute()) {
                    throw new Exception("Note insert failed: " . $stmtNote->error);
                }
            }
            $stmtNote->close();
        }

        $conn->commit();
        header("Location: cart-job-list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<br><a href='cart-job-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    } finally {
        $conn->close();
    }
}
?>
