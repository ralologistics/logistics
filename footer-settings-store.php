<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $site_name = trim($_POST['site_name'] ?? '');
    $copyright_start_year = !empty($_POST['copyright_start_year']) ? (int)$_POST['copyright_start_year'] : null;
    $version = trim($_POST['version'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    // Validation
    if (empty($site_name)) {
        header("Location: footer-settings-form.php?error=Site name is required");
        exit;
    }

    if (empty($copyright_start_year)) {
        header("Location: footer-settings-form.php?error=Copyright start year is required");
        exit;
    }

    if (empty($version)) {
        header("Location: footer-settings-form.php?error=Version is required");
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE footer_settings SET 
            site_name=?, 
            copyright_start_year=?, 
            version=?, 
            status=? 
            WHERE id=?");
        $stmt->bind_param("ssiii", 
            $site_name, 
            $copyright_start_year, 
            $version, 
            $status,
            $id);

        if ($stmt->execute()) {
            header("Location: footer-settings-list.php?success=1");
        } else {
            header("Location: footer-settings-form.php?id=" . $id . "&error=" . urlencode($stmt->error));
        }
        $stmt->close();
    } else {
        // CREATE
        $stmt = $conn->prepare("INSERT INTO footer_settings (site_name, copyright_start_year, version, status) 
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", 
            $site_name, 
            $copyright_start_year, 
            $version, 
            $status);

        if ($stmt->execute()) {
            header("Location: footer-settings-list.php?success=1");
        } else {
            header("Location: footer-settings-form.php?error=" . urlencode($stmt->error));
        }
        $stmt->close();
    }

    $conn->close();
} else {
    header("Location: footer-settings-list.php");
}
?>
