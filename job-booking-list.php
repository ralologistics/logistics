<?php
session_start();
require 'db.php';
require 'functions.php';

$conn->query("SET NAMES utf8mb4");
$sql = "SELECT jb.*, c.name as customer_name, co.name as company_name
        FROM job_bookings jb
        LEFT JOIN customers c ON jb.customer_id = c.id
        LEFT JOIN companies co ON jb.company_id = co.id
        ORDER BY jb.created_at DESC";
$result = $conn->query($sql);
$bookings = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Job Bookings List | NAVBRIDGE</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
            <h1>Job Bookings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Job Bookings</li>
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
                <h3 class="card-title">List of Job Bookings</h3>
                <div class="card-tools">
                  <a href="<?php echo URL; ?>/job-booking-form.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Job Booking
                  </a>
                </div>
              </div>
              <div class="card-body">
                <table id="jobBookingsTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Booking ID</th>
                      <th>Customer</th>
                      <th>Company</th>
                      <th>Customer Ref</th>
                      <th>Receiver Ref</th>
                      <th>Freight Ready By</th>
                      <th>Job Type</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($bookings as $b): ?>
                      <tr>
                        <td><?php echo (int)$b['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($b['booking_id'] ?? '-'); ?></strong></td>
                        <td><?php echo htmlspecialchars($b['customer_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($b['company_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($b['customer_reference'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($b['receiver_reference'] ?? '-'); ?></td>
                        <td><?php echo $b['freight_ready_by'] ? date('d M Y H:i', strtotime($b['freight_ready_by'])) : '-'; ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($b['job_type'] ?? '-'); ?></span></td>
                        <td>
                          <?php
                          $status = $b['status'] ?? 'DRAFT';
                          $badge = 'secondary';
                          if (in_array($status, ['APPROVED', 'COMPLETED', 'ACTIVE'])) $badge = 'success';
                          elseif (in_array($status, ['REJECTED'])) $badge = 'danger';
                          elseif (in_array($status, ['SUBMITTED', 'IN_PROCESS', 'URGENT'])) $badge = 'warning';
                          ?>
                          <span class="badge badge-<?php echo $badge; ?>"><?php echo htmlspecialchars($status); ?></span>
                        </td>
                        <td><?php echo $b['created_at'] ? date('d M Y H:i', strtotime($b['created_at'])) : '-'; ?></td>
                        <td>
                          <a href="<?php echo URL; ?>/job-booking-view.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="<?php echo URL; ?>/job-booking-print.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-secondary btn-sm" title="Print" target="_blank">
                            <i class="fas fa-print"></i>
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

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Version 2.0.3</div>
    <strong>Copyright &copy; 2026 Navare Solutions.</strong> All rights reserved.
  </footer>
</div>

<script src="<?php echo URL; ?>/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo URL; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URL; ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo URL; ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo URL; ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo URL; ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo URL; ?>/dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $("#jobBookingsTable").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "order": [[0, "desc"]],
      "pageLength": 25
    });
  });
</script>
</body>
</html>
