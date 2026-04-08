<?php
require_once 'config.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $stmt=$conn->prepare("
    INSERT INTO schedules(task_name,project_id,schedule_date,start_time,assigned_to,description,priority)
    VALUES (?,?,?,?,?,?,?)");

    $stmt->bind_param("sisssss",
    $_POST['task_name'],
    $_POST['project_id'],
    $_POST['schedule_date'],
    $_POST['start_time'],
    $_POST['assigned_to'],
    $_POST['description'],
    $_POST['priority']
    );

    $stmt->execute();
    header("Location:schedule.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Schedule</title>
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

<section class="edit-form">
<div class="form-card">

<h2>Add New Schedule Item</h2>

<form method="POST" class="task-form">

<div class="form-grid">

<div class="form-group">
<label>Task Name</label>
<input type="text" name="task_name" required>
</div>

<div class="form-group">
<label>Project</label>
<select name="project_id" required>
<?php
$p=$conn->query("SELECT * FROM projects");
while($pr=$p->fetch_assoc()){
echo "<option value='{$pr['id']}'>{$pr['name']}</option>";
}
?>
</select>
</div>

<div class="form-group">
<label>Date</label>
<input type="date" name="schedule_date" required>
</div>

<div class="form-group">
<label>Priority</label>
<select name="priority">
<option value="low">Low</option>
<option value="medium">Medium</option>
<option value="high">High</option>
</select>
</div>

<div class="form-group">
<label>Assigned</label>
<input type="text" name="assigned_to">
</div>

<div class="form-group">
<label>Time</label>
<input type="time" name="start_time">
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description" rows="10" required></textarea>
</div>

</div>

<div class="form-actions">
<button class="btn-primary">Add Schedule</button>
<a href="schedule.php" class="btn-secondary">Cancel</a>
</div>

</form>

</div>
</section>

</main>
</body>
</html>