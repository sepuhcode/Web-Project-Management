<?php
require_once 'config.php';
$id=$_GET['id'];

$q=$conn->prepare("
SELECT s.*,p.name as project_name 
FROM schedules s
LEFT JOIN projects p ON s.project_id=p.id
WHERE s.id=?");

$q->bind_param("i",$id);
$q->execute();
$data=$q->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>View Schedule</title>
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
        <a href="all_projects.php">Active Projects</a>
        <a href="all_projects.php?status=completed" >Completed Projects</a>
        <a href="schedule.php" class="active">Schedule</a>
        <a href="troubleshooting.php" >Issue</a>
    </nav>
</div>
</header>

<main class="dashboard">

<section class="task-view">

<div class="view-header">
<div class="task-title-section">
<h1><?= $data['task_name'] ?></h1>
<!-- <span class="task-id">ID #<?= $data['id'] ?></span> -->
</div>
</div>

<div class="view-content">

<div class="info-grid">

    <div class="info-card">
    <h3>Schedule Info</h3>
    <div class="info-list">
    <div class="info-item"><span>Project</span><span><?= $data['project_name'] ?></span></div>
    <div class="info-item"><span>Date</span><span><?= $data['schedule_date'] ?></span></div>
    <div class="info-item"><span>Priority</span><span><?= $data['priority'] ?></span></div>
    </div>
</div>

<div class="info-card">
<h3>Timeline</h3>
<div class="info-list">
<div class="info-item"><span>Time</span><span><?= $data['start_time'] ?></span></div>
<div class="info-item"><span>Assigned</span><span><?= $data['assigned_to'] ?></span></div>
</div>
</div>

<div class="info-card">

    <div class="info-card">
        <h3>Description</h3>
        <div class="form-group">
        <textarea name="description" rows="10" required><?= htmlspecialchars($data['description']) ?></textarea>
        </div>
    </div>

</div>

</div>

<div class="view-actions">
<a href="schedule.php" class="btn-secondary">Back</a>
<a href="schedule_edit.php?id=<?= $id ?>" class="btn-primary">Edit</a>
</div>

</section>

</main>
</body>
</html>