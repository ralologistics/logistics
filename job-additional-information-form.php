<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$additional_info = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM job_additional_information WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $additional_info = $result->fetch_assoc();
    $stmt->close();
}

// Dropdown data
$job_bookings = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);
$dg_signatories = $conn->query("SELECT id, name FROM dg_signatories WHERE status = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Insurance type ENUM values
$insurance_types = [
    'Owners Risk',
    'Carriers Risk',
    'All Risk',
    'Total Loss Only',
    'Third Party',
    'Limited Carrier Liability'
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Job Additional Information</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Job Additional Information</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="job-additional-information-list.php">Job Additional Information</a></li>
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

      <form action="job-additional-information-store.php" method="POST">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($additional_info['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Job Additional Information</h3>
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
                            <option value="<?php echo (int)$job['id']; ?>" <?php echo ($edit && (int)$additional_info['booking_id'] === (int)$job['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($job['booking_id']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="insurance_type">
                          Insurance Type
                        </label>
                        <select class="form-control" id="insurance_type" name="insurance_type">
                          <?php foreach ($insurance_types as $it): ?>
                            <?php 
                              $selected = '';
                              if ($edit && $additional_info['insurance_type'] === $it) {
                                $selected = 'selected';
                              } elseif (!$edit && $it === 'Owners Risk') {
                                $selected = 'selected';
                              }
                            ?>
                            <option value="<?php echo htmlspecialchars($it); ?>" <?php echo $selected; ?>>
                              <?php echo htmlspecialchars($it); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="dg_signatory_id">
                          DG Signatory
                        </label>
                        <select class="form-control" id="dg_signatory_id" name="dg_signatory_id">
                          <option value="">Select DG Signatory (Optional)</option>
                          <?php foreach ($dg_signatories as $dgs): ?>
                            <option value="<?php echo (int)$dgs['id']; ?>" <?php echo ($edit && $additional_info['dg_signatory_id'] && (int)$additional_info['dg_signatory_id'] === (int)$dgs['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($dgs['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="customer_reference_2">
                          Customer Reference 2
                        </label>
                        <input type="text"
                               class="form-control"
                               id="customer_reference_2"
                               name="customer_reference_2"
                               placeholder="Enter customer reference"
                               value="<?php echo $edit ? htmlspecialchars($additional_info['customer_reference_2'] ?? '') : ''; ?>"
                               maxlength="255">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="receiver_reference_2">
                          Receiver Reference 2
                        </label>
                        <input type="text"
                               class="form-control"
                               id="receiver_reference_2"
                               name="receiver_reference_2"
                               placeholder="Enter receiver reference"
                               value="<?php echo $edit ? htmlspecialchars($additional_info['receiver_reference_2'] ?? '') : ''; ?>"
                               maxlength="255">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="job-additional-information-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> Information
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
