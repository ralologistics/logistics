<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$result = $conn->query("SELECT ejb.*, c.name as customer_name, jb.booking_id as booking_ref, v.name as vessel_name, s.name as shipping_name FROM export_job_bookings ejb LEFT JOIN customers c ON ejb.customer_id = c.id LEFT JOIN job_bookings jb ON ejb.booking_id = jb.id LEFT JOIN vessels v ON ejb.vessel_id = v.id LEFT JOIN shippings s ON ejb.shipping__id = s.id ORDER BY ejb.id DESC");
$jobs = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Export Job Bookings</title>

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
            <h1>Export Job Bookings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Export Jobs</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Export Job Bookings</h3>
                <div class="card-tools">
                  <a href="export-job-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="jobsTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Job No</th>
                    <th>Customer</th>
                    <th>Booking</th>
                    <th>Shipping</th>
                    <th>Vessel</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Document Received</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($jobs as $job): ?>
                  <tr>
                    <td><?php echo $job['id']; ?></td>
                    <td><?php echo htmlspecialchars($job['job_no']); ?></td>
                    <td><?php echo htmlspecialchars($job['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($job['booking_ref']); ?></td>
                    <td><?php echo htmlspecialchars($job['shipping_name']); ?></td>
                    <td><?php echo htmlspecialchars($job['vessel_name']); ?></td>
                    <td><?php echo htmlspecialchars($job['from_location']); ?></td>
                    <td><?php echo htmlspecialchars($job['to_location']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($job['document_received_at'])); ?></td>
                    <td>
                      <a href="export-job-view.php?id=<?php echo $job['id']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View
                      </a>
                      <a href="export-job-form.php?id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <a href="export-job-delete.php?id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
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
  <?php include('footer.php'); ?>
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
    $("#jobsTable").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>
</body>
</html>