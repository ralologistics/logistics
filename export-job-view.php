<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

if (!isset($_GET['id'])) {
    header("Location: export-job-list.php");
    exit;
}

require 'db.php';

$id = (int)$_GET['id'];

// Get export job details
$stmt = $conn->prepare("SELECT ejb.*, 
                        c.name as customer_name, 
                        c.code as customer_code,
                        jb.booking_id as booking_ref, 
                        v.name as vessel_name, 
                        s.name as shipping_name
                        FROM export_job_bookings ejb 
                        LEFT JOIN customers c ON ejb.customer_id = c.id 
                        LEFT JOIN job_bookings jb ON ejb.booking_id = jb.id 
                        LEFT JOIN vessels v ON ejb.vessel_id = v.id 
                        LEFT JOIN shippings s ON ejb.shipping__id = s.id 
                        WHERE ejb.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$job) {
    header("Location: export-job-list.php");
    exit;
}

// Get containers
$stmt = $conn->prepare("SELECT c.*, 
                        ic.code as iso_code,
                        dt.name as door_type_name
                        FROM containers c
                        LEFT JOIN iso_codes ic ON c.iso_code_id = ic.id
                        LEFT JOIN door_types dt ON c.door_type_id = dt.id
                        WHERE c.job_type = 'export' AND c.job_id = ? 
                        ORDER BY c.id ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$containers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get container IDs for services
$containerIds = [];
if (!empty($containers)) {
    foreach ($containers as $c) {
        $containerIds[] = (int)$c['id'];
    }
}

// Get services
$services = [];
if (!empty($containerIds)) {
    $containerIdsStr = implode(',', array_map('intval', $containerIds));
    $servicesQuery = "
        SELECT DISTINCT ijs.service_id, ijs.container_id, c.booking_id, s.name AS service_name
        FROM import_job_services ijs
        INNER JOIN services s ON s.id = ijs.service_id
        INNER JOIN containers c ON c.id = ijs.container_id
        WHERE ijs.job_type = 'export' AND ijs.container_id IN ($containerIdsStr)
        ORDER BY ijs.id ASC
    ";
    $result = $conn->query($servicesQuery);
    $services = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Get notes
$notes = [];
if (!empty($containers)) {
    $containerBookingIds = [];
    foreach ($containers as $c) {
        if (!empty($c['booking_id'])) {
            $containerBookingIds[] = $conn->real_escape_string($c['booking_id']);
        }
    }
    
    if (!empty($containerBookingIds)) {
        $bookingIdsStr = "'" . implode("','", array_unique($containerBookingIds)) . "'";
        $notesQuery = "
            SELECT ijn.id, ijn.booking_id, ijn.endorsement_id, e.name AS endorsement_name, ijn.note
            FROM import_job_notes ijn
            INNER JOIN endorsements e ON e.id = ijn.endorsement_id
            WHERE ijn.job_type = 'export' AND ijn.booking_id IN ($bookingIdsStr)
            ORDER BY ijn.id ASC
        ";
        $result = $conn->query($notesQuery);
        $notes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Export Job View</title>

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
            <h1>Export Job Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="export-job-list.php">Export Jobs</a></li>
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
          <!-- Left Column: Customer Details -->
          <div class="col-md-3">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Customer Details</h3>
              </div>
              <div class="card-body">
                <dl>
                  <dt>Job No</dt>
                  <dd><?php echo htmlspecialchars($job['job_no'] ?? 'N/A'); ?></dd>
                  
                  <dt>Customer</dt>
                  <dd>
                    <?php echo htmlspecialchars($job['customer_name'] ?? 'N/A'); ?>
                    <?php if (!empty($job['customer_code'])): ?>
                      <br><small class="text-muted">Code: <?php echo htmlspecialchars($job['customer_code']); ?></small>
                    <?php endif; ?>
                  </dd>
                  
                  <dt>Booking</dt>
                  <dd><?php echo htmlspecialchars($job['booking_ref'] ?? 'N/A'); ?></dd>
                  
                  <dt>Document Received</dt>
                  <dd><?php echo !empty($job['document_received_at']) ? date('Y-m-d H:i', strtotime($job['document_received_at'])) : 'N/A'; ?></dd>
                  
                  <dt>Shipping</dt>
                  <dd><?php echo htmlspecialchars($job['shipping_name'] ?? 'N/A'); ?></dd>
                  
                  <dt>Vessel</dt>
                  <dd><?php echo htmlspecialchars($job['vessel_name'] ?? 'N/A'); ?></dd>
                  
                  <dt>Voyage</dt>
                  <dd><?php echo htmlspecialchars($job['voyage'] ?? 'N/A'); ?></dd>
                  
                  <dt>From Location</dt>
                  <dd><i class="fas fa-map-marker-alt text-info"></i> <?php echo htmlspecialchars($job['from_location'] ?? 'N/A'); ?></dd>
                  
                  <dt>To Location</dt>
                  <dd><i class="fas fa-map-marker-alt text-info"></i> <?php echo htmlspecialchars($job['to_location'] ?? 'N/A'); ?></dd>
                </dl>
              </div>
              <div class="card-footer">
                <a href="export-job-form.php?id=<?php echo $id; ?>" class="btn btn-warning btn-sm">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a href="export-job-list.php" class="btn btn-default btn-sm">
                  <i class="fas fa-arrow-left"></i> Back
                </a>
              </div>
            </div>
          </div>

          <!-- Right Column: Container Details, Services, Notes -->
          <div class="col-md-9">
            <!-- Containers -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Container Details</h3>
              </div>
              <div class="card-body">
                <?php if (empty($containers)): ?>
                  <p class="text-muted">No containers found.</p>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Booking ID</th>
                          <th>Reference</th>
                          <th>Container No</th>
                          <th>ISO Code</th>
                          <th>Weight</th>
                          <th>No. Of Containers</th>
                          <th>Cut Off Date</th>
                          <th>Grid Position</th>
                          <th>Available Date</th>
                          <th>Door</th>
                          <th>Port</th>
                          <th>Random Number</th>
                          <th>Release/ECN</th>
                          <th>Options</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($containers as $idx => $c): ?>
                          <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><?php echo htmlspecialchars($c['booking_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['reference'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['container_no'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['iso_code'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['weight'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['no_of_containers'] ?? '1'); ?></td>
                            <td><?php echo !empty($c['cut_off_date']) ? date('Y-m-d', strtotime($c['cut_off_date'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($c['grid_position'] ?? 'N/A'); ?></td>
                            <td><?php echo !empty($c['available_date']) ? date('Y-m-d', strtotime($c['available_date'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($c['door_type_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['port_pin_no'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['random_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['release_ecn_number'] ?? 'N/A'); ?></td>
                            <td>
                              <?php if (!empty($c['xray'])): ?><span class="badge badge-info">XRay</span><?php endif; ?>
                              <?php if (!empty($c['dgs'])): ?><span class="badge badge-warning">DGS</span><?php endif; ?>
                              <?php if (!empty($c['live_ul'])): ?><span class="badge badge-success">Live Load</span><?php endif; ?>
                              <?php if (!empty($c['ready_now'])): ?><span class="badge badge-primary">Ready Now</span><?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Services -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Additional Services</h3>
              </div>
              <div class="card-body">
                <?php if (empty($services)): ?>
                  <p class="text-muted">No services found.</p>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Booking ID</th>
                          <th>Service</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($services as $idx => $srv): ?>
                          <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><?php echo htmlspecialchars($srv['booking_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($srv['service_name']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Notes -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Notes</h3>
              </div>
              <div class="card-body">
                <?php if (empty($notes)): ?>
                  <p class="text-muted">No notes found.</p>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Booking ID</th>
                          <th>Endorsement</th>
                          <th>Note</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($notes as $idx => $n): ?>
                          <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><?php echo htmlspecialchars($n['booking_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($n['endorsement_name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($n['note'] ?? '')); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
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
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
</body>
</html>
