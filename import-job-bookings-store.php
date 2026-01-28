<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $customer_id = (int)$_POST['customer_id'];
    // datetime-local posts as "YYYY-MM-DDTHH:MM" -> MySQL DATETIME expects space
    $document_received_at = str_replace('T', ' ', (string)($_POST['document_received_at'] ?? ''));

    if (empty($customer_id) || empty($document_received_at)) {
        header("Location: import-job-bookings-form.php?error=Required fields are missing");
        exit;
    }

    // Nested arrays from form
    $containers = (isset($_POST['containers']) && is_array($_POST['containers'])) ? $_POST['containers'] : [];
    $service_ids = (isset($_POST['service_ids']) && is_array($_POST['service_ids'])) ? $_POST['service_ids'] : [];
    $service_booking_ids = (isset($_POST['service_booking_ids']) && is_array($_POST['service_booking_ids'])) ? $_POST['service_booking_ids'] : [];
    $booking_ids = (isset($_POST['booking_ids']) && is_array($_POST['booking_ids'])) ? $_POST['booking_ids'] : [];
    $endorsement_ids = (isset($_POST['endorsement_ids']) && is_array($_POST['endorsement_ids'])) ? $_POST['endorsement_ids'] : [];
    $notes = (isset($_POST['notes']) && is_array($_POST['notes'])) ? $_POST['notes'] : [];

    $keep_document_ids = (isset($_POST['keep_document_ids']) && is_array($_POST['keep_document_ids'])) ? $_POST['keep_document_ids'] : [];
    $remove_document_ids = (isset($_POST['remove_document_ids']) && is_array($_POST['remove_document_ids'])) ? $_POST['remove_document_ids'] : [];

    $conn->begin_transaction();
    try {
        if ($id) {
            // Update existing booking
            $stmt = $conn->prepare("UPDATE import_job_bookings SET
                customer_id=?,
                document_received_at=?
                WHERE id=?");
            $stmt->bind_param("isi", $customer_id, $document_received_at, $id);
            if (!$stmt->execute()) {
                throw new Exception("Booking update failed: " . $stmt->error);
            }
            $stmt->close();
            $id = (int)$id;
        } else {
            // Insert new booking
            $stmt = $conn->prepare("INSERT INTO import_job_bookings (
                customer_id,
                document_received_at
            ) VALUES (?, ?)");
            $stmt->bind_param("is", $customer_id, $document_received_at);
            if (!$stmt->execute()) {
                throw new Exception("Booking insert failed: " . $stmt->error);
            }
            $id = (int)$conn->insert_id;
            $stmt->close();
            
            // Generate job_no using format: IMPORT-YYYY-N (e.g., IMPORT-2026-1)
            $current_year = date('Y');
            
            // Get the maximum number for this year to determine next number
            $maxQuery = $conn->prepare("SELECT job_no FROM import_job_bookings WHERE job_no LIKE ? ORDER BY job_no DESC LIMIT 1");
            $likePattern = 'IMPORT-' . $current_year . '-%';
            $maxQuery->bind_param("s", $likePattern);
            $maxQuery->execute();
            $maxResult = $maxQuery->get_result();
            $maxRow = $maxResult->fetch_assoc();
            $maxQuery->close();
            
            $nextNumber = 1;
            if ($maxRow && !empty($maxRow['job_no'])) {
                // Extract number from job_no (e.g., IMPORT-2026-5 -> 5)
                $parts = explode('-', $maxRow['job_no']);
                if (count($parts) >= 3) {
                    $lastNumber = (int)$parts[2];
                    $nextNumber = $lastNumber + 1;
                }
            }
            
            $job_no = 'IMPORT-' . $current_year . '-' . $nextNumber;
            
            $stmtUpdate = $conn->prepare("UPDATE import_job_bookings SET job_no = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $job_no, $id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        // -----------------------------
        // Containers (replace all)
        // -----------------------------
        $stmtDel = $conn->prepare("DELETE FROM containers WHERE job_type = 'import' AND job_id = ?");
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($containers as $c) {
                $container_booking_id = trim((string)($c['booking_id'] ?? ''));
                $reference = trim((string)($c['reference'] ?? ''));
                $container_no = trim((string)($c['container_no'] ?? ''));
                $iso_code_id = (int)($c['iso_code_id'] ?? 0);
                $weight = (float)($c['weight'] ?? 0);
                $from_location = trim((string)($c['from_location'] ?? ''));
                $to_location = trim((string)($c['to_location'] ?? ''));

                $return_to = trim((string)($c['return_to'] ?? ''));
                $customer_location = trim((string)($c['customer_location'] ?? ''));
                $door_type_id = !empty($c['door_type_id']) ? (int)$c['door_type_id'] : null;
                $security_check = trim((string)($c['security_check'] ?? ''));
                $random_number = trim((string)($c['random_number'] ?? ''));
                $release_ecn_number = trim((string)($c['release_ecn_number'] ?? ''));
                $port_pin_no = trim((string)($c['port_pin_no'] ?? ''));
                $available_date = !empty($c['available_date']) ? (string)$c['available_date'] : null;
                $vb_slot_date = !empty($c['vb_slot_date']) ? (string)$c['vb_slot_date'] : null;
                $demurrage_date = !empty($c['demurrage_date']) ? (string)$c['demurrage_date'] : null;
                $detention_days = (int)($c['detention_days'] ?? 0);
                $shipping_id = !empty($c['shipping_id']) ? (int)$c['shipping_id'] : null;
                $vessel_id = !empty($c['vessel_id']) ? (int)$c['vessel_id'] : null;
                $ship_type_id = !empty($c['ship_type_id']) ? (int)$c['ship_type_id'] : null;
                $voyage = trim((string)($c['voyage'] ?? ''));

                $xray = !empty($c['xray']) ? 1 : 0;
                $dgs = !empty($c['dgs']) ? 1 : 0;
                $live_ul = !empty($c['live_ul']) ? 1 : 0;
                $hold_sh = !empty($c['hold_sh']) ? 1 : 0;
                $hold_customs = !empty($c['hold_customs']) ? 1 : 0;
                $hold_mpi = !empty($c['hold_mpi']) ? 1 : 0;

                // minimal validation for screenshot required fields
                if (empty($container_booking_id) || empty($reference) || empty($container_no) || empty($iso_code_id) || empty($weight) || empty($from_location) || empty($to_location)) {
                    continue;
                }

                // Always use id for job_id (form may send 0 for new entries)
                $job_type = 'import';
                $job_id = $id;
                
                $stmtIns->bind_param(
                    // 30 params: s=string, i=integer, d=double
                    // job_type(s), job_id(i), booking_id(s), reference(s), container_no(s), iso_code_id(i), weight(d),
                    // from_location(s), to_location(s), return_to(s), customer_location(s), door_type_id(i),
                    // security_check(s), random_number(s), release_ecn_number(s), port_pin_no(s),
                    // available_date(s), vb_slot_date(s), demurrage_date(s), detention_days(i),
                    // shipping_id(i), vessel_id(i), ship_type_id(i), voyage(s),
                    // xray(i), dgs(i), live_ul(i), hold_sh(i), hold_customs(i), hold_mpi(i)
                    "sisssidssssisssssssiiiisiiiiii",
                    $job_type,              // s
                    $job_id,                // i
                    $container_booking_id,  // s
                    $reference,             // s
                    $container_no,          // s
                    $iso_code_id,           // i
                    $weight,                // d
                    $from_location,         // s
                    $to_location,           // s
                    $return_to,             // s
                    $customer_location,     // s
                    $door_type_id,          // i
                    $security_check,        // s
                    $random_number,         // s
                    $release_ecn_number,    // s
                    $port_pin_no,           // s
                    $available_date,        // s
                    $vb_slot_date,          // s
                    $demurrage_date,        // s
                    $detention_days,        // i
                    $shipping_id,           // i
                    $vessel_id,             // i
                    $ship_type_id,          // i
                    $voyage,                // s
                    $xray,                  // i
                    $dgs,                   // i
                    $live_ul,               // i
                    $hold_sh,               // i
                    $hold_customs,          // i
                    $hold_mpi              // i
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
            $stmtGetContainers = $conn->prepare("SELECT id FROM containers WHERE job_type = 'import' AND job_id = ? ORDER BY id ASC");
            $stmtGetContainers->bind_param("i", $id);
            $stmtGetContainers->execute();
            $result = $stmtGetContainers->get_result();
            while ($row = $result->fetch_assoc()) {
                $containerIds[] = (int)$row['id'];
            }
            $stmtGetContainers->close();
        }
        
        // Delete services by job_type='import' and container_id matching this booking's containers
        if (!empty($containerIds)) {
            $containerIdsStr = implode(',', array_map('intval', $containerIds));
            $deleteQuery = "DELETE FROM import_job_services WHERE job_type = 'import' AND container_id IN ($containerIdsStr)";
            if (!$conn->query($deleteQuery)) {
                throw new Exception("Services delete failed: " . $conn->error);
            }
        }

        if (!empty($service_ids) && !empty($containerIds) && !empty($service_booking_ids)) {
            $stmtSrv = $conn->prepare("INSERT INTO import_job_services (job_type, service_id, container_id) VALUES (?, ?, ?)");
            $sCount = min(count($service_ids), count($service_booking_ids));
            $jobType = 'import'; // Set job_type to 'import' for import job bookings
            for ($i = 0; $i < $sCount; $i++) {
                $sid = (int)$service_ids[$i];
                $sbid = trim((string)$service_booking_ids[$i]);
                if ($sid <= 0 || empty($sbid)) continue;
                
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
        // Note: import_job_booking_id has been removed from import_job_notes table
        // Notes are now managed independently through import-job-notes-form.php
        // If you need to link notes to import job bookings, use job_type='import' filter
        $nCount = min(count($booking_ids), count($endorsement_ids), count($notes));
        if ($nCount > 0) {
            $stmtNote = $conn->prepare("INSERT INTO import_job_notes (job_type, booking_id, endorsement_id, note) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < $nCount; $i++) {
                $bid = trim((string)$booking_ids[$i]);
                $eid = (int)$endorsement_ids[$i];
                $noteText = trim((string)$notes[$i]);
                if (empty($bid) || $eid <= 0) continue;
                $jobType = 'import'; // Set job_type to 'import' for import job bookings
                $stmtNote->bind_param("ssis", $jobType, $bid, $eid, $noteText);
                if (!$stmtNote->execute()) {
                    throw new Exception("Note insert failed: " . $stmtNote->error);
                }
            }
            $stmtNote->close();
        }

        // -----------------------------
        // Documents (keep/remove + upload)
        // -----------------------------
        // Remove requested existing documents
        if (!empty($remove_document_ids)) {
            $stmtSel = $conn->prepare("SELECT id, file_path FROM import_job_documents WHERE id = ? AND import_job_id = ?");
            $stmtDelDoc = $conn->prepare("DELETE FROM import_job_documents WHERE id = ? AND import_job_id = ?");
            foreach ($remove_document_ids as $did) {
                $did = (int)$did;
                if ($did <= 0) continue;
                $stmtSel->bind_param("ii", $did, $id);
                $stmtSel->execute();
                $row = $stmtSel->get_result()->fetch_assoc();

                $stmtDelDoc->bind_param("ii", $did, $id);
                $stmtDelDoc->execute();

                // attempt delete file if under uploads/
                if ($row && !empty($row['file_path'])) {
                    $path = (string)$row['file_path'];
                    if (str_starts_with($path, 'uploads/')) {
                        $full = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
                        if (is_file($full)) {
                            @unlink($full);
                        }
                    }
                }
            }
            $stmtSel->close();
            $stmtDelDoc->close();
        }

        // Upload new files (multiple)
        if (isset($_FILES['document_files']) && isset($_FILES['document_files']['name']) && is_array($_FILES['document_files']['name'])) {
            $uploadDirRel = 'uploads/import_job_documents';
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'import_job_documents';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $stmtDoc = $conn->prepare("INSERT INTO import_job_documents (import_job_id, file_path) VALUES (?, ?)");

            $names = $_FILES['document_files']['name'];
            $tmp = $_FILES['document_files']['tmp_name'];
            $errs = $_FILES['document_files']['error'];
            $count = count($names);

            for ($i = 0; $i < $count; $i++) {
                if (!isset($errs[$i]) || $errs[$i] === UPLOAD_ERR_NO_FILE) continue;
                if ($errs[$i] !== UPLOAD_ERR_OK) continue;

                $orig = (string)($names[$i] ?? 'document');
                $ext = pathinfo($orig, PATHINFO_EXTENSION);
                $ext = preg_replace('/[^a-zA-Z0-9]/', '', (string)$ext);
                $safeExt = $ext ? ('.' . strtolower($ext)) : '';
                $newName = 'import_doc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . $safeExt;

                $targetFull = $uploadDir . DIRECTORY_SEPARATOR . $newName;
                if (!move_uploaded_file($tmp[$i], $targetFull)) {
                    continue;
                }

                $filePath = $uploadDirRel . '/' . $newName;
                $stmtDoc->bind_param("is", $id, $filePath);
                if (!$stmtDoc->execute()) {
                    throw new Exception("Document insert failed: " . $stmtDoc->error);
                }
            }

            $stmtDoc->close();
        }

        $conn->commit();
        header("Location: import-job-bookings-list.php");
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<br><a href='import-job-bookings-form.php" . ($id ? "?id=" . (int)$id : "") . "'>Go Back</a>";
    } finally {
        $conn->close();
    }
}

?>
