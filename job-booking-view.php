<?php
session_start();
require 'db.php';
require 'functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . URL . '/job-booking-list.php');
    exit;
}

$conn->query("SET NAMES utf8mb4");

// Main booking
$stmt = $conn->prepare("
    SELECT jb.*, c.name as customer_name, c.code as customer_code, co.name as company_name
    FROM job_bookings jb
    LEFT JOIN customers c ON jb.customer_id = c.id
    LEFT JOIN companies co ON jb.company_id = co.id
    WHERE jb.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    header('Location: ' . URL . '/job-booking-list.php');
    exit;
}

// Sender: job_addresses + addresses + country
$stmt = $conn->prepare("
    SELECT ja.*, a.*, co.name as country_name
    FROM job_addresses ja
    INNER JOIN addresses a ON ja.address_id = a.id
    LEFT JOIN countries co ON a.country_id = co.id
    WHERE ja.booking_id = ? AND ja.party_role = 'SENDER'
");
$stmt->bind_param('i', $id);
$stmt->execute();
$sender = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Receiver (ja.signature_required for this booking's receiver)
$stmt = $conn->prepare("
    SELECT ja.instructions as ja_instructions, ja.signature_required as receiver_signature_required, a.*, co.name as country_name
    FROM job_addresses ja
    INNER JOIN addresses a ON ja.address_id = a.id
    LEFT JOIN countries co ON a.country_id = co.id
    WHERE ja.booking_id = ? AND ja.party_role = 'RECEIVER'
");
$stmt->bind_param('i', $id);
$stmt->execute();
$receiver = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Packages (with package_type, dg_type names)
$packages = [];
$r = $conn->query("
    SELECT jp.*, pt.name as package_type_name, dt.name as dg_type_name
    FROM job_packages jp
    LEFT JOIN package_types pt ON jp.package_type_id = pt.id
    LEFT JOIN dg_types dt ON jp.dg_type_id = dt.id
    WHERE jp.booking_id = " . (int)$id
);
if ($r) while ($row = $r->fetch_assoc()) $packages[] = $row;

// Tracking notifications
$notifications = [];
$r = $conn->query("
    SELECT jtn.*, nt.name as notification_type_name
    FROM job_tracking_notifications jtn
    LEFT JOIN notification_types nt ON jtn.notification_type_id = nt.id
    WHERE jtn.job_id = " . (int)$id
);
if ($r) while ($row = $r->fetch_assoc()) $notifications[] = $row;

// Attachments
$attachments = [];
$r = $conn->query("SELECT * FROM job_attachments WHERE booking_id = " . (int)$id);
if ($r) while ($row = $r->fetch_assoc()) $attachments[] = $row;

// Additional information (with dg_signatory name)
$addinfo = null;
$r = $conn->query("
    SELECT jai.*, ds.name as dg_signatory_name
    FROM job_additional_information jai
    LEFT JOIN dg_signatories ds ON jai.dg_signatory_id = ds.id
    WHERE jai.booking_id = " . (int)$id
);
if ($r && $row = $r->fetch_assoc()) $addinfo = $row;

$conn->close();

function out($v, $def = '-') {
    echo $v !== null && $v !== '' ? htmlspecialchars($v) : $def;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Job Booking | <?php out($booking['booking_id']); ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/dist/css/adminlte.min.css">
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
            <h1>Job Booking - <?php out($booking['booking_id']); ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/job-booking-list.php">Job Bookings</a></li>
              <li class="breadcrumb-item active">View</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h3 class="card-title">Booking Details</h3>
            <div class="card-tools">
              <a href="<?php echo URL; ?>/job-booking-print.php?id=<?php echo $id; ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-print"></i> Print</a>
              <a href="<?php echo URL; ?>/job-booking-list.php" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-sm table-bordered">
                  <tr><th width="180">Booking ID</th><td><strong><?php out($booking['booking_id']); ?></strong></td></tr>
                  <tr><th>Customer</th><td><?php out($booking['customer_name']); ?><?php if ($booking['customer_code']) echo ' (' . htmlspecialchars($booking['customer_code']) . ')'; ?></td></tr>
                  <tr><th>Company</th><td><?php out($booking['company_name']); ?></td></tr>
                  <tr><th>Customer Reference</th><td><?php out($booking['customer_reference']); ?></td></tr>
                  <tr><th>Receiver Reference</th><td><?php out($booking['receiver_reference']); ?></td></tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-bordered">
                  <tr><th width="180">Job Type</th><td><span class="badge badge-info"><?php out($booking['job_type']); ?></span></td></tr>
                  <tr><th>Status</th><td><span class="badge badge-secondary"><?php out($booking['status']); ?></span></td></tr>
                  <tr><th>Freight Ready By</th><td><?php echo $booking['freight_ready_by'] ? date('d M Y H:i', strtotime($booking['freight_ready_by'])) : '-'; ?></td></tr>
                  <tr><th>Created</th><td><?php echo $booking['created_at'] ? date('d M Y H:i', strtotime($booking['created_at'])) : '-'; ?></td></tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header"><h3 class="card-title mb-0">Sender</h3></div>
              <div class="card-body">
                <?php if ($sender): ?>
                  <table class="table table-sm table-bordered">
                    <tr><th width="140">Country</th><td><?php out($sender['country_name']); ?></td></tr>
                    <tr><th>Name</th><td><?php out($sender['name']); ?></td></tr>
                    <tr><th>Building</th><td><?php out($sender['building']); ?></td></tr>
                    <tr><th>Street No / Street</th><td><?php out($sender['street_no']); ?> <?php out($sender['street']); ?></td></tr>
                    <tr><th>Suburb</th><td><?php out($sender['suburb']); ?></td></tr>
                    <tr><th>City / State / Postcode</th><td><?php out($sender['city']); ?> <?php out($sender['state']); ?> <?php out($sender['postcode']); ?></td></tr>
                    <tr><th>Contact Person</th><td><?php out($sender['contact_person']); ?></td></tr>
                    <tr><th>Mobile / Phone</th><td><?php out($sender['mobile']); ?> / <?php out($sender['phone']); ?></td></tr>
                    <tr><th>Email</th><td><?php out($sender['email']); ?></td></tr>
                    <tr><th>Pickup Instruction</th><td><?php out($sender['pickup_instruction']); ?></td></tr>
                  </table>
                <?php else: ?>
                  <p class="text-muted mb-0">No sender address.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header"><h3 class="card-title mb-0">Receiver</h3></div>
              <div class="card-body">
                <?php if ($receiver): ?>
                  <table class="table table-sm table-bordered">
                    <tr><th width="140">Country</th><td><?php out($receiver['country_name']); ?></td></tr>
                    <tr><th>Name</th><td><?php out($receiver['name']); ?></td></tr>
                    <tr><th>Building</th><td><?php out($receiver['building']); ?></td></tr>
                    <tr><th>Street No / Street</th><td><?php out($receiver['street_no']); ?> <?php out($receiver['street']); ?></td></tr>
                    <tr><th>City / State / Postcode</th><td><?php out($receiver['city']); ?> <?php out($receiver['state']); ?> <?php out($receiver['postcode']); ?></td></tr>
                    <tr><th>Contact Person</th><td><?php out($receiver['contact_person']); ?></td></tr>
                    <tr><th>Mobile / Phone</th><td><?php out($receiver['mobile']); ?> / <?php out($receiver['phone']); ?></td></tr>
                    <tr><th>Email</th><td><?php out($receiver['email']); ?></td></tr>
                    <tr><th>Delivery Instruction</th><td><?php out($receiver['delivery_instruction']); ?></td></tr>
                    <tr><th>Signature Required</th><td><?php echo !empty($receiver['receiver_signature_required']) ? 'Yes' : 'No'; ?></td></tr>
                  </table>
                <?php else: ?>
                  <p class="text-muted mb-0">No receiver address.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="card card-primary mb-3">
          <div class="card-header"><h3 class="card-title mb-0">Packages</h3></div>
          <div class="card-body p-0">
            <?php if (count($packages) > 0): ?>
              <table class="table table-bordered table-sm mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Units</th>
                    <th>Weight (Kg)</th>
                    <th>Dimensions (L×W×H cm)</th>
                    <th>Cubic (m³)</th>
                    <th>Package Type</th>
                    <th>DG Type</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($packages as $p): ?>
                    <tr>
                      <td><?php out($p['units']); ?></td>
                      <td><?php out($p['weight_kg']); ?></td>
                      <td><?php out($p['length_cm']); ?> × <?php out($p['width_cm']); ?> × <?php out($p['height_cm']); ?></td>
                      <td><?php out($p['cubic_m3']); ?></td>
                      <td><?php out($p['package_type_name']); ?></td>
                      <td><?php out($p['dg_type_name']); ?></td>
                      <td><?php out($p['remarks']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p class="text-muted p-3 mb-0">No packages.</p>
            <?php endif; ?>
          </div>
        </div>

        <div class="card card-primary mb-3">
          <div class="card-header"><h3 class="card-title mb-0">Tracking Notifications</h3></div>
          <div class="card-body p-0">
            <?php if (count($notifications) > 0): ?>
              <table class="table table-bordered table-sm mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Communication Type</th>
                    <th>Contact</th>
                    <th>Notification Type</th>
                    <th>Sent</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($notifications as $n): ?>
                    <tr>
                      <td><?php out($n['communication_type']); ?></td>
                      <td><?php out($n['contact']); ?></td>
                      <td><?php out($n['notification_type_name']); ?></td>
                      <td><?php echo !empty($n['is_sent']) ? 'Yes' : 'No'; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p class="text-muted p-3 mb-0">No tracking notifications.</p>
            <?php endif; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary mb-3">
              <div class="card-header"><h3 class="card-title mb-0">Attachments</h3></div>
              <div class="card-body">
                <?php if (count($attachments) > 0): ?>
                  <ul class="list-unstyled mb-0">
                    <?php foreach ($attachments as $a): ?>
                      <li>
                        <a href="<?php echo URL; ?>/<?php echo htmlspecialchars($a['file_path']); ?>" target="_blank" class="text-primary">
                          <i class="fas fa-paperclip"></i> <?php echo htmlspecialchars(basename($a['file_path'])); ?>
                        </a>
                        <small class="text-muted"> (<?php echo $a['uploaded_at'] ? date('d M Y H:i', strtotime($a['uploaded_at'])) : ''; ?>)</small>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php else: ?>
                  <p class="text-muted mb-0">No attachments.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-primary mb-3">
              <div class="card-header"><h3 class="card-title mb-0">Additional Information</h3></div>
              <div class="card-body">
                <?php if ($addinfo): ?>
                  <table class="table table-sm table-bordered mb-0">
                    <tr><th width="140">Insurance Type</th><td><?php out($addinfo['insurance_type']); ?></td></tr>
                    <tr><th>DG Signatory</th><td><?php out($addinfo['dg_signatory_name']); ?></td></tr>
                    <tr><th>Customer Reference 2</th><td><?php out($addinfo['customer_reference_2']); ?></td></tr>
                    <tr><th>Receiver Reference 2</th><td><?php out($addinfo['receiver_reference_2']); ?></td></tr>
                  </table>
                <?php else: ?>
                  <p class="text-muted mb-0">No additional information.</p>
                <?php endif; ?>
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
<script src="<?php echo URL; ?>/dist/js/adminlte.min.js"></script>
</body>
</html>
