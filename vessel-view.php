<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: vessel-list.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT v.*, c.name as country_name, st.type_name as ship_type_name 
                        FROM vessels v 
                        LEFT JOIN countries c ON v.country_id = c.id 
                        LEFT JOIN ship_types st ON v.ship_type_id = st.id 
                        WHERE v.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vessel = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$vessel) {
    header("Location: vessel-list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | View Vessel</title>

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
            <h1>View Vessel</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="vessel-list.php">Vessels</a></li>
              <li class="breadcrumb-item active">View</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Vessel Details</h3>
                <div class="card-tools">
                  <a href="vessel-form.php?id=<?php echo $vessel['id']; ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <a href="vessel-list.php" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                  </a>
                </div>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tr>
                    <th width="200">Vessel Name</th>
                    <td><?php echo htmlspecialchars($vessel['name']); ?></td>
                  </tr>
                  <tr>
                    <th>Country</th>
                    <td><?php echo htmlspecialchars($vessel['country_name'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>Ship Type</th>
                    <td><?php echo htmlspecialchars($vessel['ship_type_name'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>IMO Number</th>
                    <td><?php echo htmlspecialchars($vessel['imo_number'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>MMSI</th>
                    <td><?php echo htmlspecialchars($vessel['mmsi'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>Call Sign</th>
                    <td><?php echo htmlspecialchars($vessel['call_sign'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>Built Year</th>
                    <td><?php echo $vessel['built_year'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Length (m)</th>
                    <td><?php echo $vessel['length_m'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Width (m)</th>
                    <td><?php echo $vessel['width_m'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Draught (m)</th>
                    <td><?php echo $vessel['draught_m'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Gross Tonnage</th>
                    <td><?php echo $vessel['gross_tonnage'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Net Tonnage</th>
                    <td><?php echo $vessel['net_tonnage'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Dead Weight</th>
                    <td><?php echo $vessel['dead_weight'] ?? 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Created Date</th>
                    <td><?php echo $vessel['created_at'] ? date('d-m-Y H:i', strtotime($vessel['created_at'])) : 'N/A'; ?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- jQuery -->
<script src="/ralo/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
</body>
</html>
