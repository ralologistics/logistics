<?php
session_start();

require 'db.php';

$result = $conn->query("SELECT * FROM endorsements ORDER BY name ASC");
$items = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Endorsements</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="/ralo/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/ralo/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<?php include('top-navbar.php'); ?>
<?php include('left-navbar.php'); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Endorsements</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Endorsements</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Endorsements</h3>
                <div class="card-tools">
                  <a href="endorsements-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                  </a>
                </div>
              </div>
              <div class="card-body">
                <table id="itemsTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($items as $it): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($it['name']); ?></td>
                    <td>
                      <?php if ($it['status']): ?>
                        <span class="badge badge-success">Active</span>
                      <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="endorsements-form.php?id=<?php echo htmlspecialchars($it['id']); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <a href="endorsements-delete.php?id=<?php echo htmlspecialchars($it['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this endorsement?')">
                        <i class="fas fa-trash"></i> Delete
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="/ralo/plugins/jquery/jquery.min.js"></script>
<script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/ralo/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/ralo/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/ralo/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/ralo/dist/js/adminlte.min.js"></script>
<script src="/ralo/dist/js/demo.js"></script>
<script>
  $(function () {
    $("#itemsTable").DataTable({"responsive": true, "autoWidth": false, "order": [[0, "asc"]]});
  });
</script>
<?php include('footer.php'); ?>
</body>
</html>
