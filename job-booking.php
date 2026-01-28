<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     // User is not logged in
//     header("Location: login.php");
//     exit;
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ralo Logistics</title>

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
<?php 
  include('top-navbar.php');
?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
<?php
  include('left-navbar.php');

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>General Form</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">General Form</li>
            </ol>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          
          <!-- right column -->
          <div class="col-md-12">
            <!-- <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Different Width</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-3">
                    <label>What is your name?</label>
                    <input type="text" class="form-control" placeholder=".col-3">
                  </div>
                  <div class="col-4">
                    <input type="text" class="form-control" placeholder=".col-4">
                  </div>
                  <div class="col-5">
                    <input type="text" class="form-control" placeholder=".col-5">
                  </div>
                </div>
              </div>
            </div> -->

            <!-- general form elements disabled -->
            
            <!-- /.card -->
            
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->











    <!-- Content Wrapper. Contains page content -->
<div class="-content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Import Job Booking</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Job Booking</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="job-booking-store.php" method="POST" enctype="multipart/form-data">
            <!-- If using Laravel, add @csrf here -->

            <div class="container-fluid">
                <div class="row">

                    <!-- Left column -->
                    <div class="col-lg-12">

                        <!-- Customer Details Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Customer Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <!-- Customer Name / Code -->
                                    <div class="col-12 col-md-3">
          <label class="form-label req">Customer Name / Customer Code</label>
          <input type="text" class="form-control" name="customer_name_code" placeholder="Search customer...">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label">Company</label>
          <select class="form-select form-control" name="company">
            <option value="">Select company...</option>
            <option>Company A</option>
            <option>Company B</option>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Customer Reference / Order Number</label>
          <input type="text" class="form-control" name="customer_reference" placeholder="e.g. ORD-1021">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label">Receiver Reference</label>
          <input type="text" class="form-control" name="receiver_reference" placeholder="e.g. REF-009">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label">Freight Ready By</label>
          <input type="datetime-local" class="form-control" name="freight_ready_by">
        </div>

                                </div>

                               
                            </div>
                        </div>
                    </div>
                    
                </div><!-- /.row -->

                 <!-- SENDER + RECEIVER -->
  <div class="row g-3">
    <!-- Sender -->
    <div class="col-12 col-lg-6">
      <div class="panel">
        <div class="panel-body">
            <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Sender</h3>
                            </div>
                            <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label req">Country</label>
              <select class="form-select form-control" name="sender_country">
                <option value="">Select</option>
                <option>NZ</option><option>AU</option><option>PK</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label req">Name</label>
              <input type="text" class="form-control" name="sender_name" placeholder="Sender name">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Find Address</label>
              <input type="text" class="form-control" name="sender_find_address" placeholder="Search address...">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Building</label>
              <input type="text" class="form-control" name="sender_building">
            </div>

            <div class="col-6 col-md-4">
              <label class="form-label">Street No.</label>
              <input type="text" class="form-control" name="sender_street_no">
            </div>

            <div class="col-6 col-md-4">
              <label class="form-label">Street</label>
              <input type="text" class="form-control" name="sender_street">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Suburb</label>
              <input type="text" class="form-control" name="sender_suburb">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">City / Town</label>
              <input type="text" class="form-control" name="sender_city_town">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">State</label>
              <input type="text" class="form-control" name="sender_state">
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Postcode</label>
              <input type="text" class="form-control" name="sender_postcode">
            </div>

            <div class="col-12 col-md-5">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" name="sender_contact_person">
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label">Mobile</label>
              <input type="text" class="form-control" name="sender_mobile">
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="sender_phone">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="sender_email">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Pickup Instruction</label>
              <input type="text" class="form-control" name="pickup_instruction" placeholder="e.g. Call before pickup">
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end pt-2">
              <button class="btn btn-outline-secondary small-btn" type="reset">Clear</button>
              <button class="btn btn-secondary small-btn" type="button">Save</button>
            </div>
          </div>

</div></div>

        </div>
      </div>
    </div>

    <!-- Receiver -->
    <div class="col-12 col-lg-6">
      <div class="panel">
        
        <div class="panel-body">
            <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Receiver</h3>
                            </div>
                            <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label req">Country</label>
              <select class="form-select form-control" name="receiver_country">
                <option value="">Select</option>
                <option>NZ</option>
                <option>AU</option>
                <option>PK</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label req">Name</label>
              <input type="text" class="form-control" name="receiver_name" placeholder="Receiver name">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Find Address</label>
              <input type="text" class="form-control" name="receiver_find_address" placeholder="Search address...">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Building</label>
              <input type="text" name="receiver_building" class="form-control">
            </div>

            <div class="col-6 col-md-4">
              <label class="form-label">Street No.</label>
              <input type="text" name="receiver_street_no" class="form-control">
            </div>

            <div class="col-6 col-md-4">
              <label class="form-label">Street</label>
              <input type="text" name="receiver_street" class="form-control">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Suburb</label>
              <input type="text" name="receiver_suburb" class="form-control">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">City / Town</label>
              <input type="text" name="receiver_city_town" class="form-control">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">State</label>
              <input type="text" name="receiver_state" class="form-control">
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Postcode</label>
              <input type="text" name="receiver_postcode" class="form-control">
            </div>

            <div class="col-12 col-md-5">
              <label class="form-label">Contact Person</label>
              <input type="text" name="receiver_contact_person" class="form-control">
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label">Mobile</label>
              <input type="text" name="receiver_mobile" class="form-control">
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label">Phone</label>
              <input type="text" name="receiver_phone" class="form-control">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="receiver_email" class="form-control">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Delivery Instruction</label>
              <input type="text" class="form-control" name="delivery_instruction" placeholder="e.g. Leave at gate">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Additional Services</label>
              <select class="form-select form-control" name="additional_services[]" multiple>
                <option>Tail Lift</option>
                <option>Inside Delivery</option>
                <option>Fragile Handling</option>
              </select>
              <div class="form-text">Hold Ctrl/Command to select multiple.</div>
            </div>

            <div class="col-12 col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sigReq" name="signature_required">
                <label class="form-check-label" for="sigReq">Signature Required</label>
              </div>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end pt-2">
              <button class="btn btn-outline-secondary small-btn" type="button">Clear</button>
              <button class="btn btn-secondary small-btn" type="button">Save</button>
            </div>

          </div>

</div></div>

        </div>
      </div>
    </div>
  </div>


  <!-- PACKAGES GRID -->
  <div class="panel mt-3">
    <div class="table-responsive">
      <table class="table table-bordered mb-0 align-middle">
        <thead>
          <tr>
            <th style="min-width:180px">Package <span class="ms-2 text-white-50"><em>New</em></span></th>
            <th style="min-width:90px">Units *</th>
            <th style="min-width:120px">Weight(kg) *</th>
            <th colspan="3" style="min-width:360px">Dimensions (cm) per unit *</th>
            <th style="min-width:130px">Cubic(m3) *</th>
            <th style="min-width:160px">Package Type *</th>
            <th style="min-width:130px">DG Type</th>
            <th style="min-width:220px">Remarks</th>
            <th style="width:50px">🗑</th>
            <th style="width:50px">＋</th>
          </tr>
          <tr class="bg-white">
            <th class="bg-white text-dark"> </th>
            <th class="bg-white text-dark"> </th>
            <th class="bg-white text-dark"> </th>
            <th class="bg-white text-dark">Length</th>
            <th class="bg-white text-dark">Width</th>
            <th class="bg-white text-dark">Height</th>
            <th class="bg-white text-dark"></th>
            <th class="bg-white text-dark"></th>
            <th class="bg-white text-dark"></th>
            <th class="bg-white text-dark"></th>
            <th class="bg-white text-dark"></th>
            <th class="bg-white text-dark"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input class="form-control" name="packages[0][package_name]" placeholder="e.g. Carton"></td>
            <td><input class="form-control" type="number" name="packages[0][units]" min="1" value="1"></td>
            <td><input class="form-control" type="number" name="packages[0][weight_kg]" step="0.001" placeholder="0.000"></td>
            <td><input class="form-control" type="number" name="packages[0][length_cm]" step="0.01" placeholder="0.00"></td>
            <td><input class="form-control" type="number" name="packages[0][width_cm]"  step="0.01" placeholder="0.00"></td>
            <td><input class="form-control" type="number" name="packages[0][height_cm]" step="0.01" placeholder="0.00"></td>
            <td><input class="form-control" type="number" name="packages[0][cubic_m3]" step="0.01"  placeholder="0.00"></td>
            <td><input class="form-control" type="number" name="packages[0][package_type]" step="0.0001" placeholder="0.0000"></td>
            <td>
              <select class="form-select form-control" name="packages[0][dg_type]">
                <option value="">Select...</option>
                <option>Pallet</option>
                <option>Carton</option>
                <option>Crate</option>
              </select>
            </td>
            <td>
              <select class="form-select form-control" name="packages[0][remarks]" >
                <option value="">None</option>
                <option>DG 3</option>
                <option>DG 8</option>
              </select>
            </td>
            <td><input class="form-control" placeholder="Remarks"></td>
            <td class="text-center"><button class="btn btn-outline-danger small-btn" type="button">🗑</button></td>
            <td class="text-center"><button class="btn btn-outline-primary small-btn" type="button">＋</button></td>
          </tr>

          <tr>
            <td colspan="12" class="fw-semibold">Total</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- BOTTOM ACCORDIONS -->
  <div class="row g-3 mt-1">
    <!-- TRACKING NOTIFICATION -->
    <div class="col-12 col-lg-6">
    <div class="panel mt-3">
      <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title mb-0">Tracking Notification</h3>
        </div>
        <div class="card-body">
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Communication Type</label>
            <select class="form-select form-control" name="tracking_communication_type" id="communicationType">
              <option value="">Select</option>
              <option value="Email">Email</option>
              <option value="Phone">Phone</option>
            </select>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Email/Phone</label>
            <input type="text" class="form-control" name="tracking_contact_value" id="communicationDetail" placeholder="e.g. Email or Phone">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Notification Type</label>
            <select class="form-select form-control" name="tracking_notification_type">
              <option value="">Select</option>
              <option>SMS</option>
              <option>Email</option>
              <option>Push</option>
            </select>
          </div>
        </div>
      </div>
</div></div>

    </div>
</div>
<div class="col-12 col-lg-6">
    <div class="panel mt-3">
        <div class="card card-primary">
    <div class="card-header">
            <h3 class="card-title mb-0">Additional Information</h3>
        </div>
        <div class="card-body">
    <div class="panel-body">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label">Insurance Type</label>
          <select class="form-select form-control" name="insurance_type">
            <option value="">Select</option>
            <option>Owners Risk</option>
            <option>Carrier's Risk</option>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">DG Signatory</label>
          <select class="form-select form-control" name="dg_signatory">
            <option value="">Select</option>
            <option>Yes</option>
            <option>No</option>
          </select>
        </div>
      </div>
  </div>
</div>
</div>
</div>
</div>



   
  </div>

  <!-- Attach file -->
  <div class="mt-3">
    <label class="form-label">Attach File</label>
    <input class="form-control" type="file" name="attachment">
  </div>

</div>

<!-- Sticky footer buttons like screenshot -->
<div class="footer-actions d-flex justify-content-end gap-2">
  <button class="btn btn-outline-secondary" type="button">Clear</button>
  <button class="btn btn-secondary" type="submit">Save</button>
  <button class="btn btn-primary" type="submit">Save &amp; Print</button>
</div>


            </div><!-- /.container-fluid -->
        </form>
    </section>
</div>












  </div>
  <!-- /.content-wrapper -->
   <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2026.</strong> All rights reserved.
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
<!-- bs-custom-file-input -->
<script src="/ralo/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/ralo/dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
$(function () {
  bsCustomFileInput.init();
});
</script>
</body>
</html>
