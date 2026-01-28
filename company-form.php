<?php
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

require 'db.php';

$edit = false;
$company = null;
if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $company = $result->fetch_assoc();
    $stmt->close();
}

// Fetch countries
$countries_result = $conn->query("SELECT id, name FROM countries WHERE is_active = 1 ORDER BY name ASC");
$countries = $countries_result ? $countries_result->fetch_all(MYSQLI_ASSOC) : [];

$timezones = [
    'Pacific/Auckland' => 'Pacific/Auckland (New Zealand)',
    'America/New_York' => 'America/New_York (Eastern Time)',
    'Europe/London' => 'Europe/London (GMT)',
    'Asia/Karachi' => 'Asia/Karachi (Pakistan)',
    'Australia/Sydney' => 'Australia/Sydney (AEDT)',
];

$currencies = ['NZD', 'USD', 'EUR', 'GBP', 'PKR', 'AUD'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Company</title>

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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Company</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="company-list.php">Companies</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <form action="company-store.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Company Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_code">Company Code</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="company_code"
                                                        name="company_code"
                                                        placeholder="e.g., GBLOG, NAV001"
                                                        value="<?php echo $edit ? htmlspecialchars($company['company_code'] ?? '') : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Company Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="name"
                                                        name="name"
                                                        placeholder="e.g., GB Logistics"
                                                        value="<?php echo $edit ? htmlspecialchars($company['name']) : ''; ?>"
                                                        required
                                                        maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="legal_name">Legal Name</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="legal_name"
                                                        name="legal_name"
                                                        placeholder="e.g., GB Logistics Limited"
                                                        value="<?php echo $edit ? htmlspecialchars($company['legal_name'] ?? '') : ''; ?>"
                                                        maxlength="200">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email"
                                                        class="form-control"
                                                        id="email"
                                                        name="email"
                                                        placeholder="e.g., info@gblogistics.com"
                                                        value="<?php echo $edit ? htmlspecialchars($company['email'] ?? '') : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="phone"
                                                        name="phone"
                                                        placeholder="e.g., +64 9 123 4567"
                                                        value="<?php echo $edit ? htmlspecialchars($company['phone'] ?? '') : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobile">Mobile</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="mobile"
                                                        name="mobile"
                                                        placeholder="e.g., +64 21 123 4567"
                                                        value="<?php echo $edit ? htmlspecialchars($company['mobile'] ?? '') : ''; ?>"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="website">Website</label>
                                                    <input type="url"
                                                        class="form-control"
                                                        id="website"
                                                        name="website"
                                                        placeholder="e.g., https://www.gblogistics.com"
                                                        value="<?php echo $edit ? htmlspecialchars($company['website'] ?? '') : ''; ?>"
                                                        maxlength="150">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="timezone">Timezone</label>
                                                    <select class="form-control" id="timezone" name="timezone">
                                                        <?php foreach ($timezones as $tz => $label): 
                                                            $selected = ($edit && $company['timezone'] == $tz) || (!$edit && $tz == 'Pacific/Auckland') ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $tz; ?>" <?php echo $selected; ?>>
                                                                <?php echo $label; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="currency">Currency</label>
                                                    <select class="form-control" id="currency" name="currency">
                                                        <?php foreach ($currencies as $curr): 
                                                            $selected = ($edit && $company['currency'] == $curr) || (!$edit && $curr == 'NZD') ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $curr; ?>" <?php echo $selected; ?>>
                                                                <?php echo $curr; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            id="status"
                                                            name="status"
                                                            value="1"
                                                            <?php echo $edit && $company['status'] ? 'checked' : 'checked'; ?>>
                                                        <label class="form-check-label" for="status">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="country_id">Country</label>
                                                    <select class="form-control" id="country_id" name="country_id">
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $country): 
                                                            $selected = ($edit && $company['country_id'] == $country['id']) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $country['id']; ?>" <?php echo $selected; ?>>
                                                                <?php echo htmlspecialchars($country['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="state">State/Province</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="state"
                                                        name="state"
                                                        placeholder="e.g., Auckland"
                                                        value="<?php echo $edit ? htmlspecialchars($company['state'] ?? '') : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="city"
                                                        name="city"
                                                        placeholder="e.g., Auckland"
                                                        value="<?php echo $edit ? htmlspecialchars($company['city'] ?? '') : ''; ?>"
                                                        maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="postcode">Postcode</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="postcode"
                                                        name="postcode"
                                                        placeholder="e.g., 1010"
                                                        value="<?php echo $edit ? htmlspecialchars($company['postcode'] ?? '') : ''; ?>"
                                                        maxlength="20">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <textarea class="form-control"
                                                        id="address"
                                                        name="address"
                                                        rows="3"
                                                        placeholder="Full address"><?php echo $edit ? htmlspecialchars($company['address'] ?? '') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="logo">Logo</label>
                                                    <input type="file"
                                                        class="form-control"
                                                        id="logo"
                                                        name="logo"
                                                        accept="image/*">
                                                    <?php if ($edit && !empty($company['logo'])): ?>
                                                        <small class="form-text text-muted">Current logo: <?php echo htmlspecialchars($company['logo']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="notes">Notes</label>
                                                    <textarea class="form-control"
                                                        id="notes"
                                                        name="notes"
                                                        rows="3"
                                                        placeholder="Additional notes"><?php echo $edit ? htmlspecialchars($company['notes'] ?? '') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Buttons -->
                                    <div class="card-footer">
                                        <a href="company-list.php" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <?php echo $edit ? 'Update' : 'Save'; ?> Company
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
</body>

</html>