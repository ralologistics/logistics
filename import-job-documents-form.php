<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$document = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM import_job_documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

// Fetch import job bookings for dropdown
require 'db.php';
$import_jobs_result = $conn->query("SELECT id, booking_no FROM import_job_bookings ORDER BY booking_no ASC");
$import_jobs = $import_jobs_result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Import Job Document</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Import Job Document</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="import-job-documents-list.php">Import Job Documents</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="import-job-documents-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($document['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Import Job Document Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="import_job_id">
                                                        Import Job <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="import_job_id" name="import_job_id" required>
                                                        <option value="">Select Import Job</option>
                                                        <?php foreach ($import_jobs as $job): ?>
                                                            <option value="<?php echo htmlspecialchars($job['id']); ?>" <?php echo $edit && $document['import_job_id'] == $job['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($job['booking_no']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="file_path">
                                                        File Path <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="file_path"
                                                        name="file_path"
                                                        placeholder="e.g., uploads/document.pdf"
                                                        value="<?php echo $edit ? htmlspecialchars($document['file_path']) : ''; ?>"
                                                        required
                                                        maxlength="255">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="import-job-documents-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Document
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
