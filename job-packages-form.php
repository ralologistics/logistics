<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$package = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM job_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
}

// Dropdown data
$job_bookings = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);
$package_types = $conn->query("SELECT id, name FROM package_types WHERE is_active = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$dg_types = $conn->query("SELECT id, name FROM dg_types ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Job Package</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Job Package</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="job-packages-list.php">Job Packages</a></li>
              <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h5><i class="icon fas fa-ban"></i> Error!</h5>
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <form action="job-packages-store.php" method="POST">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($package['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Job Package Information</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="booking_id">
                          Job Booking <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="booking_id" name="booking_id" required>
                          <option value="">Select Job Booking</option>
                          <?php foreach ($job_bookings as $job): ?>
                            <option value="<?php echo (int)$job['id']; ?>" <?php echo ($edit && (int)$package['booking_id'] === (int)$job['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($job['booking_id']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="units">
                          Units
                        </label>
                        <input type="number"
                               class="form-control"
                               id="units"
                               name="units"
                               placeholder="Enter number of units"
                               value="<?php echo $edit ? htmlspecialchars($package['units'] ?? '') : ''; ?>"
                               min="0"
                               step="1">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="weight_kg">
                          Weight (kg)
                        </label>
                        <input type="number"
                               class="form-control"
                               id="weight_kg"
                               name="weight_kg"
                               placeholder="Enter weight in kg"
                               value="<?php echo $edit ? htmlspecialchars($package['weight_kg'] ?? '') : ''; ?>"
                               min="0"
                               step="0.01">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="cubic_m3">
                          Cubic (m³)
                        </label>
                        <input type="number"
                               class="form-control"
                               id="cubic_m3"
                               name="cubic_m3"
                               placeholder="Enter cubic meters"
                               value="<?php echo $edit ? htmlspecialchars($package['cubic_m3'] ?? '') : ''; ?>"
                               min="0"
                               step="0.001">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="length_cm">
                          Length (cm)
                        </label>
                        <input type="number"
                               class="form-control"
                               id="length_cm"
                               name="length_cm"
                               placeholder="Enter length"
                               value="<?php echo $edit ? htmlspecialchars($package['length_cm'] ?? '') : ''; ?>"
                               min="0"
                               step="0.01">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="width_cm">
                          Width (cm)
                        </label>
                        <input type="number"
                               class="form-control"
                               id="width_cm"
                               name="width_cm"
                               placeholder="Enter width"
                               value="<?php echo $edit ? htmlspecialchars($package['width_cm'] ?? '') : ''; ?>"
                               min="0"
                               step="0.01">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="height_cm">
                          Height (cm)
                        </label>
                        <input type="number"
                               class="form-control"
                               id="height_cm"
                               name="height_cm"
                               placeholder="Enter height"
                               value="<?php echo $edit ? htmlspecialchars($package['height_cm'] ?? '') : ''; ?>"
                               min="0"
                               step="0.01">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="package_type_id">
                          Package Type
                        </label>
                        <select class="form-control" id="package_type_id" name="package_type_id">
                          <option value="">Select Package Type (Optional)</option>
                          <?php foreach ($package_types as $pt): ?>
                            <option value="<?php echo (int)$pt['id']; ?>" <?php echo ($edit && $package['package_type_id'] && (int)$package['package_type_id'] === (int)$pt['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($pt['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="dg_type_id">
                          DG Type
                        </label>
                        <select class="form-control" id="dg_type_id" name="dg_type_id">
                          <option value="">Select DG Type (Optional)</option>
                          <?php foreach ($dg_types as $dgt): ?>
                            <option value="<?php echo (int)$dgt['id']; ?>" <?php echo ($edit && $package['dg_type_id'] && (int)$package['dg_type_id'] === (int)$dgt['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($dgt['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="remarks">
                          Remarks
                        </label>
                        <textarea class="form-control"
                                  id="remarks"
                                  name="remarks"
                                  rows="3"
                                  placeholder="Enter any remarks"
                                  maxlength="255"><?php echo $edit ? htmlspecialchars($package['remarks'] ?? '') : ''; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="job-packages-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> Package
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
