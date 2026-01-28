<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if (empty($name)) {
        header("Location: endorsements-form.php?error=Name is required");
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE endorsements SET name = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $status, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO endorsements (name, status) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $status);
    }

    if ($stmt->execute()) {
        header("Location: endorsements-list.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error . "<br>";
        echo "SQL Error: " . $conn->error;
        echo "<br><a href='endorsements-form.php'>Go Back</a>";
    }

    $stmt->close();
} else {
    header("Location: endorsements-list.php");
    exit;
}

$conn->close();
?>
