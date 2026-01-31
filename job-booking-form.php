<?php
session_start();
require 'db.php';
require 'functions.php';

// Load lookup data
$countries = [];
$companies = [];
$customers = [];
$package_types = [];
$dg_types = [];
$notification_types = [];
$additional_services = [];
$dg_signatories = [];

if ($r = $conn->query("SELECT id, name FROM countries ORDER BY name")) $countries = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name FROM companies ORDER BY name")) $companies = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name, code FROM customers ORDER BY name")) $customers = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name FROM package_types WHERE is_active = 1 ORDER BY name")) $package_types = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name FROM dg_types ORDER BY name")) $dg_types = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name FROM notification_types WHERE is_active = 1 ORDER BY name")) $notification_types = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, service_name FROM additional_services WHERE status = 1 ORDER BY service_name")) $additional_services = $r->fetch_all(MYSQLI_ASSOC);
if ($r = $conn->query("SELECT id, name FROM dg_signatories WHERE status = 1 ORDER BY name")) $dg_signatories = $r->fetch_all(MYSQLI_ASSOC);
$conn->close();

$communication_types = ['EMAIL' => 'Email', 'PHONE' => 'Phone', 'SMS' => 'SMS', 'WHATSAPP' => 'WhatsApp', 'PUSH' => 'Push'];
$insurance_types = ['Owners Risk', 'Carriers Risk', 'All Risk', 'Total Loss Only', 'Third Party', 'Limited Carrier Liability'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Job Booking | NAVBRIDGE</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL; ?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <style>
    .card-header.bg-primary { background: #007bff !important; color: #fff; }
    .job-booking-top .form-group { margin-bottom: 0.5rem; }
    .package-table th, .package-table td { vertical-align: middle; }
    .notification-row td { padding: 0.25rem 0.5rem; }
    .btn-remove-row { padding: 0.2rem 0.5rem; }
    /* Customer dropdown: no border */
    .customer-select-wrap .select2-container--bootstrap4 .select2-selection,
    .customer-select-wrap .select2-container--default .select2-selection { border: none !important; box-shadow: none !important; }
    .customer-select-wrap .select2-container { width: 100% !important; }
    .customer-search-wrap #customer_search {
      border-top: none; border-left: none; border-right: none;
      border-bottom: 1px solid #ced4da; border-radius: 0;
      box-shadow: none;
    }
    .customer-search-wrap #customer_search:focus { border-bottom-color: #80bdff; box-shadow: none; outline: 0; }
    .customer-search-results { box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 1px solid #ced4da; border-radius: 4px; }
    .customer-search-results .list-group-item { cursor: pointer; border-left: none; border-right: none; }
    .customer-search-results .list-group-item:first-child { border-top: none; }
    .company-search-wrap #company_search {
      border-top: none; border-left: none; border-right: none;
      border-bottom: 1px solid #ced4da; border-radius: 0;
      box-shadow: none;
    }
    .company-search-wrap #company_search:focus { border-bottom-color: #80bdff; box-shadow: none; outline: 0; }
    .company-search-results { box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 1px solid #ced4da; border-radius: 4px; }
    .company-search-results .list-group-item { cursor: pointer; border-left: none; border-right: none; }
    .company-search-results .list-group-item:first-child { border-top: none; }
    .customer-ref-wrap .input-group { border: none; box-shadow: none; }
    .customer-ref-wrap .form-control {
      border-top: none; border-left: none; border-right: none;
      border-bottom: 1px solid #ced4da; border-radius: 0; box-shadow: none;
    }
    .customer-ref-wrap .form-control:focus { border-bottom-color: #80bdff; box-shadow: none; outline: 0; }
    .customer-ref-wrap .input-group-append .btn { border: none; background: transparent; color: #6c757d; }
    .customer-ref-wrap .input-group-append .btn:hover { color: #495057; background: transparent; }
    .receiver-ref-wrap .form-control {
      border-top: none; border-left: none; border-right: none;
      border-bottom: 1px solid #ced4da; border-radius: 0; box-shadow: none;
    }
    .receiver-ref-wrap .form-control:focus { border-bottom-color: #80bdff; box-shadow: none; outline: 0; }
    /* Freight Ready By: dotted bottom only, minimal buttons */
    .freight-ready-by-wrap .input-group { border: none; box-shadow: none; background: #fff; }
    .freight-ready-by-wrap .form-control {
      border: none; border-radius: 0; background: #fff;
      border-bottom: 1px dotted #6c757d;
      color: #495057;
      position: relative; z-index: 1;
    }
    .freight-ready-by-wrap .form-control:focus { border-bottom-color: #80bdff; box-shadow: none; outline: 0; }
    .freight-ready-by-wrap .input-group-append .btn {
      border: none; background: transparent; color: #6c757d;
      padding: 0.25rem 0.4rem;
    }
    .freight-ready-by-wrap .input-group-append .btn:hover { color: #495057; background: transparent; }
    .freight-ready-by-wrap .input-group-append .btn.calendar-btn {
      background: #e9ecef; border-radius: 50%; width: 32px; height: 32px;
      display: inline-flex; align-items: center; justify-content: center;
      padding: 0;
    }
    .freight-ready-by-wrap .input-group-append .btn.calendar-btn:hover { background: #dee2e6; }
    .freight-ready-by-wrap .input-group { position: relative; }
    .freight-ready-by-wrap .freight-placeholder {
      position: absolute; left: 0; top: 0; bottom: 0; right: 70px;
      display: flex; align-items: center;
      padding: 0 12px; margin: 0;
      color: #6c757d; font-size: 0.9rem; pointer-events: none;
      z-index: 2;
    }
    .freight-ready-by-wrap .freight-placeholder.hide { display: none !important; }
    /* All form inputs/selects: bottom border only (no top/sides) */
    .job-booking-form .form-control,
    .job-booking-form select.form-control,
    .job-booking-form textarea.form-control {
      border-top: none; border-left: none; border-right: none;
      border-bottom: 1px solid #ced4da; border-radius: 0; box-shadow: none;
    }
    .job-booking-form .form-control:focus,
    .job-booking-form select.form-control:focus,
    .job-booking-form textarea.form-control:focus {
      border-bottom-color: #80bdff; box-shadow: none; outline: 0;
    }
  </style>
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
            <h1>Job Booking</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="<?php echo URL; ?>/job-booking-list.php">Job Bookings</a></li>
              <li class="breadcrumb-item active">New Job Booking</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <?php if (!empty($_SESSION['job_booking_error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo htmlspecialchars($_SESSION['job_booking_error']); unset($_SESSION['job_booking_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['job_booking_success'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo htmlspecialchars($_SESSION['job_booking_success']); unset($_SESSION['job_booking_success']); ?>
        </div>
      <?php endif; ?>
      <form id="jobBookingForm" class="job-booking-form" action="job-booking-store.php" method="POST" enctype="multipart/form-data">
        <div class="container-fluid">

          <!-- Top row: Customer Name/Code, Company, Customer Ref, Receiver Ref, Freight Ready By -->
          <div class="card card-outline card-primary mb-3">
            <div class="card-body">
              <div class="row job-booking-top">
                <div class="col-md-3 customer-search-wrap" style="position:relative;">
                  <input type="text" class="form-control" id="customer_search" placeholder="Customer Name / Customer Code" autocomplete="off">
                  <input type="hidden" name="customer_id" id="customer_id" value="">
                  <input type="hidden" name="customer_code" id="customer_code" value="">
                  <div class="customer-search-results list-group" id="customerSearchResults" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1000; max-height:220px; overflow-y:auto;"></div>
                </div>
                <div class="col-md-3 company-search-wrap" style="position:relative;">
                  <input type="text" class="form-control" id="company_search" placeholder="Company" autocomplete="off">
                  <input type="hidden" name="company_id" id="company_id" value="">
                  <div class="company-search-results list-group" id="companySearchResults" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1000; max-height:220px; overflow-y:auto;"></div>
                </div>
                <div class="col-md-2 customer-ref-wrap">
                  <div class="input-group">
                    <input type="text" class="form-control" name="customer_reference" placeholder="Customer Reference/ Order Number">
                    <span class="input-group-append"><button type="button" class="btn clear-field"><i class="fas fa-times"></i></button></span>
                  </div>
                </div>
                <div class="col-md-2 receiver-ref-wrap">
                  <input type="text" class="form-control" name="receiver_reference" placeholder="Receiver Reference">
                </div>
                <div class="col-md-2 freight-ready-by-wrap">
                  <div class="input-group">
                    <span class="freight-placeholder" id="freightPlaceholder">Freight Ready By</span>
                    <input type="datetime-local" class="form-control" name="freight_ready_by" id="freight_ready_by" placeholder="Freight Ready By">
                    <span class="input-group-append">
                      <button type="button" class="btn clear-field" title="Clear"><i class="fas fa-times"></i></button>
                      <button type="button" class="btn calendar-btn" id="freightCalendarBtn" title="Pick date"><i class="far fa-calendar-alt"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sender | Receiver -->
          <div class="row">
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title mb-0">Sender</h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <select class="form-control" name="sender_country_id" required>
                      <option value="">Country</option>
                      <?php foreach ($countries as $co): ?><option value="<?php echo (int)$co['id']; ?>"><?php echo htmlspecialchars($co['name']); ?></option><?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_name" placeholder="Name" required></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_find_address" placeholder="Find Address"></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_building" placeholder="Building"></div>
                  <div class="row">
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="sender_street_no" placeholder="Street No."></div></div>
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="sender_street" placeholder="Street"></div></div>
                  </div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_suburb" placeholder="Suburb"></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_city" placeholder="City / Town" required></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_state" placeholder="State"></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_postcode" placeholder="Postcode"></div>
                  <div class="form-group"><input type="text" class="form-control" name="sender_contact_person" placeholder="Contact Person"></div>
                  <div class="row">
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="sender_mobile" placeholder="Mobile"></div></div>
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="sender_phone" placeholder="Phone"></div></div>
                  </div>
                  <div class="form-group"><input type="email" class="form-control" name="sender_email" placeholder="Email"></div>
                  <div class="form-group"><textarea class="form-control" name="sender_pickup_instruction" rows="2" placeholder="Pickup Instruction"></textarea></div>
                </div>
                <div class="card-footer text-right">
                  <button type="button" class="btn btn-default btn-sender-clear">Clear</button>
                  <button type="button" class="btn btn-primary btn-sender-save">Save</button>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title mb-0">Receiver</h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <select class="form-control" name="receiver_country_id" required>
                      <option value="">Country</option>
                      <?php foreach ($countries as $co): ?><option value="<?php echo (int)$co['id']; ?>"><?php echo htmlspecialchars($co['name']); ?></option><?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_name" placeholder="Name" required></div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_find_address" placeholder="Find Address"></div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_building" placeholder="Building"></div>
                  <div class="row">
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="receiver_street_no" placeholder="Street No."></div></div>
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="receiver_street" placeholder="Street"></div></div>
                  </div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_city" placeholder="City / Town" required></div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_state" placeholder="State"></div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_postcode" placeholder="Postcode"></div>
                  <div class="form-group"><input type="text" class="form-control" name="receiver_contact_person" placeholder="Contact Person"></div>
                  <div class="row">
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="receiver_mobile" placeholder="Mobile"></div></div>
                    <div class="col-6"><div class="form-group"><input type="text" class="form-control" name="receiver_phone" placeholder="Phone"></div></div>
                  </div>
                  <div class="form-group"><input type="email" class="form-control" name="receiver_email" placeholder="Email"></div>
                  <div class="form-group"><textarea class="form-control" name="receiver_delivery_instruction" rows="2" placeholder="Delivery Instruction"></textarea></div>
                  <div class="form-group">
                    <select class="form-control" name="receiver_additional_services_id">
                      <option value="">Additional Services</option>
                      <?php foreach ($additional_services as $s): ?><option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['service_name']); ?></option><?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" name="receiver_signature_required" value="1" id="receiver_signature_required">
                      <span class="form-check-label">Signature Required</span>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="button" class="btn btn-default btn-receiver-clear">Clear</button>
                  <button type="button" class="btn btn-primary btn-receiver-save">Save</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Package -->
          <div class="card card-primary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title mb-0">Package <span class="text-danger">*</span> New</h3>
              <button type="button" class="btn btn-sm btn-light" id="addPackageRow"><i class="fas fa-plus"></i></button>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-bordered package-table mb-0" id="packageTable">
                  <thead class="bg-light">
                    <tr>
                      <th>Units *</th>
                      <th>Weight(Kg) *</th>
                      <th>Dimensions (cm) per unit *</th>
                      <th>Cubic(m3) *</th>
                      <th>Package Type *</th>
                      <th>DG Type</th>
                      <th>Remarks</th>
                      <th width="40"></th>
                    </tr>
                  </thead>
                  <tbody id="packageRows">
                    <tr class="package-row">
                      <td><input type="number" class="form-control form-control-sm units" name="packages[0][units]" min="1" value="1" required></td>
                      <td><input type="number" class="form-control form-control-sm weight_kg" name="packages[0][weight_kg]" step="0.01" min="0" required></td>
                      <td>
                        <div class="d-flex gap-1">
                          <input type="number" class="form-control form-control-sm dim-l" placeholder="L" step="0.01" min="0" name="packages[0][length_cm]">
                          <input type="number" class="form-control form-control-sm dim-w" placeholder="W" step="0.01" min="0" name="packages[0][width_cm]">
                          <input type="number" class="form-control form-control-sm dim-h" placeholder="H" step="0.01" min="0" name="packages[0][height_cm]">
                        </div>
                      </td>
                      <td><input type="number" class="form-control form-control-sm cubic_m3" name="packages[0][cubic_m3]" step="0.001" min="0" required></td>
                      <td>
                        <select class="form-control form-control-sm" name="packages[0][package_type_id]" required>
                          <option value="">Select</option>
                          <?php foreach ($package_types as $pt): ?><option value="<?php echo (int)$pt['id']; ?>"><?php echo htmlspecialchars($pt['name']); ?></option><?php endforeach; ?>
                        </select>
                      </td>
                      <td>
                        <select class="form-control form-control-sm" name="packages[0][dg_type_id]">
                          <option value="">Select</option>
                          <?php foreach ($dg_types as $dt): ?><option value="<?php echo (int)$dt['id']; ?>"><?php echo htmlspecialchars($dt['name']); ?></option><?php endforeach; ?>
                        </select>
                      </td>
                      <td><input type="text" class="form-control form-control-sm" name="packages[0][remarks]"></td>
                      <td><button type="button" class="btn btn-sm btn-danger btn-remove-package" title="Remove"><i class="fas fa-trash"></i></button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-2 d-flex justify-content-between align-items-center border-top">
                <span><strong>Total</strong></span>
                <button type="button" class="btn btn-secondary btn-sm">Revenue</button>
              </div>
            </div>
          </div>

          <!-- Tracking Notification | Additional Information (one row, col-md-6 each) -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">Tracking Notification</h3>
                  <div>
                    <button type="button" class="btn btn-sm btn-light" id="addNotificationRow"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                  </div>
                </div>
                <div class="card-body p-0">
                  <table class="table table-sm mb-0" id="notificationTable">
                    <thead class="bg-light">
                      <tr>
                        <th>Communication Type</th>
                        <th>Email/Phone</th>
                        <th>Notification Type</th>
                        <th width="40"></th>
                      </tr>
                    </thead>
                    <tbody id="notificationRows">
                      <tr class="notification-row">
                        <td>
                          <select class="form-control form-control-sm" name="notifications[0][communication_type]" required>
                            <?php foreach ($communication_types as $k => $v): ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php endforeach; ?>
                          </select>
                        </td>
                        <td><input type="text" class="form-control form-control-sm" name="notifications[0][contact]" placeholder="Email or Phone"></td>
                        <td>
                          <select class="form-control form-control-sm" name="notifications[0][notification_type_id]">
                            <option value="">Select</option>
                            <?php foreach ($notification_types as $nt): ?><option value="<?php echo (int)$nt['id']; ?>"><?php echo htmlspecialchars($nt['name']); ?></option><?php endforeach; ?>
                          </select>
                        </td>
                        <td><button type="button" class="btn btn-sm btn-danger btn-remove-notification"><i class="fas fa-trash"></i></button></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="card-footer">
                  <button type="button" class="btn btn-secondary" id="attachFileBtn"><i class="fas fa-cloud-upload-alt"></i> Attach File</button>
                  <input type="file" name="attachments[]" id="attachmentInput" multiple class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                  <span id="attachmentNames" class="ml-2 small text-muted"></span>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card card-primary h-100">
                <div class="card-header">
                  <h3 class="card-title mb-0">Additional Information</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-6">
                      <label>Insurance Type</label>
                      <select class="form-control" name="insurance_type">
                        <?php foreach ($insurance_types as $it): ?><option value="<?php echo htmlspecialchars($it); ?>" <?php echo $it === 'Owners Risk' ? 'selected' : ''; ?>><?php echo htmlspecialchars($it); ?></option><?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-6">
                      <label>DG Signatory</label>
                      <select class="form-control" name="dg_signatory_id">
                        <option value="">Select</option>
                        <?php foreach ($dg_signatories as $ds): ?><option value="<?php echo (int)$ds['id']; ?>"><?php echo htmlspecialchars($ds['name']); ?></option><?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form actions -->
          <div class="mb-4 text-right">
            <button type="reset" class="btn btn-secondary">Clear</button>
            <button type="submit" class="btn btn-primary" name="action" value="save">Save</button>
            <button type="submit" class="btn btn-primary" name="action" value="save_print">Save & Print</button>
          </div>

        </div>
      </form>
    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Version 2.0.3</div>
    <strong>Copyright &copy; 2026 Navare Solutions.</strong> All rights reserved. <a href="#">Terms & Conditions</a> | <a href="#">Privacy Policy</a> | <a href="#">Contact Us</a> <i class="fas fa-phone"></i>
  </footer>
</div>

<script src="<?php echo URL; ?>/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo URL; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URL; ?>/plugins/select2/js/select2.full.min.js"></script>
<script src="<?php echo URL; ?>/dist/js/adminlte.min.js"></script>
<script>
(function() {
  var packageIndex = 1, notificationIndex = 1;

  // Customer: search only (type to search, click to select)
  var customersList = <?php echo json_encode(array_map(function($c) { return ['id' => (int)$c['id'], 'name' => $c['name'], 'code' => $c['code'] ?? '', 'label' => $c['name'] . (!empty($c['code']) ? ' (' . $c['code'] . ')' : '')]; }, $customers)); ?>;
  var customerSearchTimer;
  $('#customer_search').on('input focus', function() {
    var q = $(this).val().trim().toLowerCase();
    $('#customer_id').val('');
    if (q.length < 1) {
      $('#customerSearchResults').hide().empty();
      return;
    }
    var matches = customersList.filter(function(c) {
      return c.name.toLowerCase().indexOf(q) >= 0 || (c.code && c.code.toLowerCase().indexOf(q) >= 0);
    });
    var $res = $('#customerSearchResults').empty();
    if (matches.length === 0) {
      var safeQ = $('<div/>').text(q).html();
      $res.append('<div class="list-group-item text-muted">No customer found</div>');
      $res.append('<a href="#" class="list-group-item list-group-item-action customer-add-new" data-name="' + safeQ + '">Add as new customer: ' + safeQ + '</a>');
    } else {
      matches.slice(0, 15).forEach(function(c) {
        var safe = $('<div/>').text(c.label).html();
        var safeCode = (c.code || '').replace(/"/g, '&quot;');
        $res.append('<a href="#" class="list-group-item list-group-item-action customer-pick" data-id="' + c.id + '" data-code="' + safeCode + '">' + safe + '</a>');
      });
    }
    $res.show();
  });
  $(document).on('click', '.customer-pick', function(e) {
    e.preventDefault();
    var id = $(this).data('id'), label = $(this).text().trim(), code = $(this).data('code') || '';
    $('#customer_id').val(id);
    $('#customer_search').val(label);
    $('#customer_code').val(code);
    $('#customerSearchResults').hide().empty();
    loadCustomerAddresses(id);
  });
  $(document).on('click', '.customer-add-new', function(e) {
    e.preventDefault();
    var name = $(this).data('name') || $('#customer_search').val().trim();
    $('#customer_id').val('');
    $('#customer_search').val(name);
    $('#customer_code').val(''); // new customer – code user type karega
    $('#customerSearchResults').hide().empty();
    $('[name="sender_name"]').val(name);
    $('[name="receiver_name"]').val(name);
  });
  function fillSender(addr) {
    if (!addr) return;
    $('[name="sender_country_id"]').val(addr.country_id || '');
    $('[name="sender_name"]').val(addr.name || '');
    $('[name="sender_find_address"]').val(addr.find_address || '');
    $('[name="sender_building"]').val(addr.building || '');
    $('[name="sender_street_no"]').val(addr.street_no || '');
    $('[name="sender_street"]').val(addr.street || '');
    $('[name="sender_suburb"]').val(addr.suburb || '');
    $('[name="sender_city"]').val(addr.city || '');
    $('[name="sender_state"]').val(addr.state || '');
    $('[name="sender_postcode"]').val(addr.postcode || '');
    $('[name="sender_contact_person"]').val(addr.contact_person || '');
    $('[name="sender_mobile"]').val(addr.mobile || '');
    $('[name="sender_phone"]').val(addr.phone || '');
    $('[name="sender_email"]').val(addr.email || '');
    $('[name="sender_pickup_instruction"]').val(addr.pickup_instruction || '');
  }
  function fillReceiver(addr) {
    if (!addr) return;
    $('[name="receiver_country_id"]').val(addr.country_id || '');
    $('[name="receiver_name"]').val(addr.name || '');
    $('[name="receiver_find_address"]').val(addr.find_address || '');
    $('[name="receiver_building"]').val(addr.building || '');
    $('[name="receiver_street_no"]').val(addr.street_no || '');
    $('[name="receiver_street"]').val(addr.street || '');
    $('[name="receiver_suburb"]').val(addr.suburb || '');
    $('[name="receiver_city"]').val(addr.city || '');
    $('[name="receiver_state"]').val(addr.state || '');
    $('[name="receiver_postcode"]').val(addr.postcode || '');
    $('[name="receiver_contact_person"]').val(addr.contact_person || '');
    $('[name="receiver_mobile"]').val(addr.mobile || '');
    $('[name="receiver_phone"]').val(addr.phone || '');
    $('[name="receiver_email"]').val(addr.email || '');
    $('[name="receiver_delivery_instruction"]').val(addr.delivery_instruction || '');
    $('#receiver_signature_required').prop('checked', !!addr.signature_required);
  }
  function loadCustomerAddresses(customerId) {
    $.get('<?php echo URL; ?>/get_customer_addresses.php', { customer_id: customerId }, function(data) {
      if (data.sender) fillSender(data.sender);
      if (data.receiver) fillReceiver(data.receiver);
      if (data.last_booking) {
        if (data.last_booking.company_id) {
          $('#company_id').val(data.last_booking.company_id);
          $('#company_search').val(data.last_booking.company_name || '');
        }
        $('[name="customer_reference"]').val(data.last_booking.customer_reference || '');
        $('[name="receiver_reference"]').val(data.last_booking.receiver_reference || '');
      }
    }, 'json').fail(function() {});
  }
  $('#customer_search').on('blur', function() {
    customerSearchTimer = setTimeout(function() { $('#customerSearchResults').hide(); }, 200);
  });
  $('#customerSearchResults').on('mousedown', '.customer-pick, .customer-add-new', function(e) { e.preventDefault(); });
  $('#jobBookingForm').on('submit', function() {
    if (!$('#customer_id').val()) {
      alert('Please search and select a customer.');
      $('#customer_search').focus();
      return false;
    }
    if (!$('#company_id').val()) {
      alert('Please search and select a company.');
      $('#company_search').focus();
      return false;
    }
  });

  // Company: search only (type to search, click to select)
  var companiesList = <?php echo json_encode(array_map(function($c) { return ['id' => (int)$c['id'], 'name' => $c['name']]; }, $companies)); ?>;
  var companySearchTimer;
  $('#company_search').on('input focus', function() {
    var q = $(this).val().trim().toLowerCase();
    $('#company_id').val('');
    if (q.length < 1) {
      $('#companySearchResults').hide().empty();
      return;
    }
    var matches = companiesList.filter(function(c) { return c.name.toLowerCase().indexOf(q) >= 0; });
    var $res = $('#companySearchResults').empty();
    if (matches.length === 0) {
      $res.append('<div class="list-group-item text-muted">No company found</div>');
    } else {
      matches.slice(0, 15).forEach(function(c) {
        var safe = $('<div/>').text(c.name).html();
        $res.append('<a href="#" class="list-group-item list-group-item-action company-pick" data-id="' + c.id + '">' + safe + '</a>');
      });
    }
    $res.show();
  });
  $(document).on('click', '.company-pick', function(e) {
    e.preventDefault();
    var id = $(this).data('id'), label = $(this).text();
    $('#company_id').val(id);
    $('#company_search').val(label);
    $('#companySearchResults').hide().empty();
  });
  $('#company_search').on('blur', function() {
    companySearchTimer = setTimeout(function() { $('#companySearchResults').hide(); }, 200);
  });
  $('#companySearchResults').on('mousedown', '.company-pick', function(e) { e.preventDefault(); });

  $('.clear-field').on('click', function() { $(this).closest('.input-group').find('input').val(''); });

  function clearSender() {
    $('[name^="sender_"]').filter('input, select, textarea').each(function() {
      if ($(this).attr('name') === 'sender_signature_required') $(this).prop('checked', false);
      else $(this).val('');
    });
  }
  function clearReceiver() {
    $('[name^="receiver_"]').filter('input, select, textarea').each(function() {
      if ($(this).attr('name') === 'receiver_signature_required') $(this).prop('checked', false);
      else $(this).val($(this).is('select') ? '' : '');
    });
  }
  $('.btn-sender-clear').on('click', clearSender);
  $('.btn-receiver-clear').on('click', clearReceiver);

  function saveAddress(role) {
    var customerId = $('#customer_id').val();
    var customerName = $('#customer_search').val().trim();
    var customerCode = $('#customer_code').val().trim();
    if (!customerId && !customerName) {
      alert('Please enter or select a customer name first.');
      $('#customer_search').focus();
      return;
    }
    var prefix = role === 'SENDER' ? 'sender_' : 'receiver_';
    var data = {
      role: role,
      customer_id: customerId || 0,
      customer_name: customerName,
      customer_code: customerCode
    };
    $('[name^="' + prefix + '"]').filter('input, select, textarea').each(function() {
      var n = $(this).attr('name');
      if (n) data[n] = $(this).is(':checkbox') ? ($(this).prop('checked') ? '1' : '') : $(this).val();
    });
    var $btn = role === 'SENDER' ? $('.btn-sender-save') : $('.btn-receiver-save');
    $btn.prop('disabled', true);
    $.post('<?php echo URL; ?>/save_customer_address.php', data)
      .done(function(res) {
        if (res.success) {
          $('#customer_id').val(res.customer_id);
          $('#customer_search').val(res.customer_label);
          if (res.customer_code !== undefined) $('#customer_code').val(res.customer_code);
          loadCustomerAddresses(res.customer_id);
          alert(role === 'SENDER' ? 'Sender address saved. Customer set.' : 'Receiver address saved. Customer set.');
        } else {
          alert(res.message || 'Save failed.');
        }
      })
      .fail(function() { alert('Request failed.'); })
      .always(function() { $btn.prop('disabled', false); });
  }
  $('.btn-sender-save').on('click', function() { saveAddress('SENDER'); });
  $('.btn-receiver-save').on('click', function() { saveAddress('RECEIVER'); });

  // Package: add row
  function addPackageRow() {
    var row = '<tr class="package-row">' +
      '<td><input type="number" class="form-control form-control-sm units" name="packages[' + packageIndex + '][units]" min="1" value="1" required></td>' +
      '<td><input type="number" class="form-control form-control-sm weight_kg" name="packages[' + packageIndex + '][weight_kg]" step="0.01" min="0" required></td>' +
      '<td><div class="d-flex gap-1"><input type="number" class="form-control form-control-sm dim-l" placeholder="L" step="0.01" name="packages[' + packageIndex + '][length_cm]"><input type="number" class="form-control form-control-sm dim-w" placeholder="W" step="0.01" name="packages[' + packageIndex + '][width_cm]"><input type="number" class="form-control form-control-sm dim-h" placeholder="H" step="0.01" name="packages[' + packageIndex + '][height_cm]"></div></td>' +
      '<td><input type="number" class="form-control form-control-sm cubic_m3" name="packages[' + packageIndex + '][cubic_m3]" step="0.001" min="0" required></td>' +
      '<td><select class="form-control form-control-sm" name="packages[' + packageIndex + '][package_type_id]" required><option value="">Select</option><?php foreach ($package_types as $pt): ?><option value="<?php echo (int)$pt['id']; ?>"><?php echo htmlspecialchars(addslashes($pt['name'])); ?></option><?php endforeach; ?></select></td>' +
      '<td><select class="form-control form-control-sm" name="packages[' + packageIndex + '][dg_type_id]"><option value="">Select</option><?php foreach ($dg_types as $dt): ?><option value="<?php echo (int)$dt['id']; ?>"><?php echo htmlspecialchars(addslashes($dt['name'])); ?></option><?php endforeach; ?></select></td>' +
      '<td><input type="text" class="form-control form-control-sm" name="packages[' + packageIndex + '][remarks]"></td>' +
      '<td><button type="button" class="btn btn-sm btn-danger btn-remove-package"><i class="fas fa-trash"></i></button></td></tr>';
    $('#packageRows').append(row);
    packageIndex++;
  }
  $('#addPackageRow').on('click', addPackageRow);
  $(document).on('click', '.btn-remove-package', function() {
    if ($('#packageRows tr').length > 1) $(this).closest('tr').remove();
  });

  // Dimensions -> cubic (L*W*H/1000000)
  $(document).on('input', '.dim-l, .dim-w, .dim-h', function() {
    var tr = $(this).closest('tr');
    var l = parseFloat(tr.find('.dim-l').val()) || 0, w = parseFloat(tr.find('.dim-w').val()) || 0, h = parseFloat(tr.find('.dim-h').val()) || 0;
    if (l && w && h) tr.find('.cubic_m3').val((l * w * h / 1000000).toFixed(3));
  });

  // Notification: add row
  var commOpts = '<?php foreach ($communication_types as $k => $v): ?><option value="<?php echo $k; ?>"><?php echo addslashes($v); ?></option><?php endforeach; ?>';
  var notifOpts = '<option value="">Select</option><?php foreach ($notification_types as $nt): ?><option value="<?php echo (int)$nt['id']; ?>"><?php echo htmlspecialchars(addslashes($nt['name'])); ?></option><?php endforeach; ?>';
  function addNotificationRow() {
    var row = '<tr class="notification-row"><td><select class="form-control form-control-sm" name="notifications[' + notificationIndex + '][communication_type]">' + commOpts + '</select></td><td><input type="text" class="form-control form-control-sm" name="notifications[' + notificationIndex + '][contact]"></td><td><select class="form-control form-control-sm" name="notifications[' + notificationIndex + '][notification_type_id]">' + notifOpts + '</select></td><td><button type="button" class="btn btn-sm btn-danger btn-remove-notification"><i class="fas fa-trash"></i></button></td></tr>';
    $('#notificationRows').append(row);
    notificationIndex++;
  }
  $('#addNotificationRow').on('click', addNotificationRow);
  $(document).on('click', '.btn-remove-notification', function() {
    if ($('#notificationRows tr').length > 1) $(this).closest('tr').remove();
  });

  // Attach file
  function toggleFreightPlaceholder() {
    var inp = document.getElementById('freight_ready_by');
    var ph = document.getElementById('freightPlaceholder');
    if (!inp || !ph) return;
    if (inp.value) ph.classList.add('hide'); else ph.classList.remove('hide');
  }
  $('#freight_ready_by').on('input change focus', function() { toggleFreightPlaceholder(); });
  $('#freight_ready_by').on('blur', function() { toggleFreightPlaceholder(); });
  $(document).on('click', '.freight-ready-by-wrap .clear-field', function() {
    $('#freight_ready_by').val('');
    toggleFreightPlaceholder();
  });
  $('#freightCalendarBtn').on('click', function() { document.getElementById('freight_ready_by').focus(); document.getElementById('freight_ready_by').showPicker && document.getElementById('freight_ready_by').showPicker(); });
  $('#attachFileBtn').on('click', function() { $('#attachmentInput').click(); });
  $('#attachmentInput').on('change', function() {
    var names = [];
    $(this).get(0).files && $.each($(this).get(0).files, function(i, f) { names.push(f.name); });
    $('#attachmentNames').text(names.length ? names.join(', ') : '');
  });

})();
</script>
</body>
</html>
