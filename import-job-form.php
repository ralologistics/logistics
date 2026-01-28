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
  <title>AdminLTE 3 | General Form Elements</title>

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
                        <li class="breadcrumb-item active">Import Job Booking</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="store.php" method="POST" enctype="multipart/form-data">
            <!-- If using Laravel, add @csrf here -->

            <div class="container-fluid">
                <div class="row">

                    <!-- Left column -->
                    <div class="col-lg-3">

                        <!-- Customer Details Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Customer Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <!-- Customer Name / Code -->
                                    <div class="form-group col-md-12">
                                        <label for="customer_name_code">
                                            Customer Name / Customer Code <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="customer_name_code" 
                                               name="customer_name_code" 
                                               placeholder="Enter customer name or code"
                                               required>
                                    </div>

                                    <!-- Document Received Date & Time -->
                                    <div class="form-group col-md-12">
                                        <label for="document_received_at">
                                            Document Received Date &amp; Time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" 
                                               class="form-control" 
                                               id="document_received_at" 
                                               name="document_received_at"
                                               required>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="form-group">
                                    <label for="document_upload_path">Document Upload</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input" 
                                               id="document_upload_path" 
                                               name="document_upload_path">
                                        <label class="custom-file-label" for="document_upload_path">
                                            Choose file
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Upload any related documents (PDF, Excel, etc.)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <!-- Container Details Card -->
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Container Details</h3>
                            </div>
                            <div class="card-body">

                                <!-- Top Row: Reference, Container, ISO, Weight -->
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="reference_no">
                                            Reference <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="reference_no" 
                                               name="reference_no" 
                                               placeholder="REF-001"
                                               required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="container_no">
                                            Container <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="container_no" 
                                               name="container_no" 
                                               placeholder="e.g. ABCD1234567"
                                               required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="iso_code">
                                            ISO Code <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="iso_code" 
                                               name="iso_code" 
                                               placeholder="e.g. 22G1"
                                               required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="weight_kg">
                                            Weight (kg) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               step="0.01"
                                               class="form-control" 
                                               id="weight_kg" 
                                               name="weight_kg" 
                                               placeholder="e.g. 28000"
                                               required>
                                    </div>
                                </div>

                                <!-- Locations Row -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="from_location">
                                            From <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="from_location" 
                                               name="from_location" 
                                               placeholder="Origin location"
                                               required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="to_location">
                                            To <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="to_location" 
                                               name="to_location" 
                                               placeholder="Destination location"
                                               required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="return_to_location">Return To</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="return_to_location" 
                                                   name="return_to_location" 
                                                   placeholder="Return location">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Container Information -->
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="customer_location_grid">Customer Location Grid</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="customer_location_grid" 
                                               name="customer_location_grid">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="door_type">Door</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="door_type" 
                                               name="door_type">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="security_check">Security Check</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="security_check" 
                                               name="security_check">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="random_number">Random Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="random_number" 
                                               name="random_number">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="release_ecn_number">Release / ECN Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="release_ecn_number" 
                                               name="release_ecn_number">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="port_pin_no">Port Pin No.</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="port_pin_no" 
                                               name="port_pin_no">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="detention_days">
                                            Detention Days <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="detention_days" 
                                               name="detention_days" 
                                               value="0"
                                               required>
                                    </div>
                                </div>

                                <!-- Dates Row -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="available_date">Available Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="available_date" 
                                               name="available_date">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="vb_slot_date">VB Slot Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="vb_slot_date" 
                                               name="vb_slot_date">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="demurrage_date">Demurrage Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="demurrage_date" 
                                               name="demurrage_date">
                                    </div>
                                </div>

                                <!-- Shipping / Vessel / Voyage -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="shipping">Shipping</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="shipping" 
                                               name="shipping">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="vessel">Vessel</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="vessel" 
                                               name="vessel">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="voyage">Voyage</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="voyage" 
                                               name="voyage">
                                    </div>
                                </div>

                                <!-- Checkboxes Row -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Options</label>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_xray" 
                                                   name="is_xray" 
                                                   value="1">
                                            <label class="form-check-label" for="is_xray">
                                                XRay
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_dgs" 
                                                   name="is_dgs" 
                                                   value="1">
                                            <label class="form-check-label" for="is_dgs">
                                                DGS
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_live_ul" 
                                                   name="is_live_ul" 
                                                   value="1">
                                            <label class="form-check-label" for="is_live_ul">
                                                Live UL
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Hold Section -->
                                    <div class="form-group col-md-4">
                                        <label>Hold</label>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="hold_sh" 
                                                   name="hold_sh" 
                                                   value="1">
                                            <label class="form-check-label" for="hold_sh">
                                                SH
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="hold_customs" 
                                                   name="hold_customs" 
                                                   value="1">
                                            <label class="form-check-label" for="hold_customs">
                                                Customs
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="hold_mpi" 
                                                   name="hold_mpi" 
                                                   value="1">
                                            <label class="form-check-label" for="hold_mpi">
                                                MPI
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Services / Notes -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="additional_services">Additional Services</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="additional_services" 
                                               name="additional_services" 
                                               placeholder="e.g. Storage, fumigation, etc.">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="add_notes">Additional Notes</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="add_notes" 
                                               name="add_notes" 
                                               placeholder="Any special instructions">
                                    </div>
                                </div>

                            </div>

                            <!-- Card Footer: Buttons -->
                            <div class="card-footer text-right">
                                <button type="reset" class="btn btn-default">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Save Import Job
                                </button>
                            </div>
                        </div><!-- /.card -->

                    </div><!-- /.col -->

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </form>
    </section>
</div>












  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
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
