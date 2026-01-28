<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$item = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM import_job_services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
}

// Fetch services
$services_result = $conn->query("SELECT id, name FROM services WHERE status = 1 ORDER BY name ASC");
$services = $services_result ? $services_result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch containers
$containers_result = $conn->query("SELECT id, container_no FROM containers ORDER BY container_no ASC");
$containers = $containers_result ? $containers_result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Import Job Service</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Import Job Service</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="import-job-services-list.php">Import Job Services</a></li>
              <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <form action="import-job-services-store.php" method="POST">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Import Job Service Information</h3>
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
                          <option value="import" <?php echo ($edit && isset($item['job_type']) && $item['job_type'] == 'import') ? 'selected' : ''; ?>>Import</option>
                          <option value="cart" <?php echo ($edit && isset($item['job_type']) && $item['job_type'] == 'cart') ? 'selected' : ''; ?>>Cart</option>
                          <option value="export" <?php echo ($edit && isset($item['job_type']) && $item['job_type'] == 'export') ? 'selected' : ''; ?>>Export</option>
                          <option value="swing" <?php echo ($edit && isset($item['job_type']) && $item['job_type'] == 'swing') ? 'selected' : ''; ?>>Swing</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="service_id">
                          Service <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="service_id" name="service_id" required>
                          <option value="">Select Service</option>
                          <?php foreach ($services as $service):
                            $selected = ($edit && $item['service_id'] == $service['id']) ? 'selected' : '';
                          ?>
                            <option value="<?php echo $service['id']; ?>" <?php echo $selected; ?>>
                              <?php echo htmlspecialchars($service['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="container_id">
                          Container <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="container_id" name="container_id" required>
                          <option value="">Select Container</option>
                          <?php foreach ($containers as $container):
                            $selected = ($edit && $item['container_id'] == $container['id']) ? 'selected' : '';
                          ?>
                            <option value="<?php echo $container['id']; ?>" <?php echo $selected; ?>>
                              <?php echo htmlspecialchars($container['container_no']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="import-job-services-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> Import Job Service
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

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
<?php include('footer.php'); ?>
</body>
</html>
