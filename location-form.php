<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

// Get options for dropdowns
$companies_result = $conn->query("SELECT id, name FROM companies ORDER BY name");
$companies = $companies_result->fetch_all(MYSQLI_ASSOC);

$location_types_result = $conn->query("SELECT id, name FROM location_types ORDER BY name");
$location_types = $location_types_result->fetch_all(MYSQLI_ASSOC);

$door_types_result = $conn->query("SELECT id, name FROM door_types ORDER BY name");
$door_types = $door_types_result->fetch_all(MYSQLI_ASSOC);

$lift_types_result = $conn->query("SELECT id, name FROM lift_types ORDER BY name");
$lift_types = $lift_types_result->fetch_all(MYSQLI_ASSOC);

$countries_result = $conn->query("SELECT id, name FROM countries WHERE is_active = 1 ORDER BY name");
$countries = $countries_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Location</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css.all.min.css">
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
                            <h1>Create Location</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="location-list.php">Locations</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="location-store.php" method="POST">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Location Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Warehouse A" required maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="location_code">Location Code</label>
                                                    <input type="text" class="form-control" id="location_code" name="location_code" placeholder="e.g., LOC001" maxlength="50">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_id">Company <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="company_id" name="company_id" required>
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($companies as $company): ?>
                                                            <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="location_type_id">Location Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="location_type_id" name="location_type_id" required>
                                                        <option value="">Select Location Type</option>
                                                        <?php foreach ($location_types as $type): ?>
                                                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="door_type_id">Door Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="door_type_id" name="door_type_id" required>
                                                        <option value="">Select Door Type</option>
                                                        <?php foreach ($door_types as $type): ?>
                                                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lift_type_id">Lift Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="lift_type_id" name="lift_type_id" required>
                                                        <option value="">Select Lift Type</option>
                                                        <?php foreach ($lift_types as $type): ?>
                                                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="building">Building</label>
                                                    <input type="text" class="form-control" id="building" name="building" placeholder="e.g., Building 1" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="street_no">Street No</label>
                                                    <input type="text" class="form-control" id="street_no" name="street_no" placeholder="e.g., 123" maxlength="20">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="street">Street</label>
                                                    <input type="text" class="form-control" id="street" name="street" placeholder="e.g., Main Street" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="suburb">Suburb</label>
                                                    <input type="text" class="form-control" id="suburb" name="suburb" placeholder="e.g., Downtown" maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text" class="form-control" id="city" name="city" placeholder="e.g., Auckland" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="state">State</label>
                                                    <input type="text" class="form-control" id="state" name="state" placeholder="e.g., Auckland" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="postcode">Postcode</label>
                                                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="e.g., 1010" maxlength="20">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="country_id">Country <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="country_id" name="country_id" required>
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $country): ?>
                                                            <option value="<?php echo $country['id']; ?>"><?php echo htmlspecialchars($country['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contact_person">Contact Person</label>
                                                    <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="e.g., John Doe" maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g., +64 9 123 4567" maxlength="30">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mobile">Mobile</label>
                                                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="e.g., +64 21 123 4567" maxlength="30">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="e.g., contact@location.com" maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="send_tracking_email">Send Tracking Email</label>
                                                    <select class="form-control" id="send_tracking_email" name="send_tracking_email">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="special_instruction">Special Instruction</label>
                                                    <textarea class="form-control" id="special_instruction" name="special_instruction" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Create Location</button>
                                        <a href="location-list.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
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