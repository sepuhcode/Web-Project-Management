<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = sanitize($conn, $_POST['name']);
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pic = $_POST['pic'];
    $progress = $_POST['progress'];
    $description = sanitize($conn, $_POST['description']);

    $query = "INSERT INTO projects (name, status, start_date, end_date, pic, progress, description) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssis", $name, $status, $start_date, $end_date, $pic, $progress, $description);
    $stmt->execute();

    header("Location: all_projects.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project - SIS Dashboard</title>
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
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span class="breadcrumb-separator">></span>
    <a href="all_projects.php">Active Projects</a>
    <span class="breadcrumb-separator">></span>
    <span class="breadcrumb-current">Add New Project</span>
</nav>

<!-- FORM -->
<section class="edit-form">
<div class="form-card">

<h2>Add Projects</h2>

<form method="POST" class="project-form" id="addProjectForm">

<div class="form-grid">

<div class="form-group">
    <label>Project Name</label>
    <input type="text" name="name" required>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="status">
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="on-hold">On Hold</option>
    </select>
</div>

<div class="form-group">
    <label>Start Date</label>
    <input type="date" name="start_date" required>
</div>

<div class="form-group">
    <label>End Date</label>
    <input type="date" name="end_date" required>
</div>

<div class="form-group">
    <label>PIC</label>
    <input type="text" name="pic" min="1" required>
</div>

<div class="form-group">
    <label>Overall Progress (%)</label>
    <input type="number" name="progress" min="0" max="100" required>
</div>

</div>

<div class="form-group full-width">
    <label>Project Description</label>
    <textarea name="description" rows="4"></textarea>
</div>

<!-- STAGE PROGRESS (visual only, belum masuk DB) -->
<div class="progress-section">
    <h3>Stage Progress</h3>

    <div class="stage-progress-grid">

        <div class="form-group">
            <label>Survey (%)</label>
            <input type="number" value="0">
        </div>

        <div class="form-group">
            <label>Installation (%)</label>
            <input type="number" value="0">
        </div>

        <div class="form-group">
            <label>Programming (%)</label>
            <input type="number" value="0">
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
    <button type="submit" class="btn-primary">Create Project</button>
    <button type="button" class="btn-secondary" onclick="window.location.href='all_projects.php'">Cancel</button>
</div>

</form>
</div>
</section>

</main>

</body>
</html>