<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$result = $conn->query("SELECT c.*,
                        ijb.booking_no as import_job_booking_no,
                        c.booking_id,
                        ic.code as iso_code,
                        dt.name as door_type_name,
                        s.name as shipping_name,
                        v.name as vessel_name,
                        st.type_name as ship_type_name
                        FROM containers c
                        LEFT JOIN import_job_bookings ijb ON c.job_type = 'import' AND c.job_id = ijb.id
                        LEFT JOIN iso_codes ic ON c.iso_code_id = ic.id
                        LEFT JOIN door_types dt ON c.door_type_id = dt.id
                        LEFT JOIN shippings s ON c.shipping_id = s.id
                        LEFT JOIN vessels v ON c.vessel_id = v.id
                        LEFT JOIN ship_types st ON c.ship_type_id = st.id
                        ORDER BY c.created_at DESC");
$containers = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Containers</title>

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
            <h1>Containers</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Containers</li>
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
                <h3 class="card-title">List of Containers</h3>
                <div class="card-tools">
                  <a href="containers-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Container
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="containersTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Import Job</th>
                    <th>Booking ID</th>
                    <th>Container No</th>
                    <th>Reference</th>
                    <th>Cut Off Date</th>
                    <th>Grid Position</th>
                    <th>No. of Containers</th>
                    <th>ISO Code</th>
                    <th>Weight</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($containers as $container): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($container['id']); ?></td>
                    <td><?php echo htmlspecialchars($container['import_job_booking_no'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($container['booking_id'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($container['container_no']); ?></td>
                    <td><?php echo htmlspecialchars($container['reference']); ?></td>
                    <td><?php echo htmlspecialchars($container['cut_off_date'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($container['grid_position'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($container['no_of_containers'] ?? '1'); ?></td>
                    <td><?php echo htmlspecialchars($container['iso_code'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($container['weight']); ?></td>
                    <td><?php echo htmlspecialchars($container['from_location']); ?></td>
                    <td><?php echo htmlspecialchars($container['to_location']); ?></td>
                    <td><?php echo htmlspecialchars($container['created_at']); ?></td>
                    <td>
                      <a href="containers-form.php?id=<?php echo htmlspecialchars($container['id']); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <a href="containers-delete.php?id=<?php echo htmlspecialchars($container['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this container?')">
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
    $("#containersTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      "order": [[ 0, "desc" ]]
    });
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>
