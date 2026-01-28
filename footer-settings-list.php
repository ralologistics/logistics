<?php
session_start();

require 'db.php';

$result = $conn->query("SELECT * FROM footer_settings ORDER BY id DESC");
$settings = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Footer Settings</title>

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
            <h1>Footer Settings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/index.php">Home</a></li>
              <li class="breadcrumb-item active">Footer Settings</li>
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
                <h3 class="card-title">Footer Settings List</h3>
                <div class="card-tools">
                  <a href="<?php echo URL; ?>/footer-settings-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Setting
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (isset($_GET['success'])): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Operation completed successfully.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>

                <table id="footerSettingsTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Site Name</th>
                      <th>Copyright Start Year</th>
                      <th>Version</th>
                      <th>Status</th>
                      <th>Created At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($settings as $setting): ?>
                      <tr>
                        <td><?php echo $setting['id']; ?></td>
                        <td><?php echo htmlspecialchars($setting['site_name']); ?></td>
                        <td><?php echo $setting['copyright_start_year']; ?></td>
                        <td><?php echo htmlspecialchars($setting['version']); ?></td>
                        <td>
                          <?php if ($setting['status'] == 1): ?>
                            <span class="badge badge-success">Active</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                          <?php endif; ?>
                        </td>
                        <td><?php echo $setting['created_at']; ?></td>
                        <td>
                          <a href="<?php echo URL; ?>/footer-settings-form.php?id=<?php echo $setting['id']; ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-edit"></i> Edit
                          </a>
                          <a href="<?php echo URL; ?>/footer-settings-delete.php?id=<?php echo $setting['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">
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
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include('footer.php'); ?>
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
<!-- Page specific script -->
<script>
  $(function () {
    $('#footerSettingsTable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>
