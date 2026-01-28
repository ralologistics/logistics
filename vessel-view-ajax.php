<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid vessel ID</div>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT v.*, c.name as country_name, st.type_name as ship_type_name 
                        FROM vessels v 
                        LEFT JOIN countries c ON v.country_id = c.id 
                        LEFT JOIN ship_types st ON v.ship_type_id = st.id 
                        WHERE v.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vessel = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$vessel) {
    echo '<div class="alert alert-danger">Vessel not found</div>';
    exit;
}

// Format size
$size = '';
if ($vessel['length_m'] && $vessel['width_m']) {
    $size = $vessel['length_m'] . ' x ' . $vessel['width_m'] . ' m';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form>
                <!-- First Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['name']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['country_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ship Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['ship_type_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">IMO Number</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['imo_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MMSI</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['mmsi'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Call Sign</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vessel['call_sign'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Built</label>
                        <input type="text" class="form-control" value="<?php echo $vessel['built_year'] ?? ''; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Size (m)</label>
                        <input type="text" class="form-control" value="<?php echo $size; ?>" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Draught (m)</label>
                        <input type="text" class="form-control" value="<?php echo $vessel['draught_m'] ?? ''; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gross Tonnage</label>
                        <input type="text" class="form-control" value="<?php echo $vessel['gross_tonnage'] ?? ''; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Net Tonnage</label>
                        <input type="text" class="form-control" value="<?php echo $vessel['net_tonnage'] ?? ''; ?>" readonly>
                    </div>
                </div>

                <!-- Third Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Dead Weight</label>
                        <input type="text" class="form-control" value="<?php echo $vessel['dead_weight'] ?? ''; ?>" readonly>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" onclick="editVessel(<?php echo $vessel['id']; ?>)">Edit</button>
</div>
