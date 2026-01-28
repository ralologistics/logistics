<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$job = null;
$containers = [];
$selected_services = [];
$selected_notes = [];

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
$job_bookings_full = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM export_job_bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $job = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Containers
    $stmt = $conn->prepare("SELECT * FROM containers WHERE job_type = 'export' AND job_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $containers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Services - Get services by job_type='export' and container_id from containers
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
    
    if (!empty($containerIds)) {
        $containerIdsStr = implode(',', array_map('intval', $containerIds));
        $servicesQuery = "
            SELECT DISTINCT ijs.service_id, ijs.container_id, c.booking_id, s.name AS service_name
            FROM import_job_services ijs
            INNER JOIN services s ON s.id = ijs.service_id
            INNER JOIN containers c ON c.id = ijs.container_id
            WHERE ijs.job_type = 'export' AND ijs.container_id IN ($containerIdsStr)
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

    // Notes - Get notes by job_type='export' and booking_id from containers
    if (!empty($containerBookingIds)) {
        $bookingIdsStr = "'" . implode("','", array_unique($containerBookingIds)) . "'";
        $notesQuery = "
            SELECT ijn.id, ijn.booking_id, ijn.endorsement_id, e.name AS endorsement_name, ijn.note
            FROM import_job_notes ijn
            INNER JOIN endorsements e ON e.id = ijn.endorsement_id
            WHERE ijn.job_type = 'export' AND ijn.booking_id IN ($bookingIdsStr)
            ORDER BY ijn.id ASC
        ";
        $result = $conn->query($notesQuery);
        $selected_notes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        $selected_notes = [];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Export Job Booking</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
    
    <style>
        .ghost-table {
            display: none;
        }

        .ghost-table.show {
            display: table;
        }

        .section-title {
            color: #6cb6ff;
            font-weight: 700;
            margin: 0;
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
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Export Job Booking</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="export-job-list.php">Export Jobs</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="export-job-store.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <!-- Left column: Customer Details -->
                            <div class="col-lg-3">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">Customer Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="customer_id">
                                                Customer Name / Customer Code <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="customer_id" name="customer_id" required>
                                                <option value="">Select Customer</option>
                                                <?php foreach ($customers as $c): 
                                                    $display_name = $c['name'];
                                                    if (!empty($c['code']) && !empty($c['name'])) {
                                                        $display_name = $c['name'] . ' (' . $c['name'] . ' - ' . $c['code'] . ')';
                                                    }
                                                ?>
                                                    <option value="<?php echo (int)$c['id']; ?>" <?php echo ($edit && $job['customer_id'] == $c['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($display_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="booking_id">
                                                Booking <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="booking_id" name="booking_id" required>
                                                <option value="">Select Booking</option>
                                                <?php foreach ($job_bookings_full as $jb): ?>
                                                    <option value="<?php echo (int)$jb['id']; ?>" <?php echo ($edit && $job['booking_id'] == $jb['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($jb['booking_id']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="document_received_at">
                                                Document Received Date And Time <span class="text-danger">*</span>
                                            </label>
                                            <input type="datetime-local"
                                                class="form-control"
                                                id="document_received_at"
                                                name="document_received_at"
                                                value="<?php echo $edit ? date('Y-m-d\TH:i', strtotime($job['document_received_at'])) : ''; ?>"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="shipping__id">Shipping</label>
                                            <select class="form-control" id="shipping__id" name="shipping__id">
                                                <option value="">Select Shipping</option>
                                                <?php foreach ($shippings as $shipping): ?>
                                                    <option value="<?php echo htmlspecialchars($shipping['id']); ?>" <?php echo $edit && $job['shipping__id'] == $shipping['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($shipping['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="vessel_id">
                                                Vessel <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="vessel_id" name="vessel_id" required>
                                                <option value="">Select Vessel</option>
                                                <?php foreach ($vessels as $vessel): ?>
                                                    <option value="<?php echo htmlspecialchars($vessel['id']); ?>" <?php echo $edit && $job['vessel_id'] == $vessel['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($vessel['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="voyage">Voyage</label>
                                            <input type="text"
                                                class="form-control"
                                                id="voyage"
                                                name="voyage"
                                                placeholder="Enter voyage"
                                                value="<?php echo $edit ? htmlspecialchars($job['voyage']) : ''; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="from_location">
                                                <i class="fas fa-map-marker-alt text-info mr-1"></i> From <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                id="from_location"
                                                name="from_location"
                                                placeholder="From"
                                                value="<?php echo $edit ? htmlspecialchars($job['from_location']) : ''; ?>"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="to_location">
                                                <i class="fas fa-map-marker-alt text-info mr-1"></i> To <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                id="to_location"
                                                name="to_location"
                                                placeholder="To"
                                                value="<?php echo $edit ? htmlspecialchars($job['to_location']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right column: Container Details -->
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">Container Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label>Booking ID *</label>
                                                <select id="c_booking_id" class="form-control">
                                                    <option value="">Select</option>
                                                    <?php foreach ($job_bookings as $jb): ?>
                                                        <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>">
                                                            <?php echo htmlspecialchars($jb['booking_id']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Reference *</label>
                                                <input type="text" id="c_reference" class="form-control" placeholder="Reference">
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
                                            <div class="col-md-3">
                                                <label>No. Of Containers *</label>
                                                <input type="number" min="1" id="c_no_of_containers" class="form-control" placeholder="1" value="1">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-2">
                                                <label class="d-block">Ready Now</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="c_ready_now">
                                                    <label class="form-check-label" for="c_ready_now">Ready Now</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Available Date</label>
                                                <input type="date" id="c_available_date" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Cut off Date *</label>
                                                <input type="date" id="c_cut_off_date" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Door *</label>
                                                <select id="c_door_type_id" class="form-control">
                                                    <option value="">Select</option>
                                                    <?php foreach ($door_types as $d): ?>
                                                        <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Grid position</label>
                                                <input type="text" id="c_grid_position" class="form-control" placeholder="Grid position" maxlength="50">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <label>Random Number</label>
                                                <input type="text" id="c_random_number" class="form-control" placeholder="Random number" maxlength="100">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Release/ECN Number *</label>
                                                <input type="text" id="c_release_ecn_number" class="form-control" placeholder="Release/ECN Number" maxlength="100">
                                            </div>
                                            <div class="col-md-3">
                                                <label><i class="fas fa-map-marker-alt text-info mr-1"></i> Port *</label>
                                                <input type="text" id="c_port_pin_no" class="form-control" placeholder="Port" maxlength="100">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="d-block">Options</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="c_xray">
                                                    <label class="form-check-label" for="c_xray">XRay</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="c_dgs">
                                                    <label class="form-check-label" for="c_dgs">DGS</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="c_live_ul">
                                                    <label class="form-check-label" for="c_live_ul">Live Load</label>
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
                                                    <th>Reference</th>
                                                    <th>ISO Code</th>
                                                    <th>Weight</th>
                                                    <th>No. Of Containers</th>
                                                    <th>Cut off Date</th>
                                                    <th>Grid Position</th>
                                                    <th>Door</th>
                                                    <th>Port</th>
                                                    <th style="width:70px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($containers as $idx => $c): ?>
                                                    <tr>
                                                        <td><?php echo $idx + 1; ?></td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['reference'] ?? ''); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][reference]" value="<?php echo htmlspecialchars($c['reference'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $isoId = (int)($c['iso_code_id'] ?? 0);
                                                            $isoText = '';
                                                            foreach ($iso_codes as $iso) {
                                                                if ((int)$iso['id'] === $isoId) {
                                                                    $isoText = htmlspecialchars($iso['code']);
                                                                    break;
                                                                }
                                                            }
                                                            echo $isoText;
                                                            ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][iso_code_id]" value="<?php echo $isoId; ?>">
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['weight'] ?? ''); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][weight]" value="<?php echo htmlspecialchars($c['weight'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['no_of_containers'] ?? '1'); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][no_of_containers]" value="<?php echo htmlspecialchars($c['no_of_containers'] ?? '1'); ?>">
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['cut_off_date'] ?? ''); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][cut_off_date]" value="<?php echo htmlspecialchars($c['cut_off_date'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['grid_position'] ?? ''); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][grid_position]" value="<?php echo htmlspecialchars($c['grid_position'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $doorId = (int)($c['door_type_id'] ?? 0);
                                                            $doorText = '';
                                                            foreach ($door_types as $door) {
                                                                if ((int)$door['id'] === $doorId) {
                                                                    $doorText = htmlspecialchars($door['name']);
                                                                    break;
                                                                }
                                                            }
                                                            echo $doorText;
                                                            ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][door_type_id]" value="<?php echo $doorId; ?>">
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($c['port_pin_no'] ?? ''); ?>
                                                            <input type="hidden" name="containers[<?php echo $idx; ?>][port_pin_no]" value="<?php echo htmlspecialchars($c['port_pin_no'] ?? ''); ?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button>
                                                        </td>

                                                        <!-- hidden remaining fields -->
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][booking_id]" value="<?php echo htmlspecialchars($c['booking_id'] ?? ''); ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][container_no]" value="<?php echo htmlspecialchars($c['container_no'] ?? ''); ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][available_date]" value="<?php echo htmlspecialchars($c['available_date'] ?? ''); ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][random_number]" value="<?php echo htmlspecialchars($c['random_number'] ?? ''); ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][release_ecn_number]" value="<?php echo htmlspecialchars($c['release_ecn_number'] ?? ''); ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][xray]" value="<?php echo !empty($c['xray']) ? 1 : 0; ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][dgs]" value="<?php echo !empty($c['dgs']) ? 1 : 0; ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][live_ul]" value="<?php echo !empty($c['live_ul']) ? 1 : 0; ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][ready_now]" value="<?php echo !empty($c['ready_now']) ? 1 : 0; ?>">
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][job_type]" value="export">
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
                                                <textarea id="note_input" class="form-control" rows="1" placeholder="Note"></textarea>
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
                                            <a href="export-job-list.php" class="btn btn-light">Clear</a>
                                            <button type="submit" class="btn btn-secondary" id="save_btn" disabled>Save</button>
                                        </div>
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
            <b>Version</b> 2.0.3
        </div>
        <strong>Copyright 2026 Navare Solutions All rights reserved.</strong>
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
    <!-- AdminLTE App -->
    <script src="/ralo/dist/js/adminlte.min.js"></script>

    <script>
        (function () {
            const containersTable = document.getElementById('containers_table');
            const containersTbody = containersTable ? containersTable.querySelector('tbody') : null;
            const containerAddBtn = document.getElementById('container_add_btn');
            const containerClearBtn = document.getElementById('container_clear_btn');

            const requiredContainerIds = ['c_booking_id', 'c_reference', 'c_iso_code_id', 'c_weight', 'c_no_of_containers', 'c_cut_off_date', 'c_door_type_id', 'c_release_ecn_number', 'c_port_pin_no'];
            const allContainerIds = [
                'c_booking_id', 'c_reference', 'c_iso_code_id', 'c_weight', 'c_no_of_containers',
                'c_ready_now', 'c_available_date', 'c_cut_off_date', 'c_door_type_id', 'c_grid_position',
                'c_random_number', 'c_release_ecn_number', 'c_port_pin_no', 'c_xray', 'c_dgs', 'c_live_ul'
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
                    const inputs = tr.querySelectorAll('input[name^="containers["]');
                    inputs.forEach((inp) => {
                        const name = inp.getAttribute('name');
                        if (!name) return;
                        inp.setAttribute('name', name.replace(/containers\[\d+\]/, `containers[${idx}]`));
                    });
                });
            }

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

            function syncButtons() {
                if (containerAddBtn) containerAddBtn.disabled = !canAddContainer();

                const serviceSelect = document.getElementById('service_select');
                const serviceAddBtn = document.getElementById('service_add_btn');
                if (serviceAddBtn) serviceAddBtn.disabled = !(serviceSelect && serviceSelect.value);

                const endorsementSelect = document.getElementById('endorsement_select');
                const noteAddBtn = document.getElementById('note_add_btn');
                if (noteAddBtn) noteAddBtn.disabled = !(endorsementSelect && endorsementSelect.value);

                const saveBtn = document.getElementById('save_btn');
                const customerId = (document.querySelector('select[name="customer_id"]')?.value || '').trim();
                const docRecv = (document.querySelector('input[name="document_received_at"]')?.value || '').trim();
                const fromLoc = (document.querySelector('input[name="from_location"]')?.value || '').trim();
                const toLoc = (document.querySelector('input[name="to_location"]')?.value || '').trim();
                const hasContainer = containersTbody && containersTbody.querySelectorAll('tr').length > 0;
                if (saveBtn) saveBtn.disabled = !(customerId && docRecv && fromLoc && toLoc && hasContainer);
            }

            allContainerIds.forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            ['customer_id', 'document_received_at', 'from_location', 'to_location'].forEach((name) => {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            if (containerClearBtn) {
                containerClearBtn.addEventListener('click', () => {
                    setVal('c_reference', '');
                    setVal('c_iso_code_id', '');
                    setVal('c_weight', '');
                    setVal('c_no_of_containers', '1');
                    setVal('c_ready_now', 0);
                    setVal('c_available_date', '');
                    setVal('c_cut_off_date', '');
                    setVal('c_door_type_id', '');
                    setVal('c_grid_position', '');
                    setVal('c_random_number', '');
                    setVal('c_release_ecn_number', '');
                    setVal('c_port_pin_no', '');
                    setVal('c_xray', 0);
                    setVal('c_dgs', 0);
                    setVal('c_live_ul', 0);
                    syncButtons();
                });
            }

            if (containerAddBtn && containersTbody) {
                containerAddBtn.addEventListener('click', () => {
                    if (!canAddContainer()) return;

                    const idx = containersTbody.querySelectorAll('tr').length;
                    const bookingId = getVal('c_booking_id') || '';
                    const isoId = getVal('c_iso_code_id');
                    const isoText = document.querySelector(`#c_iso_code_id option[value="${isoId}"]`)?.textContent || '';
                    const doorId = getVal('c_door_type_id');
                    const doorText = document.querySelector(`#c_door_type_id option[value="${doorId}"]`)?.textContent || '';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(getVal('c_reference'))}<input type="hidden" name="containers[${idx}][reference]" value="${escapeHtml(getVal('c_reference'))}"></td>
                        <td>${escapeHtml(isoText)}<input type="hidden" name="containers[${idx}][iso_code_id]" value="${escapeHtml(isoId)}"></td>
                        <td>${escapeHtml(getVal('c_weight'))}<input type="hidden" name="containers[${idx}][weight]" value="${escapeHtml(getVal('c_weight'))}"></td>
                        <td>${escapeHtml(getVal('c_no_of_containers'))}<input type="hidden" name="containers[${idx}][no_of_containers]" value="${escapeHtml(getVal('c_no_of_containers'))}"></td>
                        <td>${escapeHtml(getVal('c_cut_off_date'))}<input type="hidden" name="containers[${idx}][cut_off_date]" value="${escapeHtml(getVal('c_cut_off_date'))}"></td>
                        <td>${escapeHtml(getVal('c_grid_position'))}<input type="hidden" name="containers[${idx}][grid_position]" value="${escapeHtml(getVal('c_grid_position'))}"></td>
                        <td>${escapeHtml(doorText)}<input type="hidden" name="containers[${idx}][door_type_id]" value="${escapeHtml(doorId)}"></td>
                        <td>${escapeHtml(getVal('c_port_pin_no'))}<input type="hidden" name="containers[${idx}][port_pin_no]" value="${escapeHtml(getVal('c_port_pin_no'))}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button></td>
                        <input type="hidden" name="containers[${idx}][booking_id]" value="${escapeHtml(bookingId)}">
                        <input type="hidden" name="containers[${idx}][container_no]" value="">
                        <input type="hidden" name="containers[${idx}][available_date]" value="${escapeHtml(getVal('c_available_date'))}">
                        <input type="hidden" name="containers[${idx}][random_number]" value="${escapeHtml(getVal('c_random_number'))}">
                        <input type="hidden" name="containers[${idx}][release_ecn_number]" value="${escapeHtml(getVal('c_release_ecn_number'))}">
                        <input type="hidden" name="containers[${idx}][xray]" value="${escapeHtml(getVal('c_xray'))}">
                        <input type="hidden" name="containers[${idx}][dgs]" value="${escapeHtml(getVal('c_dgs'))}">
                        <input type="hidden" name="containers[${idx}][live_ul]" value="${escapeHtml(getVal('c_live_ul'))}">
                        <input type="hidden" name="containers[${idx}][ready_now]" value="${escapeHtml(getVal('c_ready_now'))}">
                        <input type="hidden" name="containers[${idx}][job_type]" value="export">
                        <input type="hidden" name="containers[${idx}][job_id]" value="${(document.querySelector('input[name="id"]')?.value || '0')}">
                    `;
                    containersTbody.appendChild(row);
                    renumber(containersTbody);
                    showTableIfHasRows(containersTable);
                    containerClearBtn?.click();
                    syncButtons();
                });
            }

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

            if (serviceSelect) {
                serviceSelect.addEventListener('change', syncButtons);
            }

            if (serviceAddBtn && servicesTbody && serviceSelect) {
                serviceAddBtn.addEventListener('click', () => {
                    const sid = (serviceSelect.value || '').trim();
                    if (!sid) return;
                    if (servicesTbody.querySelector(`tr[data-service-id="${sid}"]`)) return;
                    const name = serviceSelect.options[serviceSelect.selectedIndex].textContent || '';
                    const bookingId = (document.getElementById('c_booking_id')?.value || '').trim();

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-service-id', sid);
                    tr.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bookingId)}<input type="hidden" name="service_booking_ids[]" value="${escapeHtml(bookingId)}"></td>
                        <td>${escapeHtml(name)}<input type="hidden" name="service_ids[]" value="${escapeHtml(sid)}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-service"><i class="fas fa-trash"></i></button></td>
                    `;
                    servicesTbody.appendChild(tr);
                    renumberSimple(servicesTbody);
                    showTableIfHasRows(servicesTable);
                    serviceSelect.value = '';
                    syncButtons();
                });
            }

            if (serviceClearBtn) {
                serviceClearBtn.addEventListener('click', () => {
                    if (servicesTbody) servicesTbody.innerHTML = '';
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
                syncButtons();
            });

            // Notes dynamic
            const notesTable = document.getElementById('notes_table');
            const notesTbody = notesTable ? notesTable.querySelector('tbody') : null;
            const endorsementSelect = document.getElementById('endorsement_select');
            const noteInput = document.getElementById('note_input');
            const noteAddBtn = document.getElementById('note_add_btn');
            const noteClearBtn = document.getElementById('note_clear_btn');

            if (endorsementSelect) {
                endorsementSelect.addEventListener('change', syncButtons);
            }

            if (noteAddBtn && notesTbody && endorsementSelect) {
                noteAddBtn.addEventListener('click', () => {
                    const eid = (endorsementSelect.value || '').trim();
                    if (!eid) return;
                    const name = endorsementSelect.options[endorsementSelect.selectedIndex].textContent || '';
                    const note = (noteInput?.value || '').trim();
                    const bookingId = (document.getElementById('c_booking_id')?.value || '').trim();

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-endorsement-id', eid);
                    tr.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bookingId)}<input type="hidden" name="booking_ids[]" value="${escapeHtml(bookingId)}"></td>
                        <td>${escapeHtml(name)}<input type="hidden" name="endorsement_ids[]" value="${escapeHtml(eid)}"></td>
                        <td>${escapeHtml(note)}<input type="hidden" name="notes[]" value="${escapeHtml(note)}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-note"><i class="fas fa-trash"></i></button></td>
                    `;
                    notesTbody.appendChild(tr);
                    renumberSimple(notesTbody);
                    showTableIfHasRows(notesTable);
                    endorsementSelect.value = '';
                    if (noteInput) noteInput.value = '';
                    syncButtons();
                });
            }

            if (noteClearBtn) {
                noteClearBtn.addEventListener('click', () => {
                    if (notesTbody) notesTbody.innerHTML = '';
                    endorsementSelect.value = '';
                    if (noteInput) noteInput.value = '';
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
                syncButtons();
            });

            // Initialize
            syncButtons();
            showTableIfHasRows(containersTable);
            showTableIfHasRows(servicesTable);
            showTableIfHasRows(notesTable);
        })();
    </script>
</body>

</html>
