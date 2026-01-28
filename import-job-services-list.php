<?php
session_start();

require 'db.php';

$result = $conn->query("SELECT ij.id, ij.job_type, s.name as service_name, c.container_no, ij.created_at
                        FROM import_job_services ij
                        JOIN services s ON ij.service_id = s.id
                        JOIN containers c ON ij.container_id = c.id
                        ORDER BY ij.created_at DESC");
$import_job_services = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Import Job Services</title>

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
                            <h1>Import Job Services</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Import Job Services</li>
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
                                    <h3 class="card-title">List of Import Job Services</h3>
                                    <div class="card-tools">
                                        <a href="import-job-services-form.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add New
                                        </a>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="importJobServicesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Job Type</th>
                                                <th>Service Name</th>
                                                <th>Container No</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($import_job_services as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['job_type']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['container_no']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                                                    <td>
                                                        <a href="import-job-services-form.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="import-job-services-delete.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this import job service?')">
                                                            <i class="fas fa-trash"></i> Delete
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
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

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
    <!-- DataTables -->
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
        $(function() {
            $("#importJobServicesTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [
                    [3, "desc"]
                ]
            });
        });
    </script>
    <?php include('footer.php'); ?>
</body>

</html>