<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$container = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM containers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $container = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

// Fetch dropdown data
require 'db.php';

// Import job bookings
$import_jobs_result = $conn->query("SELECT id, booking_no FROM import_job_bookings ORDER BY booking_no ASC");
$import_jobs = $import_jobs_result->fetch_all(MYSQLI_ASSOC);

// Job bookings
$job_bookings_result = $conn->query("SELECT booking_id FROM job_bookings ORDER BY booking_id ASC");
$job_bookings = $job_bookings_result->fetch_all(MYSQLI_ASSOC);

// ISO codes
$iso_codes_result = $conn->query("SELECT id, code FROM iso_codes ORDER BY code ASC");
$iso_codes = $iso_codes_result->fetch_all(MYSQLI_ASSOC);

// Door types
$door_types_result = $conn->query("SELECT id, name FROM door_types ORDER BY name ASC");
$door_types = $door_types_result->fetch_all(MYSQLI_ASSOC);

// Shippings
$shippings_result = $conn->query("SELECT id, name FROM shippings ORDER BY name ASC");
$shippings = $shippings_result->fetch_all(MYSQLI_ASSOC);

// Vessels
$vessels_result = $conn->query("SELECT id, name FROM vessels ORDER BY name ASC");
$vessels = $vessels_result->fetch_all(MYSQLI_ASSOC);

// Ship types
$ship_types_result = $conn->query("SELECT id, type_name FROM ship_types ORDER BY type_name ASC");
$ship_types = $ship_types_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Container</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Container</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="containers-list.php">Containers</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="containers-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($container['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Container Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="import_job_id">
                                                        Import Job <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="import_job_id" name="import_job_id" required>
                                                        <option value="">Select Import Job</option>
                                                        <?php foreach ($import_jobs as $job): ?>
                                                            <option value="<?php echo htmlspecialchars($job['id']); ?>" <?php echo $edit && isset($container['job_type']) && $container['job_type'] == 'import' && $container['job_id'] == $job['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($job['booking_no']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="booking_id">
                                                        Booking ID <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="booking_id" name="booking_id" required>
                                                        <option value="">Select Booking ID</option>
                                                        <?php foreach ($job_bookings as $jb): ?>
                                                            <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>" <?php echo $edit && isset($container['booking_id']) && $container['booking_id'] == $jb['booking_id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($jb['booking_id']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="container_no">Container No</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="container_no"
                                                        name="container_no"
                                                        placeholder="e.g., ABC1234567"
                                                        value="<?php echo $edit ? htmlspecialchars($container['container_no']) : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cut_off_date">Cut Off Date</label>
                                                    <input type="date"
                                                        class="form-control"
                                                        id="cut_off_date"
                                                        name="cut_off_date"
                                                        value="<?php echo $edit ? htmlspecialchars($container['cut_off_date']) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="grid_position">Grid Position</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="grid_position"
                                                        name="grid_position"
                                                        placeholder="Grid position"
                                                        value="<?php echo $edit ? htmlspecialchars($container['grid_position']) : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="no_of_containers">No. of Containers</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="no_of_containers"
                                                        name="no_of_containers"
                                                        placeholder="1"
                                                        value="<?php echo $edit ? htmlspecialchars($container['no_of_containers']) : '1'; ?>"
                                                        min="1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="reference">Reference</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="reference"
                                                        name="reference"
                                                        placeholder="Reference number"
                                                        value="<?php echo $edit ? htmlspecialchars($container['reference']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="iso_code_id">
                                                        ISO Code <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="iso_code_id" name="iso_code_id" required>
                                                        <option value="">Select ISO Code</option>
                                                        <?php foreach ($iso_codes as $code): ?>
                                                            <option value="<?php echo htmlspecialchars($code['id']); ?>" <?php echo $edit && $container['iso_code_id'] == $code['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($code['code']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="weight">Weight (kg)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        class="form-control"
                                                        id="weight"
                                                        name="weight"
                                                        placeholder="0.00"
                                                        value="<?php echo $edit ? htmlspecialchars($container['weight']) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="door_type_id">Door Type</label>
                                                    <select class="form-control" id="door_type_id" name="door_type_id">
                                                        <option value="">Select Door Type</option>
                                                        <?php foreach ($door_types as $type): ?>
                                                            <option value="<?php echo htmlspecialchars($type['id']); ?>" <?php echo $edit && $container['door_type_id'] == $type['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($type['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="from_location">From Location</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="from_location"
                                                        name="from_location"
                                                        placeholder="Origin location"
                                                        value="<?php echo $edit ? htmlspecialchars($container['from_location']) : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="to_location">To Location</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="to_location"
                                                        name="to_location"
                                                        placeholder="Destination location"
                                                        value="<?php echo $edit ? htmlspecialchars($container['to_location']) : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="return_to">Return To</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="return_to"
                                                        name="return_to"
                                                        placeholder="Return location"
                                                        value="<?php echo $edit ? htmlspecialchars($container['return_to']) : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_location">Customer Location</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="customer_location"
                                                        name="customer_location"
                                                        placeholder="Customer location"
                                                        value="<?php echo $edit ? htmlspecialchars($container['customer_location']) : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_id">Shipping</label>
                                                    <select class="form-control" id="shipping_id" name="shipping_id">
                                                        <option value="">Select Shipping</option>
                                                        <?php foreach ($shippings as $shipping): ?>
                                                            <option value="<?php echo htmlspecialchars($shipping['id']); ?>" <?php echo $edit && $container['shipping_id'] == $shipping['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($shipping['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="vessel_id">Vessel</label>
                                                    <select class="form-control" id="vessel_id" name="vessel_id">
                                                        <option value="">Select Vessel</option>
                                                        <?php foreach ($vessels as $vessel): ?>
                                                            <option value="<?php echo htmlspecialchars($vessel['id']); ?>" <?php echo $edit && $container['vessel_id'] == $vessel['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($vessel['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ship_type_id">Ship Type</label>
                                                    <select class="form-control" id="ship_type_id" name="ship_type_id">
                                                        <option value="">Select Ship Type</option>
                                                        <?php foreach ($ship_types as $type): ?>
                                                            <option value="<?php echo htmlspecialchars($type['id']); ?>" <?php echo $edit && $container['ship_type_id'] == $type['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($type['type_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="voyage">Voyage</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="voyage"
                                                        name="voyage"
                                                        placeholder="Voyage number"
                                                        value="<?php echo $edit ? htmlspecialchars($container['voyage']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="available_date">Available Date</label>
                                                    <input type="date"
                                                        class="form-control"
                                                        id="available_date"
                                                        name="available_date"
                                                        value="<?php echo $edit ? htmlspecialchars($container['available_date']) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="vb_slot_date">VB Slot Date</label>
                                                    <input type="date"
                                                        class="form-control"
                                                        id="vb_slot_date"
                                                        name="vb_slot_date"
                                                        value="<?php echo $edit ? htmlspecialchars($container['vb_slot_date']) : ''; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="demurrage_date">Demurrage Date</label>
                                                    <input type="date"
                                                        class="form-control"
                                                        id="demurrage_date"
                                                        name="demurrage_date"
                                                        value="<?php echo $edit ? htmlspecialchars($container['demurrage_date']) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="detention_days">Detention Days</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="detention_days"
                                                        name="detention_days"
                                                        placeholder="0"
                                                        value="<?php echo $edit ? htmlspecialchars($container['detention_days']) : '0'; ?>"
                                                        min="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="security_check">Security Check</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="security_check"
                                                        name="security_check"
                                                        placeholder="Security check details"
                                                        value="<?php echo $edit ? htmlspecialchars($container['security_check']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="random_number">Random Number</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="random_number"
                                                        name="random_number"
                                                        placeholder="Random number"
                                                        value="<?php echo $edit ? htmlspecialchars($container['random_number']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="release_ecn_number">Release ECN Number</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="release_ecn_number"
                                                        name="release_ecn_number"
                                                        placeholder="Release ECN number"
                                                        value="<?php echo $edit ? htmlspecialchars($container['release_ecn_number']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="port_pin_no">Port PIN No</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="port_pin_no"
                                                        name="port_pin_no"
                                                        placeholder="Port PIN number"
                                                        value="<?php echo $edit ? htmlspecialchars($container['port_pin_no']) : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Special Requirements</label>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="xray" name="xray" value="1" <?php echo $edit && $container['xray'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="xray">X-Ray</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="dgs" name="dgs" value="1" <?php echo $edit && $container['dgs'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="dgs">DGS</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="live_ul" name="live_ul" value="1" <?php echo $edit && $container['live_ul'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="live_ul">Live UL</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="hold_sh" name="hold_sh" value="1" <?php echo $edit && $container['hold_sh'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="hold_sh">Hold SH</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="hold_customs" name="hold_customs" value="1" <?php echo $edit && $container['hold_customs'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="hold_customs">Hold Customs</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="hold_mpi" name="hold_mpi" value="1" <?php echo $edit && $container['hold_mpi'] ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="hold_mpi">Hold MPI</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="containers-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Container
                                        </button>
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
    <!-- AdminLTE App -->
    <script src="/ralo/dist/js/adminlte.min.js"></script>
</body>

</html>
