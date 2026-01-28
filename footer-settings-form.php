<?php
session_start();

$edit = false;
$setting = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM footer_settings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $setting = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Footer Settings</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Add New'; ?> Footer Setting</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo URL; ?>/index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo URL; ?>/footer-settings-list.php">Footer Settings</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Add New'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo $edit ? 'Edit' : 'Add New'; ?> Footer Setting</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="<?php echo URL; ?>/footer-settings-store.php" method="post">
                                    <div class="card-body">

                                        <?php if (isset($_GET['error'])): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($edit): ?>
                                            <input type="hidden" name="id" value="<?php echo $setting['id']; ?>">
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label for="site_name">Site Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" placeholder="Enter site name" 
                                                value="<?php echo $edit ? htmlspecialchars($setting['site_name']) : ''; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="copyright_start_year">Copyright Start Year <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="copyright_start_year" name="copyright_start_year" 
                                                placeholder="e.g., 2020" min="1900" max="<?php echo date('Y'); ?>"
                                                value="<?php echo $edit ? $setting['copyright_start_year'] : ''; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="version">Version <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="version" name="version" placeholder="e.g., 1.0.0" 
                                                value="<?php echo $edit ? htmlspecialchars($setting['version']) : ''; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="status" name="status" 
                                                    <?php echo $edit && $setting['status'] == 1 ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="status">
                                                    Active
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo $edit ? 'Update' : 'Save'; ?>
                                        </button>
                                        <a href="<?php echo URL; ?>/footer-settings-list.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include('footer.php'); ?>
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
