<?php
include('functions.php');
?>
<style type="text/css">
  a.nav-link {
    font-size: 0.8rem;
  }
</style>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo URL; ?>/index.php" class="brand-link">
    <img src="<?php echo URL; ?>/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">Ralo Logistics</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo URL; ?>/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"> <?php echo $_SESSION['user_name']; ?> </a>
      </div>
    </div>



    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
          <a href="<?php echo URL; ?>/index.php" class="nav-link active">
            <i class="far fa-circle nav-icon"></i>
            <p>Dashboard</p>
          </a>
        </li>




        <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>
                Forms
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview"> -->
        <li class="nav-header">Dashboard</li>
        <li class="nav-header">Master Data Management</li>
        <li class="nav-header">Courier/Transport</li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/country-list.php" class="nav-link">
              <img
                src="<?php echo URL; ?>/dist/img/icons8-country-64.png"
                alt="Countries"
                style="width:22px; height:22px; margin-right:8px;">
            <p>Countries</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/company-list.php" class="nav-link">
           <img
                src="<?php echo URL; ?>/dist/img/icons8-company-80.png"
                alt="Countries"
                style="width:22px; height:22px; margin-right:8px;">
            <p>Companies</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/ship-type-list.php" class="nav-link">
            <img
                src="<?php echo URL; ?>/dist/img/icons8-shipment-logistic-50.png"
                alt="Countries"
                style="width:22px; height:22px; margin-right:8px;">
            <p>Ship Types</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/door-type-list.php" class="nav-link">
            <img
                src="<?php echo URL; ?>/dist/img/icons8-door-64.png"
                alt="Countries"
                style="width:22px; height:22px; margin-right:8px;">
            <p>Door Types</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/location-type-list.php" class="nav-link">
            <img
                src="<?php echo URL; ?>/dist/img/icons8-location-50.png"
                alt="Countries"
                style="width:22px; height:22px; margin-right:8px;">
            <p>Location Types</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/lift-type-list.php" class="nav-link">
            <i class="nav-icon fas fa-truck"></i>
            <p>Lift Types</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-booking-form.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Job Booking</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-booking-list.php" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>Job Bookings List</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Consignment Review</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Order to be sent</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo URL; ?>" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Manifests</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Delete Job</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Deleted Jobs List</p>
          </a>
        </li>

        <li class="nav-header">Customer Management</li>
        <li class="nav-header">Freight Network</li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/import-job-form.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Pickup/Delivery Allocation</p>
          </a>
        </li>


        <li class="nav-header">Containers</li>

        <li class="nav-item">
          <a href="<?php echo URL; ?>/import-job-form.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Import Job Booking</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/export-job-list.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Export Job Booking</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/cart-job-list.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Cart Job Booking</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/swing-job-list.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Swing Job Booking</p>
          </a>
        </li>
        <!-- <li class="nav-item">
                <a href="pages/forms/advanced.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Advanced Elements</p>
                </a>
              </li> -->

        <!-- </ul> -->
        <!-- </li> -->
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-queue.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Job Queue
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-queue.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Shipping Line
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/vessel-list.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Vessel
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-queue.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Locations
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo URL; ?>/user-registeration-form.php" class="nav-link">
            <i class="nav-icon far fa-calendar-alt"></i>
            <p>
              User Registeration form
              <!-- <span class="badge badge-info right">2</span> -->
            </p>
          </a>
        </li>



        <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Pages
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages/examples/invoice.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Invoice</p>
                </a>
              </li>
            </ul>
          </li> -->
        <li class="nav-item">
          <a href="<?php echo URL; ?>/logout.php" class="nav-link">
            <i class="nav-icon far fa-plus-square"></i>
            <p>
              Logout
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>

        <li class="nav-header">Invoice</li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/job-queue.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Invoice Dashboard
              <!-- <i class="fas fa-angle-left right"></i> -->
            </p>
          </a>
        </li>

        <li class="nav-header">Settings</li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/footer-settings-list.php" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>
              Footer Settings
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/additional-services-list.php" class="nav-link">
            <i class="nav-icon fas fa-plus-circle"></i>
            <p>
              Additional Services
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo URL; ?>/endorsements-list.php" class="nav-link">
            <i class="nav-icon fas fa-id-badge"></i>
            <p>
              Endorsements
            </p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>