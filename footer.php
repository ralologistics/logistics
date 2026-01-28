<?php
// Fetch footer settings from database
$footerData = [
    'site_name' => 'Ralo Logistics',
    'copyright_start_year' => 2020,
    'version' => '1.0.0'
];

try {
    require 'db.php';
    $result = $conn->query("SELECT * FROM footer_settings WHERE status = 1 LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $footerData = $result->fetch_assoc();
    }
    $conn->close();
} catch (Exception $e) {
    // Use default footer data if database query fails
}

$currentYear = date('Y');
$copyrightYears = $footerData['copyright_start_year'] == $currentYear 
    ? $currentYear 
    : $footerData['copyright_start_year'] . ' - ' . $currentYear;
?>

<!-- Main Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; <?php echo $copyrightYears; ?> <a href="<?php echo URL; ?>/index.php"><?php echo htmlspecialchars($footerData['site_name']); ?></a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> <?php echo htmlspecialchars($footerData['version']); ?>
    </div>
</footer>
</div>
