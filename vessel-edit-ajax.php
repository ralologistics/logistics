<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid vessel ID</div>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM vessels WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vessel = $result->fetch_assoc();
$stmt->close();

if (!$vessel) {
    echo '<div class="alert alert-danger">Vessel not found</div>';
    exit;
}

// Get countries for dropdown
$countries_result = $conn->query("SELECT id, name FROM countries WHERE is_active = 1 ORDER BY name");
$countries = $countries_result->fetch_all(MYSQLI_ASSOC);

// Get ship types for dropdown
$ship_types_result = $conn->query("SELECT id, type_name FROM ship_types ORDER BY type_name");
$ship_types = $ship_types_result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<form id="editVesselForm" onsubmit="saveVessel(event, <?php echo $vessel['id']; ?>)">
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
                               value="<?php echo htmlspecialchars($vessel['name']); ?>" 
                               required 
                               maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-control" name="country_id" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): 
                                $selected = $vessel['country_id'] == $country['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $country['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($country['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ship Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="ship_type_id" required>
                            <option value="">Select Ship Type</option>
                            <?php foreach ($ship_types as $ship_type): 
                                $selected = $vessel['ship_type_id'] == $ship_type['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $ship_type['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($ship_type['type_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">IMO Number</label>
                        <input type="text" 
                               class="form-control" 
                               name="imo_number" 
                               value="<?php echo htmlspecialchars($vessel['imo_number'] ?? ''); ?>" 
                               maxlength="20">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MMSI</label>
                        <input type="text" 
                               class="form-control" 
                               name="mmsi" 
                               value="<?php echo htmlspecialchars($vessel['mmsi'] ?? ''); ?>" 
                               maxlength="20">
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Call Sign</label>
                        <input type="text" 
                               class="form-control" 
                               name="call_sign" 
                               value="<?php echo htmlspecialchars($vessel['call_sign'] ?? ''); ?>" 
                               maxlength="20"
                               style="text-transform: uppercase;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Built</label>
                        <input type="number" 
                               class="form-control" 
                               name="built_year" 
                               value="<?php echo $vessel['built_year'] ?? ''; ?>" 
                               min="1900"
                               max="<?php echo date('Y'); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Length (m)</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control" 
                               name="length_m" 
                               value="<?php echo $vessel['length_m'] ?? ''; ?>"
                               id="length_m">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Width (m)</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control" 
                               name="width_m" 
                               value="<?php echo $vessel['width_m'] ?? ''; ?>"
                               id="width_m">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Size (m)</label>
                        <input type="text" 
                               class="form-control" 
                               id="size_display" 
                               readonly
                               placeholder="Auto-calculated">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Draught (m)</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control" 
                               name="draught_m" 
                               value="<?php echo $vessel['draught_m'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gross Tonnage</label>
                        <input type="number" 
                               class="form-control" 
                               name="gross_tonnage" 
                               value="<?php echo $vessel['gross_tonnage'] ?? ''; ?>">
                    </div>
                </div>

                <!-- Third Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Net Tonnage</label>
                        <input type="number" 
                               class="form-control" 
                               name="net_tonnage" 
                               value="<?php echo $vessel['net_tonnage'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dead Weight</label>
                        <input type="number" 
                               class="form-control" 
                               name="dead_weight" 
                               value="<?php echo $vessel['dead_weight'] ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
// Auto-calculate size
function updateSize() {
    const length = document.getElementById('length_m').value;
    const width = document.getElementById('width_m').value;
    if (length && width) {
        document.getElementById('size_display').value = length + ' x ' + width + ' m';
    } else {
        document.getElementById('size_display').value = '';
    }
}

document.getElementById('length_m')?.addEventListener('input', updateSize);
document.getElementById('width_m')?.addEventListener('input', updateSize);

// Initialize size on load
updateSize();

// Auto-uppercase call sign
document.querySelector('input[name="call_sign"]')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

function saveVessel(event, id) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('id', id);
    
    // Disable submit button
    const submitBtn = event.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    $.ajax({
        url: 'vessel-store.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    // Close modal and reload page
                    $('#editVesselModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Update';
                }
            } catch(e) {
                // If response is not JSON, assume success (redirect response)
                $('#editVesselModal').modal('hide');
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            alert('Error saving vessel: ' + error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update';
        }
    });
}
</script>
