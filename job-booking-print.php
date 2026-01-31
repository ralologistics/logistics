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
    $_SESSION['job_booking_error'] = 'Booking not found.';
    header('Location: ' . URL . '/job-booking-list.php');
    exit;
}

// Sender
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

// Receiver
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

// Packages
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

// Additional information
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
  <title>Job Booking - <?php out($booking['booking_id']); ?></title>
  <link rel="stylesheet" href="<?php echo URL; ?>/dist/css/adminlte.min.css">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { font-size: 12px; }
      .card { break-inside: avoid; }
      .table { font-size: 11px; }
    }
    .print-section { margin-bottom: 1rem; }
    .print-section h4 { border-bottom: 1px solid #dee2e6; padding-bottom: 4px; margin-bottom: 8px; font-size: 14px; }
    table.print-table { width: 100%; border-collapse: collapse; }
    table.print-table th, table.print-table td { border: 1px solid #dee2e6; padding: 6px 8px; text-align: left; }
    table.print-table th { background: #f8f9fa; font-weight: 600; width: 28%; }
  </style>
</head>
<body class="p-4">
  <div class="no-print mb-3">
    <a href="<?php echo URL; ?>/job-booking-view.php?id=<?php echo $id; ?>" class="btn btn-secondary">Back to View</a>
    <a href="<?php echo URL; ?>/job-booking-list.php" class="btn btn-outline-secondary">Job Bookings List</a>
    <button type="button" class="btn btn-primary" onclick="window.print();">Print</button>
  </div>

  <div class="text-center mb-4">
    <h2>JOB BOOKING</h2>
    <h4 class="text-primary"><?php out($booking['booking_id']); ?></h4>
    <p class="text-muted mb-0">Printed on: <?php echo date('d M Y H:i'); ?></p>
  </div>

  <!-- Booking Details -->
  <div class="card print-section">
    <div class="card-header bg-primary text-white py-2"><h4 class="mb-0">Booking Details</h4></div>
    <div class="card-body p-3">
      <div class="row">
        <div class="col-md-6">
          <table class="print-table table table-sm table-bordered mb-0">
            <tr><th>Booking ID</th><td><strong><?php out($booking['booking_id']); ?></strong></td></tr>
            <tr><th>Customer</th><td><?php out($booking['customer_name']); ?><?php if (!empty($booking['customer_code'])) echo ' (' . htmlspecialchars($booking['customer_code']) . ')'; ?></td></tr>
            <tr><th>Company</th><td><?php out($booking['company_name']); ?></td></tr>
            <tr><th>Customer Reference</th><td><?php out($booking['customer_reference']); ?></td></tr>
            <tr><th>Receiver Reference</th><td><?php out($booking['receiver_reference']); ?></td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="print-table table table-sm table-bordered mb-0">
            <tr><th>Job Type</th><td><?php out($booking['job_type']); ?></td></tr>
            <tr><th>Status</th><td><?php out($booking['status']); ?></td></tr>
            <tr><th>Freight Ready By</th><td><?php echo $booking['freight_ready_by'] ? date('d M Y H:i', strtotime($booking['freight_ready_by'])) : '-'; ?></td></tr>
            <tr><th>Created</th><td><?php echo $booking['created_at'] ? date('d M Y H:i', strtotime($booking['created_at'])) : '-'; ?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Sender & Receiver -->
  <div class="row">
    <div class="col-md-6">
      <div class="card print-section">
        <div class="card-header bg-secondary text-white py-2"><h4 class="mb-0">Sender</h4></div>
        <div class="card-body p-3">
          <?php if ($sender): ?>
            <table class="print-table table table-sm table-bordered mb-0">
              <tr><th>Country</th><td><?php out($sender['country_name']); ?></td></tr>
              <tr><th>Name</th><td><?php out($sender['name']); ?></td></tr>
              <tr><th>Address</th><td><?php out($sender['building']); ?> <?php out($sender['street_no']); ?> <?php out($sender['street']); ?></td></tr>
              <tr><th>Suburb / City</th><td><?php out($sender['suburb']); ?> / <?php out($sender['city']); ?></td></tr>
              <tr><th>State / Postcode</th><td><?php out($sender['state']); ?> <?php out($sender['postcode']); ?></td></tr>
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
      <div class="card print-section">
        <div class="card-header bg-secondary text-white py-2"><h4 class="mb-0">Receiver</h4></div>
        <div class="card-body p-3">
          <?php if ($receiver): ?>
            <table class="print-table table table-sm table-bordered mb-0">
              <tr><th>Country</th><td><?php out($receiver['country_name']); ?></td></tr>
              <tr><th>Name</th><td><?php out($receiver['name']); ?></td></tr>
              <tr><th>Address</th><td><?php out($receiver['building']); ?> <?php out($receiver['street_no']); ?> <?php out($receiver['street']); ?></td></tr>
              <tr><th>City / State / Postcode</th><td><?php out($receiver['city']); ?> / <?php out($receiver['state']); ?> <?php out($receiver['postcode']); ?></td></tr>
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

  <!-- Packages -->
  <div class="card print-section">
    <div class="card-header bg-info text-white py-2"><h4 class="mb-0">Packages</h4></div>
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

  <!-- Tracking Notifications -->
  <div class="card print-section">
    <div class="card-header bg-info text-white py-2"><h4 class="mb-0">Tracking Notifications</h4></div>
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

  <!-- Additional Information & Attachments -->
  <div class="row">
    <div class="col-md-6">
      <div class="card print-section">
        <div class="card-header py-2"><h4 class="mb-0">Additional Information</h4></div>
        <div class="card-body p-3">
          <?php if ($addinfo): ?>
            <table class="print-table table table-sm table-bordered mb-0">
              <tr><th>Insurance Type</th><td><?php out($addinfo['insurance_type']); ?></td></tr>
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
    <div class="col-md-6">
      <div class="card print-section">
        <div class="card-header py-2"><h4 class="mb-0">Attachments</h4></div>
        <div class="card-body p-3">
          <?php if (count($attachments) > 0): ?>
            <ul class="list-unstyled mb-0">
              <?php foreach ($attachments as $a): ?>
                <li><?php echo htmlspecialchars(basename($a['file_path'])); ?> <small class="text-muted">(<?php echo $a['uploaded_at'] ? date('d M Y', strtotime($a['uploaded_at'])) : ''; ?>)</small></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted mb-0">No attachments.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <p class="text-center text-muted small mt-4">— End of Job Booking —</p>

  <script>
    // Optional: auto-print on load (comment out if you prefer manual print only)
    // window.onload = function() { window.print(); }
  </script>
</body>
</html>
