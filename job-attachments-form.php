<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$attachment = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM job_attachments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attachment = $result->fetch_assoc();
    $stmt->close();
}

// Dropdown data
$job_bookings = $conn->query("SELECT id, booking_id FROM job_bookings ORDER BY booking_id ASC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Job Attachment</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Job Attachment</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="job-attachments-list.php">Job Attachments</a></li>
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

      <form action="job-attachments-store.php" method="POST" enctype="multipart/form-data">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($attachment['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Job Attachment Information</h3>
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
                            <option value="<?php echo (int)$job['id']; ?>" <?php echo ($edit && (int)$attachment['booking_id'] === (int)$job['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($job['booking_id']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="file_path">
                          File Upload <?php echo $edit ? '(Leave empty to keep current file)' : ''; ?>
                        </label>
                        <div class="custom-file">
                          <input type="file" 
                                 class="custom-file-input" 
                                 id="file_path" 
                                 name="file_path">
                          <label class="custom-file-label" for="file_path">
                            <?php 
                              if ($edit && $attachment['file_path']) {
                                echo htmlspecialchars(basename($attachment['file_path']));
                              } else {
                                echo 'Choose file';
                              }
                            ?>
                          </label>
                        </div>
                        <small class="form-text text-muted">
                          All file types are allowed (PDF, Word, Excel, Images, etc.)
                        </small>
                        <?php if ($edit && $attachment['file_path']): ?>
                          <div class="mt-2">
                            <strong>Current File:</strong> 
                            <a href="/ralo/<?php echo htmlspecialchars($attachment['file_path']); ?>" target="_blank" class="btn btn-sm btn-info">
                              <i class="fas fa-download"></i> <?php echo htmlspecialchars(basename($attachment['file_path'])); ?>
                            </a>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="job-attachments-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> Attachment
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
<script>
  // Update custom file input label
  $(document).on('change', '.custom-file-input', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>
