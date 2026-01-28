<?php
require 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;

    $job_type = trim($_POST['job_type']);
    $service_id = (int)$_POST['service_id'];
    $container_id = (int)$_POST['container_id'];

    if (empty($job_type) || empty($service_id) || empty($container_id)) {
        header("Location: import-job-services-form.php?error=Job Type, Service, and Container are required");
        exit;
    }

    // Check if the combination already exists (for insert only)
    if (!$id) {
        $check_stmt = $conn->prepare("SELECT id FROM import_job_services WHERE service_id = ? AND container_id = ?");
        $check_stmt->bind_param("ii", $service_id, $container_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows > 0) {
            header("Location: import-job-services-form.php?error=This service and container combination already exists");
            exit;
        }
        $check_stmt->close();
    }

    // ================= UPDATE =================
    if ($id) {

        $sql = "UPDATE import_job_services SET job_type=?, service_id=?, container_id=? WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $job_type, $service_id, $container_id, (int)$id);

    }
    // ================= INSERT =================
    else {

        $sql = "INSERT INTO import_job_services (job_type, service_id, container_id) VALUES (?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $job_type, $service_id, $container_id);
    }

    // ================= EXECUTE =================
    if ($stmt->execute()) {
        header("Location: import-job-services-list.php");
        exit;
    } else {
        echo "<b>SQL Error:</b> " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
