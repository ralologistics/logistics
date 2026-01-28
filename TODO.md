# TODO: Modify import_job_services table and CRUD

## Steps to Complete

- [x] Modify create_import_job_services_table.php to update the CREATE TABLE statement for import_job_services with new fields: job_id, job_type, and remove import_job_booking_id.
- [x] Update import-job-services-form.php to include form fields for job_id (dropdown from job_bookings table) and job_type (dropdown with options 'import', 'cart', 'export', 'swing').
- [x] Update import-job-services-store.php to handle INSERT/UPDATE for the new fields.
- [x] Update import-job-services-list.php to display the new fields in the table.
- [x] Update import-job-services-delete.php if needed (likely no changes required).
- [x] Test the CRUD operations to ensure everything works correctly.
