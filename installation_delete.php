<?php
require_once 'config.php';

$task_id = $_GET['id'] ?? 0;

if (!$task_id) {
    die("Invalid ID");
}

// DELETE SPEC DULU (biar ga orphan)
$query = "DELETE FROM task_specifications WHERE task_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();

// DELETE TASK
$query = "DELETE FROM tasks WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();

// BALIK KE HALAMAN SEBELUMNYA
$redirect = $_GET['redirect'] ?? 'installation.php';

header("Location: $redirect");
exit;