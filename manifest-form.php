<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }


require 'db.php';

$edit = false;
$manifest = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM manifests WHERE manifest_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $manifest = $result->fetch_assoc();
    $stmt->close();
}

// Get companies for dropdown
$companies_result = $conn->query("SELECT id, name FROM companies ORDER BY name");
$companies = $companies_result->fetch_all(MYSQLI_ASSOC);

// Get customers for dropdown - try different column name possibilities
$customers = [];
try {
    // Try customer_name first
    $customers_result = $conn->query("SELECT id, customer_name as name FROM customers ORDER BY customer_name");
    if ($customers_result) {
        $customers = $customers_result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    // If customer_name doesn't exist, try name
    try {
        $customers_result = $conn->query("SELECT id, name FROM customers ORDER BY name");
        if ($customers_result) {
            $customers = $customers_result->fetch_all(MYSQLI_ASSOC);
        }
    } catch (Exception $e2) {
        // Customers table might not exist, continue with empty array
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Manifest</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Manifest</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="manifest-list.php">Manifests</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="manifest-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="manifest_id" value="<?php echo htmlspecialchars($manifest['manifest_id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Manifest Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="company_id">
                                                Company <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="company_id" name="company_id" required>
                                                <option value="">Select Company</option>
                                                <?php foreach ($companies as $company): 
                                                    $company_id_value = $company['id'];
                                                    $name = $company['name'];
                                                    $selected = $edit && isset($manifest['company_id']) && $manifest['company_id'] == $company_id_value ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($company_id_value); ?>" <?php echo $selected; ?>>
                                                        <?php echo htmlspecialchars($name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="customer_id">Customer</label>
                                            <select class="form-control" id="customer_id" name="customer_id">
                                                <option value="">Select Customer (Optional)</option>
                                                <?php foreach ($customers as $customer): 
                                                    $customer_id_value = $customer['id'];
                                                    $customer_name = isset($customer['name']) ? $customer['name'] : (isset($customer['customer_name']) ? $customer['customer_name'] : 'N/A');
                                                    $selected = $edit && isset($manifest['customer_id']) && $manifest['customer_id'] == $customer_id_value ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($customer_id_value); ?>" <?php echo $selected; ?>>
                                                        <?php echo htmlspecialchars($customer_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="manifest_date">
                                                Manifest Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                class="form-control"
                                                id="manifest_date"
                                                name="manifest_date"
                                                value="<?php echo $edit ? $manifest['manifest_date'] : date('Y-m-d'); ?>"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="manifest_type">Manifest Type</label>
                                            <select class="form-control" id="manifest_type" name="manifest_type">
                                                <option value="">Select Manifest Type</option>
                                                <option value="Import" <?php echo $edit && $manifest['manifest_type'] == 'Import' ? 'selected' : ''; ?>>Import</option>
                                                <option value="Export" <?php echo $edit && $manifest['manifest_type'] == 'Export' ? 'selected' : ''; ?>>Export</option>
                                                <option value="Domestic" <?php echo $edit && $manifest['manifest_type'] == 'Domestic' ? 'selected' : ''; ?>>Domestic</option>
                                                <option value="International" <?php echo $edit && $manifest['manifest_type'] == 'International' ? 'selected' : ''; ?>>International</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="manifest-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Manifest
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
