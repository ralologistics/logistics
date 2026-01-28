<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

// Get manifests with company info
// For customers, we'll fetch separately to handle missing columns
$result = $conn->query("
    SELECT m.*, 
           c.name
    FROM manifests m
    LEFT JOIN companies c ON m.company_id = c.id
    ORDER BY m.created_at DESC
");

$manifests = [];
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $row) {
        // Try to get customer name if customer_id exists
        $customer_name = 'N/A';
        if (!empty($row['customer_id'])) {
            try {
                // Try customer_name first
                $cust_stmt = $conn->prepare("SELECT customer_name FROM customers WHERE id = ?");
                $cust_stmt->bind_param("s", $row['customer_id']);
                $cust_stmt->execute();
                $cust_result = $cust_stmt->get_result();
                if ($cust_row = $cust_result->fetch_assoc()) {
                    $customer_name = $cust_row['customer_name'] ?? 'N/A';
                }
                $cust_stmt->close();
            } catch (Exception $e) {
                // If customer_name doesn't exist, try name
                try {
                    $cust_stmt = $conn->prepare("SELECT name FROM customers WHERE id = ?");
                    $cust_stmt->bind_param("s", $row['customer_id']);
                    $cust_stmt->execute();
                    $cust_result = $cust_stmt->get_result();
                    if ($cust_row = $cust_result->fetch_assoc()) {
                        $customer_name = $cust_row['name'] ?? 'N/A';
                    }
                    $cust_stmt->close();
                } catch (Exception $e2) {
                    // Customer table or columns don't exist
                }
            }
        }
        $row['customer_name'] = $customer_name;
        $manifests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Manifests</title>

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
            <h1>Manifests</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Manifests</li>
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
                <h3 class="card-title">List of Manifests</h3>
                <div class="card-tools">
                  <a href="manifest-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="manifestsTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Manifest ID</th>
                    <th>Company</th>
                    <th>Customer</th>
                    <th>Manifest Date</th>
                    <th>Manifest Type</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($manifests as $manifest): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($manifest['manifest_id']); ?></td>
                    <td><?php echo htmlspecialchars($manifest['name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($manifest['customer_name'] ?? 'N/A'); ?></td>
                    <td><?php echo $manifest['manifest_date'] ? date('Y-m-d', strtotime($manifest['manifest_date'])) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars($manifest['manifest_type'] ?? 'N/A'); ?></td>
                    <td><?php echo $manifest['created_at'] ? date('Y-m-d H:i', strtotime($manifest['created_at'])) : 'N/A'; ?></td>
                    <td>
                      <a href="manifest-form.php?id=<?php echo htmlspecialchars($manifest['manifest_id']); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <a href="manifest-delete.php?id=<?php echo htmlspecialchars($manifest['manifest_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this manifest?')">
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
    $("#manifestsTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      "order": [[ 5, "desc" ]]
    });
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>
