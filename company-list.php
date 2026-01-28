<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$result = $conn->query("SELECT c.*, co.name as country_name FROM companies c LEFT JOIN countries co ON c.country_id = co.id ORDER BY c.name ASC");
$companies = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Companies</title>

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
            <h1>Companies</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Companies</li>
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
                <h3 class="card-title">List of Companies</h3>
                <div class="card-tools">
                  <a href="company-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Company
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="companiesTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Company Code</th>
                    <th>Name</th>
                    <th>Legal Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($companies as $company): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($company['id']); ?></td>
                      <td>
                        <?php if (!empty($company['logo']) && file_exists($company['logo'])): ?>
                          <img src="<?php echo htmlspecialchars($company['logo']); ?>" alt="Logo" style="max-width: 50px; max-height: 50px;">
                        <?php else: ?>
                          <span class="text-muted">No Logo</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo htmlspecialchars($company['company_code'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($company['name']); ?></td>
                      <td><?php echo htmlspecialchars($company['legal_name'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($company['email'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($company['phone'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($company['country_name'] ?? ''); ?></td>
                      <td>
                        <?php if ($company['status'] == 1): ?>
                          <span class="badge badge-success">Active</span>
                        <?php else: ?>
                          <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="company-form.php?id=<?php echo $company['id']; ?>" class="btn btn-warning btn-sm">
                          <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="company-delete.php?id=<?php echo $company['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this company?');">
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
      </div>
      <!-- /.container-fluid -->
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
<!-- DataTables  & Plugins -->
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
    $("#companiesTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#companiesTable_wrapper .col-md-6:eq(0)');
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>