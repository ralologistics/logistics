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
$services = $conn->query("SELECT id, name FROM services WHERE status = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$endorsements = $conn->query("SELECT id, name FROM endorsements WHERE status = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$job_bookings = $conn->query("SELECT booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);

$containers = [];
$selected_services = [];
$selected_notes = [];

$default_booking_id = '';
$id = 0;

if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT s.*, j.booking_id FROM swing_job_bookings s LEFT JOIN job_bookings j ON s.job_booking_id = j.id WHERE s.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!empty($booking['booking_id'])) {
        $default_booking_id = $booking['booking_id'];
    }

    // Containers
    $stmt = $conn->prepare("SELECT * FROM containers WHERE job_type = 'swing' AND job_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $containers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Services - Get services by job_type='swing' and container_id from containers
    $containerIds = [];
    if (!empty($containers)) {
        $stmtGetContainers = $conn->prepare("SELECT id FROM containers WHERE job_type = 'swing' AND job_id = ? ORDER BY id ASC");
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
            WHERE ijs.job_type = 'swing' AND ijs.container_id IN ($containerIdsStr)
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

    // Notes - Get notes by job_type='swing' and booking_id from containers
    if (!empty($containerBookingIds)) {
        $bookingIdsStr = "'" . implode("','", array_unique($containerBookingIds)) . "'";
        $notesQuery = "
            SELECT ijn.id, ijn.booking_id, ijn.endorsement_id, e.name AS endorsement_name, ijn.note
            FROM import_job_notes ijn
            INNER JOIN endorsements e ON e.id = ijn.endorsement_id
            WHERE ijn.job_type = 'swing' AND ijn.booking_id IN ($bookingIdsStr)
            ORDER BY ijn.id ASC
        ";
        $result = $conn->query($notesQuery);
        $selected_notes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        $selected_notes = [];
    }
}

// Get full job bookings for the hidden field (needed before closing connection)
$job_bookings_full = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swing Job Booking</title>

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

        .page-title {
            color: #17a2b8;
            font-weight: 700;
            margin-bottom: 20px;
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
                    <h4 class="page-title">Swing Job Booking</h4>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="swing-job-store.php" method="POST">
                    <?php if ($edit && !empty($booking)): ?>
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
                                                <option value="<?php echo (int)$c['id']; ?>" data-name="<?php echo htmlspecialchars($c['name']); ?>" data-code="<?php echo htmlspecialchars($c['code'] ?? ''); ?>" <?php echo ($edit && !empty($booking) && (int)$booking['customer_id'] === (int)$c['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($display_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group mt-4">
                                        <label>Document Received Date And Time *</label>
                                        <div class="input-group">
                                            <input type="datetime-local" name="document_received_at" class="form-control" required
                                                value="<?php echo ($edit && !empty($booking)) ? htmlspecialchars(str_replace(' ', 'T', (string)($booking['document_received_at'] ?? ''))) : ''; ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="background: transparent; border: 0; border-bottom: 1px solid #dfe6ee; border-radius: 0; padding: 0 5px;">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4">
                                        <label><i class="fas fa-map-marker-alt text-info mr-1"></i> From *</label>
                                        <input type="text" name="from_location" class="form-control" placeholder="From" required
                                            value="<?php echo ($edit && !empty($booking)) ? htmlspecialchars($booking['from_location'] ?? '') : ''; ?>">
                                    </div>
                                    
                                    <?php if (!$edit): ?>
                                    <div class="form-group mt-4" style="display: none;">
                                        <label>Job Booking ID *</label>
                                        <select name="job_booking_id" id="job_booking_id" class="form-control">
                                            <option value="">Select</option>
                                            <?php foreach ($job_bookings_full as $jb): ?>
                                                <option value="<?php echo (int)$jb['id']; ?>" data-booking-id="<?php echo htmlspecialchars($jb['booking_id']); ?>">
                                                    <?php echo htmlspecialchars($jb['booking_id']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Container Details -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header">Container Details</div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label>Booking ID *</label>
                                            <select id="c_booking_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($job_bookings as $jb): ?>
                                                    <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>" <?php echo ($edit && $default_booking_id === $jb['booking_id']) ? 'selected' : ''; ?>>
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
                                        <div class="col-md-3">
                                            <label>Available *</label>
                                            <input type="date" id="c_available_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="d-block">Ready Now</label>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="c_ready_now">
                                                <label class="form-check-label" for="c_ready_now">Ready Now</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Door *</label>
                                            <select id="c_door_type_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($door_types as $d): ?>
                                                    <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Service Type *</label>
                                            <select id="c_service_type_id" class="form-control">
                                                <option value="">Select</option>
                                                <?php foreach ($services as $s): ?>
                                                    <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
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
                                                <th>Container</th>
                                                <th>ISO</th>
                                                <th>Weight</th>
                                                <th>Available</th>
                                                <th>Door</th>
                                                <th>Service Type</th>
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
                                                        <?php echo htmlspecialchars($c['container_no'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][container_no]" value="<?php echo htmlspecialchars($c['container_no'] ?? ''); ?>">
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
                                                        <?php echo htmlspecialchars($c['available_date'] ?? ''); ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][available_date]" value="<?php echo htmlspecialchars($c['available_date'] ?? ''); ?>">
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
                                                        <?php 
                                                        // Service type stored in container - we'll need to get it from services table
                                                        // For now, just show placeholder
                                                        echo 'Service';
                                                        ?>
                                                        <input type="hidden" name="containers[<?php echo $idx; ?>][service_type_id]" value="">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button>
                                                    </td>

                                                    <!-- hidden remaining fields -->
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][booking_id]" value="<?php echo htmlspecialchars($c['booking_id'] ?? ''); ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][ready_now]" value="<?php echo !empty($c['ready_now']) ? 1 : 0; ?>">
                                                    <input type="hidden" name="containers[<?php echo $idx; ?>][job_type]" value="swing">
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
                                        <a href="swing-job-list.php" class="btn btn-light">Clear</a>
                                        <button type="submit" class="btn btn-secondary" id="save_btn" disabled>Save</button>
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
    <!-- Select2 -->
    <script src="/ralo/plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/ralo/dist/js/adminlte.min.js"></script>

    <script>
        (function () {
            const containersTable = document.getElementById('containers_table');
            const containersTbody = containersTable ? containersTable.querySelector('tbody') : null;
            const containerAddBtn = document.getElementById('container_add_btn');
            const containerClearBtn = document.getElementById('container_clear_btn');

            const requiredContainerIds = ['c_booking_id', 'c_reference', 'c_container_no', 'c_iso_code_id', 'c_weight', 'c_available_date', 'c_door_type_id', 'c_service_type_id'];
            const allContainerIds = [
                'c_booking_id', 'c_reference', 'c_container_no', 'c_iso_code_id', 'c_weight',
                'c_available_date', 'c_ready_now', 'c_door_type_id', 'c_service_type_id'
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
                const hasContainer = containersTbody && containersTbody.querySelectorAll('tr').length > 0;
                if (saveBtn) saveBtn.disabled = !(customerId && docRecv && fromLoc && hasContainer);
            }

            allContainerIds.forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            ['customer_id', 'document_received_at', 'from_location'].forEach((name) => {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return;
                el.addEventListener('input', syncButtons);
                el.addEventListener('change', syncButtons);
            });

            if (containerClearBtn) {
                containerClearBtn.addEventListener('click', () => {
                    // Don't clear booking_id as it's shared
                    setVal('c_reference', '');
                    setVal('c_container_no', '');
                    setVal('c_iso_code_id', '');
                    setVal('c_weight', '');
                    setVal('c_available_date', '');
                    setVal('c_ready_now', 0);
                    setVal('c_door_type_id', '');
                    setVal('c_service_type_id', '');
                    syncButtons();
                });
            }
            
            // Auto-sync booking_id from container to services and notes
            const cBookingId = document.getElementById('c_booking_id');
            if (cBookingId) {
                cBookingId.addEventListener('change', function() {
                    const selectedBookingId = this.value;
                    // Update job_booking_id hidden field if it exists
                    const jobBookingIdSelect = document.getElementById('job_booking_id');
                    if (jobBookingIdSelect && selectedBookingId) {
                        // Find the option with matching data-booking-id
                        const option = Array.from(jobBookingIdSelect.options).find(opt => 
                            opt.getAttribute('data-booking-id') === selectedBookingId
                        );
                        if (option) {
                            jobBookingIdSelect.value = option.value;
                        }
                    }
                    syncButtons();
                });
            }

            if (containerAddBtn && containersTbody) {
                containerAddBtn.addEventListener('click', () => {
                    if (!canAddContainer()) return;

                    const idx = containersTbody.querySelectorAll('tr').length;
                    const isoId = getVal('c_iso_code_id');
                    const isoText = document.querySelector(`#c_iso_code_id option[value="${isoId}"]`)?.textContent || '';
                    const doorId = getVal('c_door_type_id');
                    const doorText = document.querySelector(`#c_door_type_id option[value="${doorId}"]`)?.textContent || '';
                    const serviceId = getVal('c_service_type_id');
                    const serviceText = document.querySelector(`#c_service_type_id option[value="${serviceId}"]`)?.textContent || '';

                    const bookingId = getVal('c_booking_id') || '';
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(getVal('c_reference'))}<input type="hidden" name="containers[${idx}][reference]" value="${escapeHtml(getVal('c_reference'))}"></td>
                        <td>${escapeHtml(getVal('c_container_no'))}<input type="hidden" name="containers[${idx}][container_no]" value="${escapeHtml(getVal('c_container_no'))}"></td>
                        <td>${escapeHtml(isoText)}<input type="hidden" name="containers[${idx}][iso_code_id]" value="${escapeHtml(isoId)}"></td>
                        <td>${escapeHtml(getVal('c_weight'))}<input type="hidden" name="containers[${idx}][weight]" value="${escapeHtml(getVal('c_weight'))}"></td>
                        <td>${escapeHtml(getVal('c_available_date'))}<input type="hidden" name="containers[${idx}][available_date]" value="${escapeHtml(getVal('c_available_date'))}"></td>
                        <td>${escapeHtml(doorText)}<input type="hidden" name="containers[${idx}][door_type_id]" value="${escapeHtml(doorId)}"></td>
                        <td>${escapeHtml(serviceText)}<input type="hidden" name="containers[${idx}][service_type_id]" value="${escapeHtml(serviceId)}"></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-container"><i class="fas fa-trash"></i></button></td>
                        <input type="hidden" name="containers[${idx}][booking_id]" value="${escapeHtml(bookingId)}">
                        <input type="hidden" name="containers[${idx}][ready_now]" value="${escapeHtml(getVal('c_ready_now'))}">
                        <input type="hidden" name="containers[${idx}][job_type]" value="swing">
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
            });

            // Notes dynamic
            const notesTable = document.getElementById('notes_table');
            const notesTbody = notesTable ? notesTable.querySelector('tbody') : null;
            const bookingIdSelect = document.getElementById('booking_id_select');
            const endorsementSelect = document.getElementById('endorsement_select');
            const noteInput = document.getElementById('note_input');
            const noteAddBtn = document.getElementById('note_add_btn');
            const noteClearBtn = document.getElementById('note_clear_btn');

            if (endorsementSelect) endorsementSelect.addEventListener('change', syncButtons);
            if (noteInput) noteInput.addEventListener('input', syncButtons);

            if (noteAddBtn && notesTbody && endorsementSelect) {
                noteAddBtn.addEventListener('click', () => {
                    const eid = (endorsementSelect.value || '').trim();
                    if (!eid) return;
                    const ename = endorsementSelect.options[endorsementSelect.selectedIndex].textContent || '';
                    const ntext = (noteInput?.value || '').trim();
                    const bookingId = (document.getElementById('c_booking_id')?.value || '').trim();

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-endorsement-id', eid);
                    tr.innerHTML = `
                        <td></td>
                        <td>${escapeHtml(bookingId)}<input type="hidden" name="booking_ids[]" value="${escapeHtml(bookingId)}"></td>
                        <td>${escapeHtml(ename)}<input type="hidden" name="endorsement_ids[]" value="${escapeHtml(eid)}"></td>
                        <td>${escapeHtml(ntext)}<input type="hidden" name="notes[]" value="${escapeHtml(ntext)}"></td>
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
            });

            // initial state
            showTableIfHasRows(containersTable);
            showTableIfHasRows(servicesTable);
            showTableIfHasRows(notesTable);
            syncButtons();
        })();

        // Initialize Select2 for dropdowns
        $(document).ready(function() {
            $('#customer_id').select2({
                placeholder: "Customer Name / Customer Code",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0,
                dropdownAutoWidth: true
            });
            
            $('#c_booking_id').select2({
                placeholder: "Select Booking ID",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                minimumResultsForSearch: 0,
                dropdownAutoWidth: true
            });
            
            // Auto-sync booking_id from container to services and notes
            $('#c_booking_id').on('change', function() {
                const selectedBookingId = $(this).val();
                if (selectedBookingId) {
                    // Update job_booking_id if it exists
                    const jobBookingIdSelect = $('#job_booking_id');
                    if (jobBookingIdSelect.length) {
                        const option = jobBookingIdSelect.find(`option[data-booking-id="${selectedBookingId}"]`);
                        if (option.length) {
                            jobBookingIdSelect.val(option.val()).trigger('change');
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
