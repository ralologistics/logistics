<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$booking = null;

require 'db.php';

// Dropdown data
$customers = $conn->query("SELECT id, name, code FROM customers ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$iso_codes = $conn->query("SELECT id, code FROM iso_codes ORDER BY code ASC")->fetch_all(MYSQLI_ASSOC);
$door_types = $conn->query("SELECT id, name FROM door_types ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$shippings = $conn->query("SELECT id, name FROM shippings ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$vessels = $conn->query("SELECT id, name FROM vessels ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$services = $conn->query("SELECT id, name FROM services WHERE status = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$endorsements = $conn->query("SELECT id, name FROM endorsements WHERE status = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$job_bookings = $conn->query("SELECT booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);

$containers = [];
$selected_services = [];
$selected_notes = [];
$documents = [];

if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM import_job_bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Containers
    $stmt = $conn->prepare("SELECT * FROM containers WHERE job_type = 'import' AND job_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $containers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Services - Get services by job_type='import' and container_id from containers
    // Note: import_job_services table doesn't have booking_id, only container_id
    $containerIds = [];
    if (!empty($containers)) {
        // Get container IDs for this import job booking
        $stmtGetContainers = $conn->prepare("SELECT id FROM containers WHERE job_type = 'import' AND job_id = ? ORDER BY id ASC");
        $stmtGetContainers->bind_param("i", $id);
        $stmtGetContainers->execute();
        $result = $stmtGetContainers->get_result();
        while ($row = $result->fetch_assoc()) {
            $containerIds[] = (int)$row['id'];
        }
        $stmtGetContainers->close();
    }
    
    if (!empty($containerIds)) {
        $containerIdsStr = implode(',', array_map('intval', $containerIds));
        $servicesQuery = "
            SELECT DISTINCT ijs.service_id, ijs.container_id, c.booking_id, s.name AS service_name
            FROM import_job_services ijs
            INNER JOIN services s ON s.id = ijs.service_id
            INNER JOIN containers c ON c.id = ijs.container_id
            WHERE ijs.job_type = 'import' AND ijs.container_id IN ($containerIdsStr)
            ORDER BY ijs.id ASC
        ";
        $result = $conn->query($servicesQuery);
        $selected_services = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        $selected_services = [];
    }
    
    // Collect booking_ids from containers for notes query
    $containerBookingIds = [];
    if (!empty($containers)) {
        foreach ($containers as $c) {
            if (!empty($c['booking_id'])) {
                $containerBookingIds[] = $conn->real_escape_string($c['booking_id']);
            }
        }
    }

    // Notes - Get notes by job_type='import' and booking_id from containers
    if (!empty($containerBookingIds)) {
        $bookingIdsStr = "'" . implode("','", array_unique($containerBookingIds)) . "'";
        $notesQuery = "
            SELECT ijn.id, ijn.booking_id, ijn.endorsement_id, e.name AS endorsement_name, ijn.note
            FROM import_job_notes ijn
            INNER JOIN endorsements e ON e.id = ijn.endorsement_id
            WHERE ijn.job_type = 'import' AND ijn.booking_id IN ($bookingIdsStr)
            ORDER BY ijn.id ASC
        ";
        $result = $conn->query($notesQuery);
        $selected_notes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        $selected_notes = [];
    }

    // Documents
    $stmt = $conn->prepare("SELECT id, file_path FROM import_job_documents WHERE import_job_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Job Booking</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="/ralo/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/ralo/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        .content-wrapper {
            background: #f4f6f9;
        }

        .card {
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
        }

        .card-header {
            background: #17a2b8;
            color: #fff;
            font-weight: 600;
        }

        .card-primary .card-header {
            background: #17a2b8;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
        }

        .form-control {
            border: 0;
            border-bottom: 1px solid #dfe6ee;
            border-radius: 0;
            background: transparent;
            padding-left: 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-bottom-color: #17a2b8;
        }

        /* Select2 styling to match image */
        .select2-container--bootstrap4 .select2-selection {
            border: 0 !important;
            border-bottom: 1px solid #dfe6ee !important;
            border-radius: 0 !important;
            background: transparent !important;
            padding-left: 0 !important;
            min-height: 38px;
        }

        .select2-container--bootstrap4 .select2-selection:focus,
        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border-bottom-color: #17a2b8 !important;
            box-shadow: none !important;
        }

        .select2-container--bootstrap4 .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 38px;
        }

        .select2-container--bootstrap4 .select2-selection__arrow {
            height: 36px;
            right: 5px;
        }

        .select2-container--bootstrap4 .select2-dropdown {
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
        }

        .select2-container--bootstrap4 .select2-results__option--highlighted {
            background-color: #f0f0f0 !important;
            color: #007bff !important;
        }

        .upload-dropzone {
            border: 2px dashed #6cb6ff;
            border-radius: 6px;
            padding: 26px 12px;
            text-align: center;
            color: #6cb6ff;
            cursor: pointer;
            background: #f8fbff;
        }

        .upload-dropzone.dragover {
            background: #eef6ff;
            border-color: #2e8cff;
            color: #2e8cff;
        }

        .upload-dropzone .icon {
            font-size: 44px;
            margin-bottom: 8px;
        }

        .section-title {
            color: #6cb6ff;
            font-weight: 700;
            margin: 0;
        }

        .ghost-table {
            display: none;
        }

        .ghost-table.show {
            display: table;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include('top-navbar.php'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include('left-navbar.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <h4>Import Job Booking</h4>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="import-job-bookings-store.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$booking['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <!-- LEFT: Customer Details -->
                        <div class="col-md-3">
                            <div class="card card-primary">
                                <div class="card-header">Customer Details</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Customer Name / Customer Code *</label>
                                        <select name="customer_id" id="customer_id" class="form-control" required>
                                            <option value="">Select</option>
                                            <?php foreach ($customers as $c): 
                                                // Format: "Name (Name - CODE)" or just "Name" if no code
                                                $display_name = $c['name'];
                                                if (!empty($c['code']) && !empty($c['name'])) {
                                                    $display_name = $c['name'] . ' (' . $c['name'] . ' - ' . $c['code'] . ')';
                                                }
                                            ?>
                                                <option value="<?php echo (int)$c['id']; ?>" data-name="<?php echo htmlspecialchars($c['name']); ?>" data-code="<?php echo htmlspecialchars($c['code'] ?? ''); ?>" <?php echo ($edit && (int)$booking['customer_id'] === (int)$c['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($display_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group mt-4">
                                        <label>Document Received Date And Time *</label>
                                        <input type="datetime-local" name="document_received_at" class="form-control" required
                                            value="<?php echo $edit ? htmlspecialchars(str_replace(' ', 'T', (string)($booking['document_received_at'] ?? ''))) : ''; ?>">
                                    </div>

                                    <div class="form-group mt-4">
                                        <label>Upload Documents</label>
                                        <input id="document_files" name="document_files[]" type="file" class="d-none" multiple
                                            accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx">
                                        <div id="uploadDropzone" class="upload-dropzone">
                                            <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                            <div>Drag &amp; Drop or click here to upload the files</div>
                                            <div class="text-muted text-sm mt-2" id="uploadFilename">No files selected</div>
                                        </div>

                                        <table class="table table-sm mt-3 ghost-table" id="documents_table">
                                            <thead>
                                                <tr>
                                                    <th>Document</th>
                                                    <th style="width:70px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($documents as $doc): ?>
                                                    <tr data-doc-id="<?php echo (int)$doc['id']; ?>">
                                                        <td>
                                                            <?php echo htmlspecialchars($doc['file_path']); ?>
                                                            <input type="hidden" name="keep_document_ids[]" value="<?php echo (int)$doc['id']; ?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-danger remove-doc">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <div id="removed_docs_inputs"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Container Details -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header">Container Details</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Booking ID *</label>
                                            <select id="c_booking_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($job_bookings as $jb): ?>
                                                    <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>"><?php echo htmlspecialchars($jb['booking_id']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Reference *</label>
                                            <input type="text" id="c_reference" class="form-control" placeholder="Reference">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Container *</label>
                                            <input type="text" id="c_container_no" class="form-control" placeholder="Container No">
                                        </div>
                                        <div class="col-md-3">
                                            <label>ISO Code *</label>
                                            <select id="c_iso_code_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($iso_codes as $i): ?>
                                                    <option value="<?php echo (int)$i['id']; ?>"><?php echo htmlspecialchars($i['code']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Weight *</label>
                                            <input type="number" step="0.01" min="0" id="c_weight" class="form-control" placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <label><i class="fas fa-map-marker-alt text-info mr-1"></i> From *</label>
                                            <input type="text" id="c_from_location" class="form-control" placeholder="From">
                                        </div>
                                        <div class="col-md-4">
                                            <label><i class="fas fa-map-marker-alt text-info mr-1"></i> To *</label>
                                            <input type="text" id="c_to_location" class="form-control" placeholder="To">
                                        </div>
                                        <div class="col-md-4">
                                            <label><i class="fas fa-map-marker-alt text-info mr-1"></i> Return To</label>
                                            <input type="text" id="c_return_to" class="form-control" placeholder="Return To">
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-3">
                                            <label>Customer Location ...</label>
                                            <input type="text" id="c_customer_location" class="form-control" placeholder="Customer Location">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Door</label>
                                            <select id="c_door_type_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($door_types as $d): ?>
                                                    <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Security Check</label>
                                            <input type="text" id="c_security_check" class="form-control" placeholder="Security Check">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Random Number</label>
                                            <input type="text" id="c_random_number" class="form-control" placeholder="Random Number">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Release/ECN Numb...</label>
                                            <input type="text" id="c_release_ecn_number" class="form-control" placeholder="Release/ECN">
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-2">
                                            <label>Port Pin No.</label>
                                            <input type="text" id="c_port_pin_no" class="form-control" placeholder="Port Pin No.">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Available</label>
                                            <input type="date" id="c_available_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label>VB Slot Date</label>
                                            <input type="date" id="c_vb_slot_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Demurrage Date</label>
                                            <input type="date" id="c_demurrage_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Detention Days *</label>
                                            <input type="number" id="c_detention_days" class="form-control" min="0" value="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Shipping</label>
                                            <select id="c_shipping_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($shippings as $s): ?>
                                                    <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        </div>

                                    <div class="row mt-4">
                                        <div class="col-md-3">
                                            <label>Vessel</label>
                                            <select id="c_vessel_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($vessels as $v): ?>
                                                    <option value="<?php echo (int)$v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Voyage</label>
                                            <input type="text" id="c_voyage" class="form-control" placeholder="Voyage">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Options</label><br>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check mr-4">
                                                    <input class="form-check-input" type="checkbox" id="c_xray">
                                                    <label class="form-check-label" for="c_xray">XRay</label>
                                                </div>
                                                <div class="form-check mr-4">
                                                    <input class="form-check-input" type="checkbox" id="c_dgs">
                                                    <label class="form-check-label" for="c_dgs">DGS</label>
                                                </div>
                                                <div class="form-check mr-4">
                                                    <input class="form-check-input" type="checkbox" id="c_live_ul">
                                                    <label class="form-check-label" for="c_live_ul">Live UL</label>
                                        </div>

                                                <div class="ml-4 pl-3" style="border-left:2px solid #e9ecef;">
                                                    <label class="mr-3 mb-0">Hold</label>
                                                    <div class="form-check form-check-inline mr-3">
                                                        <input class="form-check-input" type="checkbox" id="c_hold_sh">
                                                        <label class="form-check-label" for="c_hold_sh">SH</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mr-3">
                                                        <input class="form-check-input" type="checkbox" id="c_hold_customs">
                                                        <label class="form-check-label" for="c_hold_customs">Customs</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="c_hold_mpi">
                                                        <label class="form-check-label" for="c_hold_mpi">MPI</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right mt-4">
                                        <button type="button" class="btn btn-light" id="container_clear_btn">Clear</button>
                                        <button type="button" class="btn btn-secondary" id="container_add_btn" disabled>Add</button>
                                    </div>

                                    <table class="table table-sm mt-3 ghost-table" id="containers_table">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;">#</th>
                                                <th>Booking ID</th>
                                                <th>Reference</th>
                                                <th>Container</th>
                                                <th>ISO</th>
                                                <th>Weight</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th style="width:70px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($containers as $idx => $c): ?>
                                                <tr>
                                                    <td><?php echo $idx + 1; ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['booking_id'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][booking_id]" value="<?php echo htmlspecialchars($c['booking_id'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['reference'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][reference]" value="<?php echo htmlspecialchars($c['reference'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['container_no'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][container_no]" value="<?php echo htmlspecialchars($c['container_no'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['iso_code_id'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][iso_code_id]" value="<?php echo (int)($c['iso_code_id'] ?? 0); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['weight'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][weight]" value="<?php echo htmlspecialchars($c['weight'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['from_location'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][from_location]" value="<?php echo htmlspecialchars($c['from_location'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($c['to_location'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][to_location]" value="<?php echo htmlspecialchars($c['to_location'] ?? ''); ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button>
                                                    </td>

                                                    <!-- hidden remaining fields -->
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][return_to]" value="<?php echo htmlspecialchars($c['return_to'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][customer_location]" value="<?php echo htmlspecialchars($c['customer_location'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][door_type_id]" value="<?php echo (int)($c['door_type_id'] ?? 0); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][security_check]" value="<?php echo htmlspecialchars($c['security_check'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][random_number]" value="<?php echo htmlspecialchars($c['random_number'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][release_ecn_number]" value="<?php echo htmlspecialchars($c['release_ecn_number'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][port_pin_no]" value="<?php echo htmlspecialchars($c['port_pin_no'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][available_date]" value="<?php echo htmlspecialchars($c['available_date'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][vb_slot_date]" value="<?php echo htmlspecialchars($c['vb_slot_date'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][demurrage_date]" value="<?php echo htmlspecialchars($c['demurrage_date'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][detention_days]" value="<?php echo (int)($c['detention_days'] ?? 0); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][shipping_id]" value="<?php echo (int)($c['shipping_id'] ?? 0); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][vessel_id]" value="<?php echo (int)($c['vessel_id'] ?? 0); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][ship_type_id]" value="<?php echo (int)($c['ship_type_id'] ?? 0); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][voyage]" value="<?php echo htmlspecialchars($c['voyage'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][xray]" value="<?php echo !empty($c['xray']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][dgs]" value="<?php echo !empty($c['dgs']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][live_ul]" value="<?php echo !empty($c['live_ul']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][hold_sh]" value="<?php echo !empty($c['hold_sh']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][hold_customs]" value="<?php echo !empty($c['hold_customs']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][hold_mpi]" value="<?php echo !empty($c['hold_mpi']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][job_type]" value="import">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][job_id]" value="<?php echo (int)$id; ?>">
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <hr class="my-4">

                                    <!-- Additional Services -->
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="section-title">Additional Services</div>
                                        </div>
                                        <div class="col-md-2" style="display: none;">
                                            <label>Booking ID *</label>
                                            <select id="service_booking_id_select" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($job_bookings as $jb): ?>
                                                    <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>"><?php echo htmlspecialchars($jb['booking_id']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label>Service *</label>
                                            <select id="service_select" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($services as $srv): ?>
                                                    <option value="<?php echo (int)$srv['id']; ?>"><?php echo htmlspecialchars($srv['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button type="button" class="btn btn-light" id="service_add_btn" disabled>Add</button>
                                            <button type="button" class="btn btn-light" id="service_clear_btn">Clear</button>
                                        </div>
                                    </div>

                                    <table class="table table-sm mt-3 ghost-table" id="services_table">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;">#</th>
                                                <th>Booking ID</th>
                                                <th>Service</th>
                                                <th style="width:70px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($selected_services as $idx => $srv): ?>
                                                <tr data-service-id="<?php echo (int)$srv['service_id']; ?>">
                                                    <td><?php echo $idx + 1; ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($srv['booking_id'] ?? ''); ?>
                                                        <input type="hidden" name="service_booking_ids[]" value="<?php echo htmlspecialchars($srv['booking_id'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($srv['service_name']); ?>
                                                        <input type="hidden" name="service_ids[]" value="<?php echo (int)$srv['service_id']; ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-service"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <hr class="my-4">

                                    <!-- Add Notes -->
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="section-title">Add Notes</div>
                                        </div>
                                        <div class="col-md-2" style="display: none;">
                                            <label>Booking ID *</label>
                                            <select id="booking_id_select" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($job_bookings as $jb): ?>
                                                    <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>"><?php echo htmlspecialchars($jb['booking_id']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Endorsement *</label>
                                            <select id="endorsement_select" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($endorsements as $e): ?>
                                                    <option value="<?php echo (int)$e['id']; ?>"><?php echo htmlspecialchars($e['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Note</label>
                                            <input type="text" id="note_input" class="form-control" placeholder="Note">
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button type="button" class="btn btn-light" id="note_add_btn" disabled>Add</button>
                                            <button type="button" class="btn btn-light" id="note_clear_btn">Clear</button>
                                        </div>
                                    </div>

                                    <table class="table table-sm mt-3 ghost-table" id="notes_table">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;">#</th>
                                                <th>Booking ID</th>
                                                <th>Endorsement</th>
                                                <th>Note</th>
                                                <th style="width:70px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($selected_notes as $idx => $n): ?>
                                                <tr data-endorsement-id="<?php echo (int)$n['endorsement_id']; ?>">
                                                    <td><?php echo $idx + 1; ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($n['booking_id'] ?? ''); ?>
                                                        <input type="hidden" name="booking_ids[]" value="<?php echo htmlspecialchars($n['booking_id'] ?? ''); ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($n['endorsement_name']); ?>
                                                        <input type="hidden" name="endorsement_ids[]" value="<?php echo (int)$n['endorsement_id']; ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($n['note'] ?? ''); ?>
                                                        <input type="hidden" name="notes[]" value="<?php echo htmlspecialchars($n['note'] ?? ''); ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-note"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <div class="text-right mt-4">
                                        <a href="import-job-bookings-list.php" class="btn btn-light">Clear</a>
                                        <button type="submit" class="btn btn-primary" id="save_btn" disabled>Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 3.2.0
        </div>
        <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
        </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="/ralo/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="/ralo/plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/ralo/dist/js/adminlte.min.js"></script>

    <script>
        (function () {
            const dropzone = document.getElementById('uploadDropzone');
            const fileInput = document.getElementById('document_files');
            const filenameEl = document.getElementById('uploadFilename');
            const docsTable = document.getElementById('documents_table');

            function showTableIfHasRows(tbl) {
                if (!tbl) return;
                const tbody = tbl.querySelector('tbody');
                const hasRows = tbody && tbody.querySelectorAll('tr').length > 0;
                tbl.classList.toggle('show', !!hasRows);
            }

            function escapeHtml(str) {
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            // Upload UI
            function setUploadText(files) {
                if (!filenameEl) return;
                if (!files || !files.length) {
                    filenameEl.textContent = 'No files selected';
                    return;
                }
                filenameEl.textContent = files.length === 1 ? files[0].name : `${files.length} files selected`;
            }

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', () => fileInput.click());
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                });
                dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        setUploadText([...e.dataTransfer.files]);
                    }
                });
                fileInput.addEventListener('change', () => {
                    setUploadText(fileInput.files ? [...fileInput.files] : []);
                });
            }

            // Existing document removal -> removed ids
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-doc');
                if (!btn) return;
                const tr = btn.closest('tr');
                const docId = tr ? tr.getAttribute('data-doc-id') : null;
                if (docId) {
                    const removedWrap = document.getElementById('removed_docs_inputs');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_document_ids[]';
                    input.value = docId;
                    removedWrap.appendChild(input);
                }
                if (tr) tr.remove();
                showTableIfHasRows(docsTable);
            });

            // Containers dynamic
            const containerAddBtn = document.getElementById('container_add_btn');
            const containerClearBtn = document.getElementById('container_clear_btn');
            const containersTable = document.getElementById('containers_table');
            const containersTbody = containersTable ? containersTable.querySelector('tbody') : null;

            const requiredContainerIds = ['c_booking_id', 'c_reference', 'c_container_no', 'c_iso_code_id', 'c_weight', 'c_from_location', 'c_to_location'];
            const allContainerIds = [
                'c_booking_id', 'c_reference', 'c_container_no', 'c_iso_code_id', 'c_weight',
                'c_from_location', 'c_to_location', 'c_return_to',
                'c_customer_location', 'c_door_type_id', 'c_security_check', 'c_random_number',
                'c_release_ecn_number', 'c_port_pin_no', 'c_available_date', 'c_vb_slot_date',
                'c_demurrage_date', 'c_detention_days', 'c_shipping_id', 'c_vessel_id', 'c_voyage',
                'c_xray', 'c_dgs', 'c_live_ul', 'c_hold_sh', 'c_hold_customs', 'c_hold_mpi'
            ];

            function getVal(id) {
                const el = document.getElementById(id);
                if (!el) return '';
                if (el.type === 'checkbox') return el.checked ? 1 : 0;
                return (el.value || '').trim();
            }

            function setVal(id, v) {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = !!v;
                else el.value = v;
            }

            function canAddContainer() {
                return requiredContainerIds.every((id) => !!getVal(id));
            }

            function renumber(tbody) {
                if (!tbody) return;
                [...tbody.querySelectorAll('tr')].forEach((tr, idx) => {
                    const td = tr.querySelector('td');
                    if (td) td.textContent = idx + 1;
                    // rename containers[index] hidden inputs
                    const inputs = tr.querySelectorAll('input[name^="containers["]');
                    inputs.forEach((inp) => {
                        const name = inp.getAttribute('name');
                        if (!name) return;
                        inp.setAttribute('name', name.replace(/containers\[\d+\]/, `containers[${idx}]`));
                    });
                });
            }

            function syncButtons() {
                if (containerAddBtn) containerAddBtn.disabled = !canAddContainer();

                const serviceBookingIdSelect = document.getElementById('service_booking_id_select');
                const serviceSelect = document.getElementById('service_select');
                const serviceAddBtn = document.getElementById('service_add_btn');
                if (serviceAddBtn) serviceAddBtn.disabled = !(serviceBookingIdSelect && serviceBookingIdSelect.value && serviceSelect && serviceSelect.value);

                const bookingIdSelect = document.getElementById('booking_id_select');
                const endorsementSelect = document.getElementById('endorsement_select');
                const noteAddBtn = document.getElementById('note_add_btn');
                if (noteAddBtn) noteAddBtn.disabled = !(bookingIdSelect && bookingIdSelect.value && endorsementSelect && endorsementSelect.value);

                const saveBtn = document.getElementById('save_btn');
                const customerId = (document.querySelector('select[name="customer_id"]')?.value || '').trim();
                const docRecv = (document.querySelector('input[name="document_received_at"]')?.value || '').trim();
                const hasContainer = containersTbody && containersTbody.querySelectorAll('tr').length > 0;
                if (saveBtn) saveBtn.disabled = !(customerId && docRecv && hasContainer);
            }

            allContainerIds.forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            ['customer_id', 'document_received_at'].forEach((name) => {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            if (containerClearBtn) {
                containerClearBtn.addEventListener('click', () => {
                    setVal('c_booking_id', '');
                    setVal('c_reference', '');
                    setVal('c_container_no', '');
                    setVal('c_iso_code_id', '');
                    setVal('c_weight', '');
                    setVal('c_from_location', '');
                    setVal('c_to_location', '');
                    setVal('c_return_to', '');
                    setVal('c_customer_location', '');
                    setVal('c_door_type_id', '');
                    setVal('c_security_check', '');
                    setVal('c_random_number', '');
                    setVal('c_release_ecn_number', '');
                    setVal('c_port_pin_no', '');
                    setVal('c_available_date', '');
                    setVal('c_vb_slot_date', '');
                    setVal('c_demurrage_date', '');
                    setVal('c_detention_days', '0');
                    setVal('c_shipping_id', '');
                    setVal('c_vessel_id', '');
                    setVal('c_voyage', '');
                    setVal('c_xray', 0);
                    setVal('c_dgs', 0);
                    setVal('c_live_ul', 0);
                    setVal('c_hold_sh', 0);
                    setVal('c_hold_customs', 0);
                    setVal('c_hold_mpi', 0);
                    syncButtons();
                });
            }

            if (containerAddBtn && containersTbody) {
                containerAddBtn.addEventListener('click', () => {
                    if (!canAddContainer()) return;

                    const idx = containersTbody.querySelectorAll('tr').length;
                    const bookingId = getVal('c_booking_id');
                    const bookingIdText = document.querySelector(`#c_booking_id option[value="${bookingId}"]`)?.textContent || '';
                    const isoId = getVal('c_iso_code_id');
                    const isoText = document.querySelector(`#c_iso_code_id option[value="${isoId}"]`)?.textContent || '';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bookingIdText)}<input type="hidden" name="containers[${idx}][booking_id]" value="${escapeHtml(bookingId)}"></td>
                        <td>${escapeHtml(getVal('c_reference'))}<input type="hidden" name="containers[${idx}][reference]" value="${escapeHtml(getVal('c_reference'))}"></td>
                        <td>${escapeHtml(getVal('c_container_no'))}<input type="hidden" name="containers[${idx}][container_no]" value="${escapeHtml(getVal('c_container_no'))}"></td>
                        <td>${escapeHtml(isoText)}<input type="hidden" name="containers[${idx}][iso_code_id]" value="${escapeHtml(isoId)}"></td>
                        <td>${escapeHtml(getVal('c_weight'))}<input type="hidden" name="containers[${idx}][weight]" value="${escapeHtml(getVal('c_weight'))}"></td>
                        <td>${escapeHtml(getVal('c_from_location'))}<input type="hidden" name="containers[${idx}][from_location]" value="${escapeHtml(getVal('c_from_location'))}"></td>
                        <td>${escapeHtml(getVal('c_to_location'))}<input type="hidden" name="containers[${idx}][to_location]" value="${escapeHtml(getVal('c_to_location'))}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button></td>

                        <input type="hidden" name="containers[${idx}][return_to]" value="${escapeHtml(getVal('c_return_to'))}">
                        <input type="hidden" name="containers[${idx}][customer_location]" value="${escapeHtml(getVal('c_customer_location'))}">
                        <input type="hidden" name="containers[${idx}][door_type_id]" value="${escapeHtml(getVal('c_door_type_id'))}">
                        <input type="hidden" name="containers[${idx}][security_check]" value="${escapeHtml(getVal('c_security_check'))}">
                        <input type="hidden" name="containers[${idx}][random_number]" value="${escapeHtml(getVal('c_random_number'))}">
                        <input type="hidden" name="containers[${idx}][release_ecn_number]" value="${escapeHtml(getVal('c_release_ecn_number'))}">
                        <input type="hidden" name="containers[${idx}][port_pin_no]" value="${escapeHtml(getVal('c_port_pin_no'))}">
                        <input type="hidden" name="containers[${idx}][available_date]" value="${escapeHtml(getVal('c_available_date'))}">
                        <input type="hidden" name="containers[${idx}][vb_slot_date]" value="${escapeHtml(getVal('c_vb_slot_date'))}">
                        <input type="hidden" name="containers[${idx}][demurrage_date]" value="${escapeHtml(getVal('c_demurrage_date'))}">
                        <input type="hidden" name="containers[${idx}][detention_days]" value="${escapeHtml(getVal('c_detention_days') || '0')}">
                        <input type="hidden" name="containers[${idx}][shipping_id]" value="${escapeHtml(getVal('c_shipping_id'))}">
                        <input type="hidden" name="containers[${idx}][vessel_id]" value="${escapeHtml(getVal('c_vessel_id'))}">
                        <input type="hidden" name="containers[${idx}][ship_type_id]" value="">
                        <input type="hidden" name="containers[${idx}][voyage]" value="${escapeHtml(getVal('c_voyage'))}">
                        <input type="hidden" name="containers[${idx}][xray]" value="${escapeHtml(getVal('c_xray'))}">
                        <input type="hidden" name="containers[${idx}][dgs]" value="${escapeHtml(getVal('c_dgs'))}">
                        <input type="hidden" name="containers[${idx}][live_ul]" value="${escapeHtml(getVal('c_live_ul'))}">
                        <input type="hidden" name="containers[${idx}][hold_sh]" value="${escapeHtml(getVal('c_hold_sh'))}">
                        <input type="hidden" name="containers[${idx}][hold_customs]" value="${escapeHtml(getVal('c_hold_customs'))}">
                        <input type="hidden" name="containers[${idx}][hold_mpi]" value="${escapeHtml(getVal('c_hold_mpi'))}">
                        <input type="hidden" name="containers[${idx}][job_type]" value="import">
                        <input type="hidden" name="containers[${idx}][job_id]" value="${(document.querySelector('input[name="id"]')?.value || '0')}">
                    `;
                    containersTbody.appendChild(row);
                    renumber(containersTbody);
                    showTableIfHasRows(containersTable);
                    containerClearBtn?.click();
                    syncButtons();
                });
            }

            // Remove container row
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-container');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                renumber(containersTbody);
                showTableIfHasRows(containersTable);
                syncButtons();
            });

            // Services dynamic
            const servicesTable = document.getElementById('services_table');
            const servicesTbody = servicesTable ? servicesTable.querySelector('tbody') : null;
            const serviceSelect = document.getElementById('service_select');
            const serviceAddBtn = document.getElementById('service_add_btn');
            const serviceClearBtn = document.getElementById('service_clear_btn');

            function renumberSimple(tbody) {
                if (!tbody) return;
                [...tbody.querySelectorAll('tr')].forEach((tr, idx) => {
                    const td = tr.querySelector('td');
                    if (td) td.textContent = idx + 1;
                });
            }

            const serviceBookingIdSelect = document.getElementById('service_booking_id_select');
            if (serviceSelect) {
                serviceSelect.addEventListener('change', syncButtons);
            }
            if (serviceBookingIdSelect) {
                serviceBookingIdSelect.addEventListener('change', syncButtons);
            }

            if (serviceAddBtn && servicesTbody && serviceSelect && serviceBookingIdSelect) {
                serviceAddBtn.addEventListener('click', () => {
                    const bid = (serviceBookingIdSelect.value || '').trim();
                    const sid = (serviceSelect.value || '').trim();
                    if (!bid || !sid) return;
                    if (servicesTbody.querySelector(`tr[data-service-id="${sid}"]`)) return;
                    const bidText = serviceBookingIdSelect.options[serviceBookingIdSelect.selectedIndex].textContent || '';
                    const name = serviceSelect.options[serviceSelect.selectedIndex].textContent || '';

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-service-id', sid);
                    tr.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bidText)}<input type="hidden" name="service_booking_ids[]" value="${escapeHtml(bid)}"></td>
                        <td>${escapeHtml(name)}<input type="hidden" name="service_ids[]" value="${escapeHtml(sid)}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-service"><i class="fas fa-trash"></i></button></td>
                    `;
                    servicesTbody.appendChild(tr);
                    renumberSimple(servicesTbody);
                    showTableIfHasRows(servicesTable);
                    // Don't clear booking_id as it's auto-synced from container
                    // serviceBookingIdSelect.value = '';
                    serviceSelect.value = '';
                    syncButtons();
                });
            }

            if (serviceClearBtn && servicesTbody && serviceSelect && serviceBookingIdSelect) {
                serviceClearBtn.addEventListener('click', () => {
                    servicesTbody.innerHTML = '';
                    // Don't clear booking_id as it's auto-synced from container
                    // serviceBookingIdSelect.value = '';
                    serviceSelect.value = '';
                    showTableIfHasRows(servicesTable);
                    syncButtons();
                });
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-service');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                renumberSimple(servicesTbody);
                showTableIfHasRows(servicesTable);
            });

            // Notes dynamic
            const notesTable = document.getElementById('notes_table');
            const notesTbody = notesTable ? notesTable.querySelector('tbody') : null;
            const bookingIdSelect = document.getElementById('booking_id_select');
            const endorsementSelect = document.getElementById('endorsement_select');
            const noteInput = document.getElementById('note_input');
            const noteAddBtn = document.getElementById('note_add_btn');
            const noteClearBtn = document.getElementById('note_clear_btn');

            if (bookingIdSelect) bookingIdSelect.addEventListener('change', syncButtons);
            if (endorsementSelect) endorsementSelect.addEventListener('change', syncButtons);
            if (noteInput) noteInput.addEventListener('input', syncButtons);

            if (noteAddBtn && notesTbody && bookingIdSelect && endorsementSelect) {
                noteAddBtn.addEventListener('click', () => {
                    const bid = (bookingIdSelect.value || '').trim();
                    const eid = (endorsementSelect.value || '').trim();
                    if (!bid || !eid) return;
                    const bidText = bookingIdSelect.options[bookingIdSelect.selectedIndex].textContent || '';
                    const ename = endorsementSelect.options[endorsementSelect.selectedIndex].textContent || '';
                    const ntext = (noteInput?.value || '').trim();

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-endorsement-id', eid);
                    tr.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bidText)}<input type="hidden" name="booking_ids[]" value="${escapeHtml(bid)}"></td>
                        <td>${escapeHtml(ename)}<input type="hidden" name="endorsement_ids[]" value="${escapeHtml(eid)}"></td>
                        <td>${escapeHtml(ntext)}<input type="hidden" name="notes[]" value="${escapeHtml(ntext)}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-note"><i class="fas fa-trash"></i></button></td>
                    `;
                    notesTbody.appendChild(tr);
                    renumberSimple(notesTbody);
                    showTableIfHasRows(notesTable);
                    // Don't clear booking_id as it's auto-synced from container
                    // bookingIdSelect.value = '';
                    endorsementSelect.value = '';
                    if (noteInput) noteInput.value = '';
                    syncButtons();
                });
            }

            if (noteClearBtn && notesTbody && bookingIdSelect && endorsementSelect && noteInput) {
                noteClearBtn.addEventListener('click', () => {
                    notesTbody.innerHTML = '';
                    // Don't clear booking_id as it's auto-synced from container
                    // bookingIdSelect.value = '';
                    endorsementSelect.value = '';
                    noteInput.value = '';
                    showTableIfHasRows(notesTable);
                    syncButtons();
                });
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-note');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                renumberSimple(notesTbody);
                showTableIfHasRows(notesTable);
            });

            // initial state
            showTableIfHasRows(containersTable);
            showTableIfHasRows(servicesTable);
            showTableIfHasRows(notesTable);
            showTableIfHasRows(docsTable);
            syncButtons();
        })();

        // Initialize Select2 for all searchable dropdowns
        $(document).ready(function() {
            // Customer dropdown - searchable on click
            $('#customer_id').select2({
                placeholder: "Customer Name / Customer Code",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0, // Always show search box
                dropdownAutoWidth: true,
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    },
                    inputTooShort: function() {
                        return "Please enter more characters";
                    }
                }
            });

            // Booking ID dropdowns - searchable on click
            $('#c_booking_id').select2({
                placeholder: "Select Booking ID",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0, // Always show search box
                dropdownAutoWidth: true,
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    },
                    inputTooShort: function() {
                        return "Please enter more characters";
                    }
                }
            });

            $('#service_booking_id_select').select2({
                placeholder: "Select Booking ID",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0, // Always show search box
                dropdownAutoWidth: true,
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    },
                    inputTooShort: function() {
                        return "Please enter more characters";
                    }
                }
            });

            $('#booking_id_select').select2({
                placeholder: "Select Booking ID",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0, // Always show search box
                dropdownAutoWidth: true,
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    },
                    inputTooShort: function() {
                        return "Please enter more characters";
                    }
                }
            });

            // Auto-sync booking_id from container to services and notes
            $('#c_booking_id').on('change', function() {
                const selectedBookingId = $(this).val();
                if (selectedBookingId) {
                    // Automatically set the same booking_id in services dropdown
                    $('#service_booking_id_select').val(selectedBookingId).trigger('change');
                    // Automatically set the same booking_id in notes dropdown
                    $('#booking_id_select').val(selectedBookingId).trigger('change');
                } else {
                    // If cleared, also clear services and notes
                    $('#service_booking_id_select').val('').trigger('change');
                    $('#booking_id_select').val('').trigger('change');
                }
            });
        });
    </script>
</body>

</html>
