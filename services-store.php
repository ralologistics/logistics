<?php
require 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;

    $name = trim($_POST['name'] ?? '');
    $description = $_POST['description'] ?? null;
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($name)) {
        header("Location: services-form.php?error=Service name required");
        exit;
    }

    // ================= UPDATE =================
    if ($id) {

        $sql = "UPDATE services SET name=?, description=?, status=? WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $name, $description, $status, (int)$id);

    }
    // ================= INSERT =================
    else {

        $sql = "INSERT INTO services (name, description, status) VALUES (?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $status);
    }

    // ================= EXECUTE =================
    if ($stmt->execute()) {
        header("Location: services-list.php");
        exit;
    } else {
        echo "<b>SQL Error:</b> " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
