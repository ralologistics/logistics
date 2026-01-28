<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$result = $conn->query("SELECT jai.*, 
                        jb.booking_id as job_booking_id,
                        dgs.name as dg_signatory_name
                        FROM job_additional_information jai
                        LEFT JOIN job_bookings jb ON jai.booking_id = jb.id
                        LEFT JOIN dg_signatories dgs ON jai.dg_signatory_id = dgs.id
                        ORDER BY jai.created_at DESC");
$additional_infos = $result->fetch_all(MYSQLI_ASSOC);
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
  <!-- DataTables -->
  <link rel="stylesheet" href="/ralo/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/ralo/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
            <h1>Job Additional Information</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Job Additional Information</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            Job additional information <?php echo isset($_GET['deleted']) ? 'deleted' : 'saved'; ?> successfully.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            Job additional information deleted successfully.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            <?php echo htmlspecialchars($_GET['error']); ?>
          </div>
        <?php endif; ?>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Job Additional Information</h3>
                <div class="card-tools">
                  <a href="job-additional-information-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="additionalInfoTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Job Booking ID</th>
                    <th>Insurance Type</th>
                    <th>DG Signatory</th>
                    <th>Customer Reference 2</th>
                    <th>Receiver Reference 2</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($additional_infos as $info): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($info['id']); ?></td>
                    <td><?php echo htmlspecialchars($info['job_booking_id'] ?? 'N/A'); ?></td>
                    <td>
                      <span class="badge badge-info"><?php echo htmlspecialchars($info['insurance_type'] ?? 'N/A'); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($info['dg_signatory_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($info['customer_reference_2'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($info['receiver_reference_2'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($info['created_at']))); ?></td>
                    <td>
                      <a href="job-additional-information-form.php?id=<?php echo htmlspecialchars($info['id']); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <a href="job-additional-information-delete.php?id=<?php echo htmlspecialchars($info['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this additional information?')">
                        <i class="fas fa-trash"></i> Delete
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
<!-- DataTables -->
<script src="/ralo/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/ralo/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/ralo/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/ralo/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/ralo/dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#additionalInfoTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      "order": [[ 0, "desc" ]]
    });
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>
