<?php
require_once 'config.php';

$project_id = $_GET['id'] ?? 0;

$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = sanitize($conn, $_POST['project_name']);
    $status = $_POST['project_status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pic = $_POST['pic'];
    $progress = $_POST['project_progress'];
    $description = sanitize($conn, $_POST['project_description']);

    $query = "UPDATE projects 
              SET name=?, status=?, start_date=?, end_date=?, pic=?, progress=?, description=? 
              WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssi", $name, $status, $start_date, $end_date, $pic, $progress, $description, $project_id);
    $stmt->execute();

    header("Location: project_details.php?id=" . $project_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - SIS Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1>Project Team Report</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php" >Home</a>
                <a href="all_projects.php" class="active">Active Projects</a>
                <a href="all_projects.php?status=completed">Completed Project</a>
                <a href="schedule.php">Schedule</a>
                <a href="troubleshooting.php">Issue</a>
            </nav>
        </div>
    </header>

<main class="dashboard">

<!-- BREADCRUMB -->

<!-- FORM -->
<section class="edit-form">
<div class="form-card">

<h2>Edit Project Details</h2>

<form method="POST" class="project-form">

<div class="form-grid">

<div class="form-group">
    <label>Project Name</label>
    <input type="text" name="project_name" value="<?= htmlspecialchars($project['name']); ?>" required>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="project_status">
        <option value="active" <?= $project['status']=='active'?'selected':''; ?>>Active</option>
        <option value="completed" <?= $project['status']=='completed'?'selected':''; ?>>Completed</option>
        <option value="on-hold" <?= $project['status']=='on-hold'?'selected':''; ?>>On Hold</option>
    </select>
</div>

<div class="form-group">
    <label>Start Date</label>
    <input type="date" name="start_date" value="<?= $project['start_date']; ?>" required>
</div>

<div class="form-group">
    <label>End Date</label>
    <input type="date" name="end_date" value="<?= $project['end_date']; ?>" required>
</div>

<div class="form-group">
    <label>PIC</label>
    <input type="text" name="pic" min="1" value="<?= htmlspecialchars($project['pic']); ?>" required>
</div>

<div class="form-group">
    <label>Overall Progress (%)</label>
    <input type="number" name="project_progress" value="<?= $project['progress']; ?>" required>
</div>

</div>

<div class="form-group full-width">
    <label>Project Description</label>
    <textarea name="project_description" rows="4"><?= htmlspecialchars($project['description']); ?></textarea>
</div>

<!-- STAGE PROGRESS (visual only, sama seperti HTML) -->
<div class="progress-section">
    <h3>Stage Progress</h3>

    <div class="stage-progress-grid">

        <div class="form-group">
            <label>Survey (%)</label>
            <input type="number" value="100">
        </div>

        <div class="form-group">
            <label>Installation (%)</label>
            <input type="number" value="85">
        </div>

        <div class="form-group">
            <label>Programming (%)</label>
            <input type="number" value="10">
        </div>

        <div class="form-group">
            <label>Testing (%)</label>
            <input type="number" value="0">
        </div>

        <div class="form-group">
            <label>Training (%)</label>
            <input type="number" value="0">
        </div>

    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn-primary">Save Changes</button>
    <button type="button" class="btn-secondary" onclick="window.location.href='project_details.php?id=<?= $project['id']; ?>'">Cancel</button>
</div>

</form>

</div>
</section>

</main>

</body>
</html>