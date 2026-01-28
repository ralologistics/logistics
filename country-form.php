<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$edit = false;
$country = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM countries WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $country = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

$continents = ['Africa', 'Antarctica', 'Asia', 'Europe', 'North America', 'Oceania', 'South America'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Country</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Country</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="country-list.php">Countries</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="country-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($country['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Country Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Country Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="name"
                                                        name="name"
                                                        placeholder="e.g., Pakistan, Australia"
                                                        value="<?php echo $edit ? htmlspecialchars($country['name']) : ''; ?>"
                                                        required
                                                        maxlength="255">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="continent">Continent</label>
                                                    <select class="form-control" id="continent" name="continent">
                                                        <option value="">Select Continent</option>
                                                        <?php foreach ($continents as $cont): 
                                                            $selected = $edit && $country['continent'] == $cont ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $cont; ?>" <?php echo $selected; ?>>
                                                                <?php echo $cont; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="iso_alpha2">ISO Alpha-2 Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="iso_alpha2"
                                                        name="iso_alpha2"
                                                        placeholder="e.g., PK, AU"
                                                        value="<?php echo $edit ? htmlspecialchars($country['iso_alpha2'] ?? '') : ''; ?>"
                                                        maxlength="2"
                                                        style="text-transform: uppercase;">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="iso_alpha3">ISO Alpha-3 Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="iso_alpha3"
                                                        name="iso_alpha3"
                                                        placeholder="e.g., PAK, AUS"
                                                        value="<?php echo $edit ? htmlspecialchars($country['iso_alpha3'] ?? '') : ''; ?>"
                                                        maxlength="3"
                                                        style="text-transform: uppercase;">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="numeric_code">Numeric Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="numeric_code"
                                                        name="numeric_code"
                                                        placeholder="e.g., 586"
                                                        value="<?php echo $edit ? htmlspecialchars($country['numeric_code'] ?? '') : ''; ?>"
                                                        maxlength="3">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="phone_code">Phone Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="phone_code"
                                                        name="phone_code"
                                                        placeholder="e.g., +92"
                                                        value="<?php echo $edit ? htmlspecialchars($country['phone_code'] ?? '') : ''; ?>"
                                                        maxlength="10">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="currency">Currency</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="currency"
                                                        name="currency"
                                                        placeholder="e.g., PKR, USD"
                                                        value="<?php echo $edit ? htmlspecialchars($country['currency'] ?? '') : ''; ?>"
                                                        maxlength="10">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            id="is_active"
                                                            name="is_active"
                                                            value="1"
                                                            <?php echo $edit && $country['is_active'] ? 'checked' : 'checked'; ?>>
                                                        <label class="form-check-label" for="is_active">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="country-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Country
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <!-- AdminLTE App -->
    <script src="/ralo/dist/js/adminlte.min.js"></script>
    <script>
        // Auto-uppercase ISO codes
        document.getElementById('iso_alpha2')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        document.getElementById('iso_alpha3')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
</body>

</html>
