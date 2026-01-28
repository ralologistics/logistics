<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid location ID</div>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();
$stmt->close();

if (!$location) {
    echo '<div class="alert alert-danger">Location not found</div>';
    exit;
}

// Get options for dropdowns
$companies_result = $conn->query("SELECT id, name FROM companies ORDER BY name");
$companies = $companies_result->fetch_all(MYSQLI_ASSOC);

$location_types_result = $conn->query("SELECT id, name FROM location_types ORDER BY name");
$location_types = $location_types_result->fetch_all(MYSQLI_ASSOC);

$door_types_result = $conn->query("SELECT id, name FROM door_types ORDER BY name");
$door_types = $door_types_result->fetch_all(MYSQLI_ASSOC);

$lift_types_result = $conn->query("SELECT id, name FROM lift_types ORDER BY name");
$lift_types = $lift_types_result->fetch_all(MYSQLI_ASSOC);

$countries_result = $conn->query("SELECT id, name FROM countries WHERE is_active = 1 ORDER BY name");
$countries = $countries_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<form id="editLocationForm" onsubmit="saveLocation(event, <?php echo $location['id']; ?>)">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- First Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="name" 
                               value="<?php echo htmlspecialchars($location['name']); ?>" 
                               required 
                               maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Location Code</label>
                        <input type="text" 
                               class="form-control" 
                               name="location_code" 
                               value="<?php echo htmlspecialchars($location['location_code'] ?? ''); ?>" 
                               maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-control" name="company_id" required>
                            <option value="">Select Company</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?php echo $company['id']; ?>" <?php echo ($location['company_id'] == $company['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($company['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Location Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="location_type_id" required>
                            <option value="">Select Location Type</option>
                            <?php foreach ($location_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo ($location['location_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Door Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="door_type_id" required>
                            <option value="">Select Door Type</option>
                            <?php foreach ($door_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo ($location['door_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lift Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="lift_type_id" required>
                            <option value="">Select Lift Type</option>
                            <?php foreach ($lift_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo ($location['lift_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Address Row -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Building</label>
                        <input type="text" 
                               class="form-control" 
                               name="building" 
                               value="<?php echo htmlspecialchars($location['building'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Street No</label>
                        <input type="text" 
                               class="form-control" 
                               name="street_no" 
                               value="<?php echo htmlspecialchars($location['street_no'] ?? ''); ?>" 
                               maxlength="20">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Street</label>
                        <input type="text" 
                               class="form-control" 
                               name="street" 
                               value="<?php echo htmlspecialchars($location['street'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Suburb</label>
                        <input type="text" 
                               class="form-control" 
                               name="suburb" 
                               value="<?php echo htmlspecialchars($location['suburb'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                </div>

                <!-- City etc -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" 
                               class="form-control" 
                               name="city" 
                               value="<?php echo htmlspecialchars($location['city'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" 
                               class="form-control" 
                               name="state" 
                               value="<?php echo htmlspecialchars($location['state'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Postcode</label>
                        <input type="text" 
                               class="form-control" 
                               name="postcode" 
                               value="<?php echo htmlspecialchars($location['postcode'] ?? ''); ?>" 
                               maxlength="20">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-control" name="country_id" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['id']; ?>" <?php echo ($location['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Contact -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" 
                               class="form-control" 
                               name="contact_person" 
                               value="<?php echo htmlspecialchars($location['contact_person'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input type="text" 
                               class="form-control" 
                               name="phone" 
                               value="<?php echo htmlspecialchars($location['phone'] ?? ''); ?>" 
                               maxlength="30">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" 
                               class="form-control" 
                               name="mobile" 
                               value="<?php echo htmlspecialchars($location['mobile'] ?? ''); ?>" 
                               maxlength="30">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               name="email" 
                               value="<?php echo htmlspecialchars($location['email'] ?? ''); ?>" 
                               maxlength="150">
                    </div>
                </div>

                <!-- Other -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Send Tracking Email</label>
                        <select class="form-control" name="send_tracking_email">
                            <option value="0" <?php echo (!$location['send_tracking_email']) ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?php echo ($location['send_tracking_email']) ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Special Instruction</label>
                        <textarea class="form-control" name="special_instruction"><?php echo htmlspecialchars($location['special_instruction'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function saveLocation(event, id) {
    event.preventDefault();
    
    const form = document.getElementById('editLocationForm');
    const formData = new FormData(form);
    formData.append('id', id);
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    $.ajax({
        url: 'location-store.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#editLocationModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Update';
                }
            } catch(e) {
                // If response is not JSON, assume success (redirect response)
                $('#editLocationModal').modal('hide');
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            alert('Error saving location: ' + error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update';
        }
    });
}
</script>