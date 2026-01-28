<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$note = null;
$endorsements = [];

// Fetch related data for dropdowns
$job_bookings_result = $conn->query("SELECT booking_id FROM job_bookings ORDER BY booking_id ASC");
$job_bookings = $job_bookings_result->fetch_all(MYSQLI_ASSOC);

$endorsements_result = $conn->query("SELECT id, name FROM endorsements ORDER BY name ASC");
$endorsements = $endorsements_result->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM import_job_notes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Import Job Note</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Import Job Note</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="import-job-notes-list.php">Import Job Notes</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="import-job-notes-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($note['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Import Job Note Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="job_type">
                                                        Job Type <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="job_type" name="job_type" required>
                                                        <option value="">Select Job Type</option>
                                                        <option value="import" <?php echo ($edit && isset($note['job_type']) && $note['job_type'] == 'import') ? 'selected' : ''; ?>>Import</option>
                                                        <option value="cart" <?php echo ($edit && isset($note['job_type']) && $note['job_type'] == 'cart') ? 'selected' : ''; ?>>Cart</option>
                                                        <option value="export" <?php echo ($edit && isset($note['job_type']) && $note['job_type'] == 'export') ? 'selected' : ''; ?>>Export</option>
                                                        <option value="swing" <?php echo ($edit && isset($note['job_type']) && $note['job_type'] == 'swing') ? 'selected' : ''; ?>>Swing</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="booking_id" name="booking_id" required>
                                                        <option value="">Select Booking ID</option>
                                                        <?php foreach ($job_bookings as $jb): ?>
                                                            <option value="<?php echo htmlspecialchars($jb['booking_id']); ?>" <?php echo ($edit && isset($note['booking_id']) && $note['booking_id'] == $jb['booking_id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($jb['booking_id']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="endorsement_id">Endorsement <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="endorsement_id" name="endorsement_id" required>
                                                        <option value="">Select Endorsement</option>
                                                        <?php foreach ($endorsements as $endorsement): ?>
                                                            <option value="<?php echo $endorsement['id']; ?>" <?php echo ($edit && $note['endorsement_id'] == $endorsement['id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($endorsement['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="note">Note</label>
                                                    <textarea class="form-control" id="note" name="note" rows="5" placeholder="Enter note"><?php echo $edit ? htmlspecialchars($note['note'] ?? '') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="import-job-notes-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Note
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
