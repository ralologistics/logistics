<?php
session_start();

$edit = false;
$item = null;
if (isset($_GET['id'])) {
    $edit = true;
    require 'db.php';
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM endorsements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Endorsement</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
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
                            <h1><?php echo $edit ? 'Edit' : 'Create'; ?> Endorsement</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="endorsements-list.php">Endorsements</a></li>
                                <li class="breadcrumb-item active"><?php echo $edit ? 'Edit' : 'Create'; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <form action="endorsements-store.php" method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    <?php endif; ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header"><h3 class="card-title">Information</h3></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter name" value="<?php echo $edit ? htmlspecialchars($item['name']) : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?php echo $edit && $item['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo $edit && $item['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <a href="endorsements-list.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> <?php echo $edit ? 'Update' : 'Create'; ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>

        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <script src="/ralo/plugins/jquery/jquery.min.js"></script>
    <script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/ralo/dist/js/adminlte.min.js"></script>
</body>

</html>
