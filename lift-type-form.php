<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$lift_type = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM lift_types WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lift_type = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Lift Type</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Lift Type</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="lift-type-list.php">Lift Types</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="lift-type-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($lift_type['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Lift Type Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="name"
                                                        name="name"
                                                        placeholder="e.g., All Lifts, Forklift"
                                                        value="<?php echo $edit ? htmlspecialchars($lift_type['name']) : ''; ?>"
                                                        required
                                                        maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="code">Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="code"
                                                        name="code"
                                                        placeholder="e.g., ALL, FL, TL"
                                                        value="<?php echo $edit ? htmlspecialchars($lift_type['code'] ?? '') : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            id="status"
                                                            name="status"
                                                            value="1"
                                                            <?php echo $edit && $lift_type['status'] ? 'checked' : 'checked'; ?>>
                                                        <label class="form-check-label" for="status">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control"
                                                        id="description"
                                                        name="description"
                                                        rows="3"
                                                        placeholder="Description of the lift type"><?php echo $edit ? htmlspecialchars($lift_type['description'] ?? '') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="lift-type-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Lift Type
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