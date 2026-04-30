<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? 0;

/* ===== GET PROJECT ===== */
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit;
}

/* ===== HANDLE SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = sanitize($conn, $_POST['task_name']);
    $area = sanitize($conn, $_POST['area']);
    $priority = $_POST['priority'] ?? 'low';
    $progress = min(100, (int)$_POST['progress']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $description = $_POST['description'];

    /* ===== FIX STATUS ===== */
    $allowed_status = ['pending', 'in-progress', 'completed'];
    $status = $_POST['status'] ?? 'pending';

    if (!in_array($status, $allowed_status)) {
        $status = 'pending';
    }

    /* ===== TECH SPEC ===== */
    $cable_type = sanitize($conn, $_POST['cable_type'] ?? '');
    $cable_length = (float)($_POST['cable_length'] ?? 0);
    $connection_type = sanitize($conn, $_POST['connection_type'] ?? '');
    $cable_label = sanitize($conn, $_POST['cable_label'] ?? '');

    /* ===== INSERT TASK ===== */
    $stmt = $conn->prepare("
        INSERT INTO tasks 
        (project_id, title, description, type, status, progress, start_date, end_date, area, priority)
        VALUES (?, ?, ?, 'installation', ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssissss",
        $project_id,
        $title,
        $description,
        $status,
        $progress,
        $start,
        $end,
        $area,
        $priority
    );

    $stmt->execute();
    $task_id = $stmt->insert_id;

    /* ===== INSERT SPEC ===== */
    $stmt = $conn->prepare("
        INSERT INTO task_specifications 
        (task_id, cable_type, cable_length, connection_type, cable_label)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isdss",
        $task_id,
        $cable_type,
        $cable_length,
        $connection_type,
        $cable_label
    );

    $stmt->execute();

    header("Location: installation.php?project_id=$project_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Installation Task</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>

<header class="navbar">
    <div class="nav-container">
        <div class="logo">
            <h1>Project Team Report</h1>
        </div>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="all_projects.php" class="active">Active Projects</a>
            <a href="schedule.php">Schedule</a>
            <a href="troubleshooting.php">Issue</a>
        </nav>
    </div>
</header>

<main class="dashboard">

<!-- ===== BREADCRUMB ===== -->
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span>></span>
    <a href="all_projects.php">Active Projects</a>
    <span>></span>
    <a href="project_details.php?id=<?= $project['id']; ?>">
        <?= htmlspecialchars($project['name']); ?>
    </a>
    <span>></span>
    <a href="installation.php?project_id=<?= $project['id']; ?>">Installation</a>
    <span>></span>
    <span>Add Task</span>
</nav>

<!-- ===== FORM ===== -->
<section class="edit-form">
<div class="form-card">

<h2>Add New Installation Task</h2>

<form method="POST" class="task-form">

<div class="form-grid">

    <div class="form-group">
        <label>Task Name *</label>
        <input type="text" name="task_name" required>
    </div>

    <div class="form-group">
        <label>Area *</label>
        <input type="text" name="area" required>
    </div>

    <div class="form-group">
        <label>Priority *</label>
        <select name="priority" required>
            <option value="">Select Priority</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
        </select>
    </div>

    <div class="form-group">
        <label>Progress *</label>
        <input type="number" name="progress" min="0" max="100" required>
    </div>

    <!-- 🔥 STATUS FIX -->
    <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="form-group">
        <label>Start Date *</label>
        <input type="date" name="start_date" required>
    </div>

    <div class="form-group">
        <label>Finish Date *</label>
        <input type="date" name="end_date" required>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="5"></textarea>
    </div>

</div>

<!-- ===== TECH SPEC ===== -->
<div class="form-section">
<h3>Technical Specifications</h3>

<div class="form-grid">
    <div class="form-group">
        <label>Cable Type</label>
        <input type="text" name="cable_type">
    </div>

    <div class="form-group">
        <label>Cable Length (m)</label>
        <input type="number" step="0.1" name="cable_length">
    </div>

    <div class="form-group">
        <label>Connection Type</label>
        <input type="text" name="connection_type">
    </div>

    <div class="form-group">
        <label>Cable Label</label>
        <input type="text" name="cable_label">
    </div>
</div>
</div>

<!-- ===== ACTION ===== -->
<div class="form-actions">
    <button type="submit" class="btn btn-primary">Add Task</button>

    <button type="button" class="btn btn-secondary"
        onclick="window.location.href='installation.php?project_id=<?= $project['id']; ?>'">
        Cancel
    </button>
</div>

</form>
</div>
</section>

</main>

</body>
</html>