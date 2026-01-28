<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

// Get search and sort parameters
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'name';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Build query
$where = "1=1";
if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    $where .= " AND (v.name LIKE '%$search_escaped%' OR v.mmsi LIKE '%$search_escaped%' OR v.call_sign LIKE '%$search_escaped%')";
}

// Validate sort column
$allowed_sort = ['name', 'mmsi', 'call_sign', 'created_at'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'name';
}
$sort_order = strtoupper($sort_order) == 'DESC' ? 'DESC' : 'ASC';

$query = "SELECT v.*, c.name as country_name, st.type_name as ship_type_name 
          FROM vessels v 
          LEFT JOIN countries c ON v.country_id = c.id 
          LEFT JOIN ship_types st ON v.ship_type_id = st.id 
          WHERE $where 
          ORDER BY v.$sort_by $sort_order";

$result = $conn->query($query);
$vessels = $result->fetch_all(MYSQLI_ASSOC);
$total_count = count($vessels);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Vessels</title>

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
    <section class="content-header" style="background-color: #3c8dbc; color: white; padding: 10px 15px;">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1 style="margin: 0; color: white;">Vessel</h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- Control Bar -->
            <div class="card" style="margin-bottom: 0;">
              <div class="card-body" style="padding: 15px;">
                <div class="row align-items-center">
                  <div class="col-md-3">
                    <span>Displaying <?php echo $total_count; ?> Vessel</span>
                  </div>
                  <div class="col-md-9">
                    <div class="float-right">
                      <div class="d-inline-block mr-3">
                        <label class="mb-0">Sort by</label>
                        <select class="form-control form-control-sm d-inline-block" style="width: auto; margin-left: 5px;" id="sortSelect" onchange="applySort()">
                          <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name</option>
                          <option value="mmsi" <?php echo $sort_by == 'mmsi' ? 'selected' : ''; ?>>MMSI</option>
                          <option value="call_sign" <?php echo $sort_by == 'call_sign' ? 'selected' : ''; ?>>Call Sign</option>
                          <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>Created Date</option>
                        </select>
                      </div>
                      <div class="d-inline-block">
                        <form method="GET" action="vessel-list.php" class="d-inline-flex align-items-center">
                          <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
                          <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">
                          <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search by Name, MMSI, Call Sign"
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                              </button>
                              <?php if (!empty($search)): ?>
                                <a href="vessel-list.php?sort_by=<?php echo htmlspecialchars($sort_by); ?>&sort_order=<?php echo htmlspecialchars($sort_order); ?>" class="btn btn-secondary">
                                  <i class="fas fa-times"></i>
                                </a>
                              <?php endif; ?>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Data Table -->
            <div class="card">
              <div class="card-body" style="padding: 0;">
                <table class="table table-hover" style="margin-bottom: 0;">
                  <thead style="background-color: #6c757d; color: white;">
                  <tr>
                    <th style="cursor: pointer;" onclick="sortTable('name')">
                      Name 
                      <?php if ($sort_by == 'name'): ?>
                        <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>"></i>
                      <?php endif; ?>
                    </th>
                    <th style="cursor: pointer;" onclick="sortTable('mmsi')">
                      MMSI 
                      <?php if ($sort_by == 'mmsi'): ?>
                        <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>"></i>
                      <?php endif; ?>
                    </th>
                    <th style="cursor: pointer;" onclick="sortTable('call_sign')">
                      Call Sign 
                      <?php if ($sort_by == 'call_sign'): ?>
                        <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>"></i>
                      <?php endif; ?>
                    </th>
                    <th style="cursor: pointer;" onclick="sortTable('created_at')">
                      Created Date
                      <?php if ($sort_by == 'created_at'): ?>
                        <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>"></i>
                      <?php endif; ?>
                    </th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php if (count($vessels) > 0): ?>
                    <?php foreach ($vessels as $vessel): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($vessel['name']); ?></td>
                      <td><?php echo htmlspecialchars($vessel['mmsi'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($vessel['call_sign'] ?? 'N/A'); ?></td>
                      <td><?php echo $vessel['created_at'] ? date('d-m-Y', strtotime($vessel['created_at'])) : 'N/A'; ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info" title="View" onclick="viewVessel(<?php echo $vessel['id']; ?>)">
                          <i class="fas fa-eye"></i>
                        </button>
                        <a href="vessel-delete.php?id=<?php echo $vessel['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this vessel?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">No vessels found</td>
                    </tr>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- View Vessel Modal -->
<div class="modal fade" id="viewVesselModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <h5 class="modal-title">View Vessel</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="vesselModalContent">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Vessel Modal -->
<div class="modal fade" id="editVesselModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <h5 class="modal-title">Edit Vessel</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="editVesselModalContent">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Add Button -->
<a href="vessel-form.php" class="btn btn-primary btn-lg" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.3); z-index: 1000;">
  <i class="fas fa-plus" style="font-size: 24px;"></i>
</a>

<!-- jQuery -->
<script src="/ralo/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/ralo/dist/js/adminlte.min.js"></script>
<script>
function sortTable(column) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort_by');
    const currentOrder = urlParams.get('sort_order') || 'ASC';
    
    let newOrder = 'ASC';
    if (currentSort === column && currentOrder === 'ASC') {
        newOrder = 'DESC';
    }
    
    urlParams.set('sort_by', column);
    urlParams.set('sort_order', newOrder);
    window.location.search = urlParams.toString();
}

function applySort() {
    const sortSelect = document.getElementById('sortSelect');
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort_by', sortSelect.value);
    window.location.search = urlParams.toString();
}

function viewVessel(id) {
    $('#vesselModalContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#viewVesselModal').modal('show');
    
    $.ajax({
        url: 'vessel-view-ajax.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            $('#vesselModalContent').html(response);
        },
        error: function() {
            $('#vesselModalContent').html('<div class="alert alert-danger">Error loading vessel details.</div>');
        }
    });
}

function editVessel(id) {
    // Close view modal
    $('#viewVesselModal').modal('hide');
    
    // Open edit modal
    $('#editVesselModalContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#editVesselModal').modal('show');
    
    $.ajax({
        url: 'vessel-edit-ajax.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            $('#editVesselModalContent').html(response);
        },
        error: function() {
            $('#editVesselModalContent').html('<div class="alert alert-danger">Error loading vessel form.</div>');
        }
    });
}
</script>
<?php include('footer.php'); ?>
</body>
</html>
