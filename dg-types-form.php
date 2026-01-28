<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$dg_type = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM dg_types WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dg_type = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | DG Type</title>

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
            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> DG Type</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="dg-types-list.php">DG Types</a></li>
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

      <form action="dg-types-store.php" method="POST">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($dg_type['id']); ?>">
        <?php endif; ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">DG Type Information</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name">
                          Name
                        </label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               placeholder="e.g., Flammable Liquid, Corrosive"
                               value="<?php echo $edit ? htmlspecialchars($dg_type['name'] ?? '') : ''; ?>"
                               maxlength="100">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="un_number">
                          UN Number
                        </label>
                        <input type="text"
                               class="form-control"
                               id="un_number"
                               name="un_number"
                               placeholder="e.g., UN1203, UN1263"
                               value="<?php echo $edit ? htmlspecialchars($dg_type['un_number'] ?? '') : ''; ?>"
                               maxlength="50">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Card Footer: Buttons -->
                <div class="card-footer">
                  <a href="dg-types-list.php" class="btn btn-default">Cancel</a>
                  <button type="submit" class="btn btn-primary float-right">
                    <?php echo $edit ? 'Update' : 'Save'; ?> DG Type
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
