<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

// Get search and sort parameters
$search = $_GET['search'] ?? '';
$search_by = $_GET['search_by'] ?? 'all';
$sort_by = $_GET['sort_by'] ?? 'name';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Build query
$where = "1=1";
if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    
    if ($search_by == 'name') {
        $where .= " AND l.name LIKE '%$search_escaped%'";
    } elseif ($search_by == 'company') {
        $where .= " AND c.name LIKE '%$search_escaped%'";
    } elseif ($search_by == 'location_code') {
        $where .= " AND l.location_code LIKE '%$search_escaped%'";
    } elseif ($search_by == 'city') {
        $where .= " AND l.city LIKE '%$search_escaped%'";
    } else {
        // Search all fields
        $where .= " AND (l.name LIKE '%$search_escaped%' OR l.location_code LIKE '%$search_escaped%' OR l.city LIKE '%$search_escaped%' OR c.name LIKE '%$search_escaped%')";
    }
}

// Validate sort column
if ($sort_by === 'name') {
    $sort_column = 'l.name';
} elseif ($sort_by === 'location_type') {
    $sort_column = 'lt.name';
} elseif ($sort_by === 'door_type') {
    $sort_column = 'dt.name';
} elseif ($sort_by === 'created_at') {
    $sort_column = 'l.created_at';
} else {
    $sort_column = 'l.name';
}

$sort_order = strtoupper($sort_order) == 'DESC' ? 'DESC' : 'ASC';

$query = "SELECT l.*, c.name as company_name, lt.name as location_type_name, dt.name as door_type_name, lift.name as lift_type_name, co.name as country_name 
          FROM locations l 
          LEFT JOIN companies c ON l.company_id = c.id 
          LEFT JOIN location_types lt ON l.location_type_id = lt.id 
          LEFT JOIN door_types dt ON l.door_type_id = dt.id 
          LEFT JOIN lift_types lift ON l.lift_type_id = lift.id 
          LEFT JOIN countries co ON l.country_id = co.id 
          WHERE $where 
          ORDER BY $sort_column $sort_order";

$result = $conn->query($query);
$locations = $result->fetch_all(MYSQLI_ASSOC);
$total_count = count($locations);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Locations</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="/ralo/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/ralo/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
            <h1>Locations</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Locations</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Locations</h3>
                <div class="card-tools">
                  <form method="GET" class="form-inline">
                    <div class="form-group mr-2">
                      <label for="search_by" class="mr-2">Search By:</label>
                      <select name="search_by" id="search_by" class="form-control form-control-sm">
                        <option value="all" <?php echo $search_by == 'all' ? 'selected' : ''; ?>>All Fields</option>
                        <option value="name" <?php echo $search_by == 'name' ? 'selected' : ''; ?>>Name</option>
                        <option value="company" <?php echo $search_by == 'company' ? 'selected' : ''; ?>>Company</option>
                        <option value="location_code" <?php echo $search_by == 'location_code' ? 'selected' : ''; ?>>Location Code</option>
                        <option value="city" <?php echo $search_by == 'city' ? 'selected' : ''; ?>>City</option>
                      </select>
                    </div>
                    <div class="input-group input-group-sm mr-2">
                      <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                          <i class="fas fa-search"></i>
                        </button>
                      </div>
                    </div>
                    <div class="form-group mr-2">
                      <label for="sort_by" class="mr-2">Sort By:</label>
                      <select name="sort_by" id="sort_by" class="form-control form-control-sm">
                        <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name</option>
                        <option value="location_type" <?php echo $sort_by == 'location_type' ? 'selected' : ''; ?>>Location Type</option>
                        <option value="door_type" <?php echo $sort_by == 'door_type' ? 'selected' : ''; ?>>Door Type</option>
                        <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>Created Date</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <select name="sort_order" class="form-control form-control-sm">
                        <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                        <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                      </select>
                    </div>
                    <a href="location-list.php" class="btn btn-secondary btn-sm ml-2">
                      <i class="fas fa-sync"></i> Reset
                    </a>
                  </form>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="locationsTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location Code</th>
                    <th>Company</th>
                    <th>Location Type</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($locations as $location): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($location['id']); ?></td>
                      <td><?php echo htmlspecialchars($location['name']); ?></td>
                      <td><?php echo htmlspecialchars($location['location_code'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($location['company_name'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($location['location_type_name'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($location['city'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($location['country_name'] ?? ''); ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info view-btn" data-id="<?php echo $location['id']; ?>" title="View">
                          <i class="fas fa-eye"></i>
                        </button>
                        <a href="location-delete.php?id=<?php echo $location['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this location?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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

<!-- Create Location Modal -->
<div class="modal fade" id="createLocationModal" tabindex="-1" role="dialog" style="z-index: 9999;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <h5 class="modal-title">Create Location</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="createLocationModalContent" style="max-height: 70vh; overflow-y: auto;">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- View Location Modal -->
<div class="modal fade" id="viewLocationModal" tabindex="-1" role="dialog" style="z-index: 9999;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <h5 class="modal-title">View Location</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="viewLocationModalContent" style="max-height: 70vh; overflow-y: auto;">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- Edit Location Modal -->
<div class="modal fade" id="editLocationModal" tabindex="-1" role="dialog" style="z-index: 9999;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <h5 class="modal-title">Edit Location</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="editLocationModalContent" style="max-height: 70vh; overflow-y: auto;">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- Floating Create Button -->
<a href="#" onclick="createLocation()" class="btn btn-primary btn-lg" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.3); z-index: 1000;">
  <i class="fas fa-plus"></i>
</a>

<!-- jQuery -->
<script src="/ralo/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="/ralo/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/ralo/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/ralo/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/ralo/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/ralo/dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#locationsTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#locationsTable_wrapper .col-md-6:eq(0)');
  });

  $(document).on('click', '.view-btn', function() {
    var id = $(this).data('id');
    viewLocation(id);
  });

  function viewLocation(id) {
    $('#viewLocationModalContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#viewLocationModal').modal('show');
    
    $.ajax({
        url: 'location-view-ajax.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            $('#viewLocationModalContent').html(response);
        },
        error: function() {
            $('#viewLocationModalContent').html('<div class="alert alert-danger">Error loading location details.</div>');
        }
    });
  }

  function editLocation(id) {
    // Close view modal
    $('#viewLocationModal').modal('hide');
    
    // Open edit modal
    $('#editLocationModalContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#editLocationModal').modal('show');
    
    $.ajax({
        url: 'location-edit-ajax.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            $('#editLocationModalContent').html(response);
        },
        error: function() {
            $('#editLocationModalContent').html('<div class="alert alert-danger">Error loading location form.</div>');
        }
    });
  }

  function createLocation() {
    $.ajax({
      url: 'location-create-ajax.php',
      type: 'GET',
      success: function(data) {
        $('#createLocationModalContent').html(data);
        $('#createLocationModal').modal('show');
      },
      error: function() {
        alert('Error loading create form.');
      }
    });
  }
</script>
<?php include('footer.php'); ?>
</body>
</html>