<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$notification = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM job_tracking_notifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();
    $stmt->close();
}

// Dropdown data
$job_bookings = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);
$notification_types = $conn->query("SELECT id, name FROM notification_types WHERE is_active = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Communication type ENUM values
$communication_types = ['EMAIL', 'PHONE', 'SMS', 'WHATSAPP', 'PUSH'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Job Tracking Notification</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Job Tracking Notification</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="job-tracking-notifications-list.php">Job Tracking Notifications</a></li>
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

      <form action="job-tracking-notifications-store.php" method="POST">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($notification['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Job Tracking Notification Information</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="job_id">
                          Job Booking <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="job_id" name="job_id" required>
                          <option value="">Select Job Booking</option>
                          <?php foreach ($job_bookings as $job): ?>
                            <option value="<?php echo (int)$job['id']; ?>" <?php echo ($edit && (int)$notification['job_id'] === (int)$job['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($job['booking_id']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="communication_type">
                          Communication Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="communication_type" name="communication_type" required>
                          <option value="">Select Communication Type</option>
                          <?php foreach ($communication_types as $ct): ?>
                            <option value="<?php echo htmlspecialchars($ct); ?>" <?php echo ($edit && $notification['communication_type'] === $ct) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($ct); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="contact">
                          Contact
                        </label>
                        <input type="text"
                               class="form-control"
                               id="contact"
                               name="contact"
                               placeholder="e.g., email@example.com, +1234567890"
                               value="<?php echo $edit ? htmlspecialchars($notification['contact'] ?? '') : ''; ?>"
                               maxlength="150">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="notification_type_id">
                          Notification Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="notification_type_id" name="notification_type_id" required>
                          <option value="">Select Notification Type</option>
                          <?php foreach ($notification_types as $nt): ?>
                            <option value="<?php echo (int)$nt['id']; ?>" <?php echo ($edit && (int)$notification['notification_type_id'] === (int)$nt['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($nt['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="job-tracking-notifications-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> Notification
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
