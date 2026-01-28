<?php
session_start();
require 'db.php';

$edit = false;
$booking = null;

$customers = $conn->query("SELECT id,name FROM customers ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$iso_codes = $conn->query("SELECT id,code FROM iso_codes ORDER BY code")->fetch_all(MYSQLI_ASSOC);
$door_types = $conn->query("SELECT id,name FROM door_types ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$shippings = $conn->query("SELECT id,name FROM shippings ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$vessels = $conn->query("SELECT id,name FROM vessels ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$services = $conn->query("SELECT id,name FROM services WHERE status = 1 ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$endorsements = $conn->query("SELECT id,name FROM endorsements WHERE status = 1 ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$selected_services = [];
$selected_notes = [];

if (isset($_GET['id'])) {
    $edit = true;
    $stmt = $conn->prepare("SELECT * FROM import_job_bookings WHERE id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    // Load already-added services
    $stmt2 = $conn->prepare("
        SELECT ijs.service_id, s.name AS service_name
        FROM import_job_services ijs
        INNER JOIN services s ON s.id = ijs.service_id
        WHERE ijs.import_job_booking_id = ?
        ORDER BY ijs.id ASC
    ");
    $stmt2->bind_param("i", $_GET['id']);
    $stmt2->execute();
    $selected_services = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    // Load already-added notes
    $stmt3 = $conn->prepare("
        SELECT ijn.endorsement_id, e.name AS endorsement_name, ijn.note
        FROM import_job_notes ijn
        INNER JOIN endorsements e ON e.id = ijn.endorsement_id
        WHERE ijn.import_job_booking_id = ?
        ORDER BY ijn.id ASC
    ");
    $stmt3->bind_param("i", $_GET['id']);
    $stmt3->execute();
    $selected_notes = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Import Job Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro">
    <link rel="stylesheet" href="/ralo/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/ralo/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">


    <style>
        .content-wrapper {
            background: #f4f6f9
        }

        .card {
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .08)
        }

        .card-header {
            background: #6c757d;
            color: #fff;
            font-weight: 600
        }

        .card-primary .card-header {
            background: #007bff
        }

        .form-control {
            height: 38px;
            font-size: 14px
        }

        label {
            font-size: 13px;
            font-weight: 600
        }

        .upload-dropzone {
            border: 2px dashed #6cb6ff;
            border-radius: 6px;
            padding: 18px 12px;
            text-align: center;
            color: #6cb6ff;
            cursor: pointer;
            background: #f8fbff;
        }

        .upload-dropzone.dragover {
            background: #eef6ff;
            border-color: #2e8cff;
            color: #2e8cff;
        }

        .upload-dropzone .icon {
            font-size: 34px;
            margin-bottom: 8px;
        }

        .mini-btn {
            min-width: 80px;
        }

        .select2-dropdown {
            display: flex;
            flex-direction: column-reverse;
        }

        /* spacing thori clean lage */
        .select2-search--dropdown {
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        .border-none {
            border: none !important;
            border-bottom: 1px solid #ccc !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* Focus state */
        .border-none:focus {
            border-bottom: 2px solid #007bff !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include('top-navbar.php'); ?>
        <?php include('left-navbar.php'); ?>

        <div class="content-wrapper">

            <section class="content-header">
                <div class="container-fluid">
                    <h4>Import Job Booking</h4>
                </div>
            </section>

            <section class="content">
                <form method="POST" action="import-job-bookings-store.php" enctype="multipart/form-data">

                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">Import Job Booking Details</div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label>Booking No *</label>
                                        <input type="text" name="booking_no" class="form-control"
                                            value="<?= $edit ? htmlspecialchars($booking['booking_no'] ?? '') : '' ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Customer *</label>
                                        <select name="customer_id" id="customer_select" class="form-control" required>
                                            <option value=""></option>
                                            <?php foreach ($customers as $c): ?>
                                                <option value="<?= $c['id'] ?>"
                                                    <?= ($edit && $booking['customer_id'] == $c['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c['name']) ?>
                                                    <?php if (!empty($c['code'])): ?>
                                                        (<?= htmlspecialchars($c['code']) ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Document Received *</label>
                                        <input type="datetime-local" name="document_received_at" class="form-control"
                                            value="<?= $edit ? date('Y-m-d\TH:i', strtotime($booking['document_received_at'])) : '' ?>" required>
                                    </div>

                                    <div class="text-right mt-4">
                                        <a href="import-job-bookings-list.php" class="btn btn-light">Back</a>
                                        <button type="reset" class="btn btn-light">Clear</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>

        <footer class="main-footer text-sm">
            <strong>© 2026 Ralo Logistics</strong>
        </footer>

    </div>

    <script src="/ralo/plugins/jquery/jquery.min.js"></script>
    <script src="/ralo/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/ralo/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            const fileInput = document.getElementById('document_file');
            const dropzone = document.getElementById('uploadDropzone');
            const filenameEl = document.getElementById('uploadFilename');

            function setFilename(name) {
                if (!filenameEl) return;
                filenameEl.textContent = name ? name : 'No file selected';
            }

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', () => fileInput.click());

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                });
                dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        setFilename(e.dataTransfer.files[0].name);
                    }
                });

                fileInput.addEventListener('change', () => {
                    const f = fileInput.files && fileInput.files[0];
                    setFilename(f ? f.name : '');
                });
            }

            const servicesTableBody = document.querySelector('#services_table tbody');
            const serviceSelect = document.getElementById('service_select');
            const addServiceBtn = document.getElementById('add_service_btn');
            const clearServicesBtn = document.getElementById('clear_services_btn');

            function renumberRows(tbody) {
                if (!tbody) return;
                [...tbody.querySelectorAll('tr')].forEach((tr, idx) => {
                    const td = tr.querySelector('td');
                    if (td) td.textContent = idx + 1;
                });
            }

            function serviceAlreadyAdded(serviceId) {
                if (!servicesTableBody) return false;
                return !!servicesTableBody.querySelector(`tr[data-service-id="${serviceId}"]`);
            }

            if (addServiceBtn && serviceSelect && servicesTableBody) {
                addServiceBtn.addEventListener('click', () => {
                    const serviceId = (serviceSelect.value || '').trim();
                    if (!serviceId) return;
                    if (serviceAlreadyAdded(serviceId)) return;

                    const serviceName = serviceSelect.options[serviceSelect.selectedIndex].text;

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-service-id', serviceId);
                    tr.innerHTML = `
                        <td></td>
                        <td>
                            ${escapeHtml(serviceName)}
                            <input type="hidden" name="service_ids[]" value="${escapeAttr(serviceId)}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-service">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    servicesTableBody.appendChild(tr);
                    renumberRows(servicesTableBody);
                    serviceSelect.value = '';
                });
            }

            if (clearServicesBtn && servicesTableBody && serviceSelect) {
                clearServicesBtn.addEventListener('click', () => {
                    servicesTableBody.innerHTML = '';
                    serviceSelect.value = '';
                });
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-service');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                renumberRows(servicesTableBody);
            });

            const notesTableBody = document.querySelector('#notes_table tbody');
            const endorsementSelect = document.getElementById('endorsement_select');
            const noteInput = document.getElementById('note_input');
            const addNoteBtn = document.getElementById('add_note_btn');
            const clearNotesBtn = document.getElementById('clear_notes_btn');

            if (addNoteBtn && endorsementSelect && notesTableBody) {
                addNoteBtn.addEventListener('click', () => {
                    const endorsementId = (endorsementSelect.value || '').trim();
                    if (!endorsementId) return;
                    const endorsementName = endorsementSelect.options[endorsementSelect.selectedIndex].text;
                    const noteText = (noteInput && noteInput.value) ? noteInput.value.trim() : '';

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-endorsement-id', endorsementId);
                    tr.innerHTML = `
                        <td></td>
                        <td>
                            ${escapeHtml(endorsementName)}
                            <input type="hidden" name="endorsement_ids[]" value="${escapeAttr(endorsementId)}">
                        </td>
                        <td>
                            ${escapeHtml(noteText)}
                            <input type="hidden" name="notes[]" value="${escapeAttr(noteText)}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-note">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    notesTableBody.appendChild(tr);
                    renumberRows(notesTableBody);
                    endorsementSelect.value = '';
                    if (noteInput) noteInput.value = '';
                });
            }

            if (clearNotesBtn && notesTableBody && endorsementSelect && noteInput) {
                clearNotesBtn.addEventListener('click', () => {
                    notesTableBody.innerHTML = '';
                    endorsementSelect.value = '';
                    noteInput.value = '';
                });
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-note');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                renumberRows(notesTableBody);
            });

            function escapeHtml(str) {
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function escapeAttr(str) {
                return escapeHtml(str).replaceAll('`', '&#096;');
            }

            // Renumber preloaded rows (edit mode)
            renumberRows(servicesTableBody);
            renumberRows(notesTableBody);
        })();

        $(document).ready(function() {
            $('#customer_select').select2({
                placeholder: "Customer Name / Customer Code",
                allowClear: true,
                width: '100%',
                minimumInputLength: 1
            });
        });
    </script>
</body>

</html>