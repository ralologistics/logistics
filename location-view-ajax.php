<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid location ID</div>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT l.*, c.name as company_name, lt.name as location_type_name, dt.name as door_type_name, lift.name as lift_type_name, co.name as country_name 
                        FROM locations l 
                        LEFT JOIN companies c ON l.company_id = c.id 
                        LEFT JOIN location_types lt ON l.location_type_id = lt.id 
                        LEFT JOIN door_types dt ON l.door_type_id = dt.id 
                        LEFT JOIN lift_types lift ON l.lift_type_id = lift.id 
                        LEFT JOIN countries co ON l.country_id = co.id 
                        WHERE l.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$location) {
    echo '<div class="alert alert-danger">Location not found</div>';
    exit;
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
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['name']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Location Code</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['location_code'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['company_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Location Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['location_type_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Door Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['door_type_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lift Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['lift_type_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                </div>

                <!-- Address Row -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Building</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['building'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Street No</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['street_no'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Street</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['street'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Suburb</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['suburb'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <!-- City etc -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['city'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['state'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Postcode</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['postcode'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['country_name'] ?? 'N/A'); ?>" readonly>
                    </div>
                </div>

                <!-- Contact -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['contact_person'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['phone'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['mobile'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($location['email'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <!-- Other -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Send Tracking Email</label>
                        <input type="text" class="form-control" value="<?php echo $location['send_tracking_email'] ? 'Yes' : 'No'; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Special Instruction</label>
                        <textarea class="form-control" readonly><?php echo htmlspecialchars($location['special_instruction'] ?? ''); ?></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-warning" onclick="editLocation(<?php echo $location['id']; ?>)">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
    </div>
</div>