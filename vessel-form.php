<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$vessel = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM vessels WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vessel = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

require 'db.php';
// Get countries for dropdown
$countries_result = $conn->query("SELECT id, name FROM countries WHERE is_active = 1 ORDER BY name");
$countries = $countries_result->fetch_all(MYSQLI_ASSOC);

// Get ship types for dropdown
$ship_types_result = $conn->query("SELECT id, type_name FROM ship_types ORDER BY type_name");
$ship_types = $ship_types_result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Vessel</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Vessel</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="vessel-list.php">Vessels</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="vessel-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo $vessel['id']; ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Vessel Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Vessel Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="name"
                                                        name="name"
                                                        placeholder="Enter vessel name"
                                                        value="<?php echo $edit ? htmlspecialchars($vessel['name']) : ''; ?>"
                                                        required
                                                        maxlength="150">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="country_id">
                                                        Country <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="country_id" name="country_id" required>
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $country): 
                                                            $selected = $edit && $vessel['country_id'] == $country['id'] ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $country['id']; ?>" <?php echo $selected; ?>>
                                                                <?php echo htmlspecialchars($country['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="ship_type_id">
                                                        Ship Type <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="ship_type_id" name="ship_type_id" required>
                                                        <option value="">Select Ship Type</option>
                                                        <?php foreach ($ship_types as $ship_type): 
                                                            $selected = $edit && $vessel['ship_type_id'] == $ship_type['id'] ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $ship_type['id']; ?>" <?php echo $selected; ?>>
                                                                <?php echo htmlspecialchars($ship_type['type_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="imo_number">IMO Number</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="imo_number"
                                                        name="imo_number"
                                                        placeholder="e.g., IMO1234567"
                                                        value="<?php echo $edit ? htmlspecialchars($vessel['imo_number'] ?? '') : ''; ?>"
                                                        maxlength="20">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mmsi">MMSI</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="mmsi"
                                                        name="mmsi"
                                                        placeholder="e.g., 352001683"
                                                        value="<?php echo $edit ? htmlspecialchars($vessel['mmsi'] ?? '') : ''; ?>"
                                                        maxlength="20">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="call_sign">Call Sign</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="call_sign"
                                                        name="call_sign"
                                                        placeholder="e.g., 3E2705"
                                                        value="<?php echo $edit ? htmlspecialchars($vessel['call_sign'] ?? '') : ''; ?>"
                                                        maxlength="20"
                                                        style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="built_year">Built Year</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="built_year"
                                                        name="built_year"
                                                        placeholder="e.g., 2020"
                                                        value="<?php echo $edit ? $vessel['built_year'] : ''; ?>"
                                                        min="1900"
                                                        max="<?php echo date('Y'); ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="length_m">Length (m)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        class="form-control"
                                                        id="length_m"
                                                        name="length_m"
                                                        placeholder="e.g., 200.50"
                                                        value="<?php echo $edit ? $vessel['length_m'] : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="width_m">Width (m)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        class="form-control"
                                                        id="width_m"
                                                        name="width_m"
                                                        placeholder="e.g., 30.25"
                                                        value="<?php echo $edit ? $vessel['width_m'] : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="draught_m">Draught (m)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        class="form-control"
                                                        id="draught_m"
                                                        name="draught_m"
                                                        placeholder="e.g., 10.50"
                                                        value="<?php echo $edit ? $vessel['draught_m'] : ''; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="gross_tonnage">Gross Tonnage</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="gross_tonnage"
                                                        name="gross_tonnage"
                                                        placeholder="e.g., 50000"
                                                        value="<?php echo $edit ? $vessel['gross_tonnage'] : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="net_tonnage">Net Tonnage</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="net_tonnage"
                                                        name="net_tonnage"
                                                        placeholder="e.g., 40000"
                                                        value="<?php echo $edit ? $vessel['net_tonnage'] : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="dead_weight">Dead Weight</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="dead_weight"
                                                        name="dead_weight"
                                                        placeholder="e.g., 60000"
                                                        value="<?php echo $edit ? $vessel['dead_weight'] : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="vessel-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Vessel
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
    <script>
        // Auto-uppercase call sign
        document.getElementById('call_sign')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
</body>

</html>
