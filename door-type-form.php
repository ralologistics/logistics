<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$door_type = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM door_types WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $door_type = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Door Type</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Door Type</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="door-type-list.php">Door Types</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="door-type-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo $door_type['id']; ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Door Type Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">
                                                Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                placeholder="Enter door type name"
                                                value="<?php echo $edit ? htmlspecialchars($door_type['name']) : ''; ?>"
                                                required
                                                maxlength="100">
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="door-type-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Door Type
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
