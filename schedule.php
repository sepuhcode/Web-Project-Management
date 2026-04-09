<?php
require_once 'config.php';


/* ===== DELETE ===== */
if (isset($_GET['delete_id'])) {

    $id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("DELETE FROM schedules WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // redirect biar gak ke-trigger ulang
    header("Location: schedule.php");
    exit;
}

$query = "
SELECT s.*, p.name as project_name 
FROM schedules s
LEFT JOIN projects p ON s.project_id = p.id
ORDER BY s.schedule_date ASC
";


$result = $conn->query($query);

$total=0;$this_week=0;$next_week=0;$later=0;

$today = date('Y-m-d');
$week = date('Y-m-d',strtotime('+7 days'));
$next = date('Y-m-d',strtotime('+14 days'));

$data=[];

while($row=$result->fetch_assoc()){
    $total++;

    if($row['schedule_date'] <= $week) $this_week++;
    elseif($row['schedule_date'] <= $next) $next_week++;
    else $later++;

    $data[]=$row;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Schedule</title>
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
                <a href="all_projects.php" >Active Projects</a>
                <a href="all_projects.php?status=completed">Completed Project</a>
                <a href="schedule.php" class="active">Schedule</a>
                <a href="troubleshooting.php">Issue</a>
            </nav>
        </div>
    </header>

<main class="dashboard">

<!-- HEADER -->
<div class="projects-header">
<h1>Schedule Overview</h1>

<div class="project-stats">
<div class="stat-item"><div class="stat-number"><?= $total ?></div><div class="stat-label">Total</div></div>
<div class="stat-item"><div class="stat-number"><?= $this_week ?></div><div class="stat-label">This Week</div></div>
<div class="stat-item"><div class="stat-number"><?= $next_week ?></div><div class="stat-label">Next Week</div></div>
<div class="stat-item"><div class="stat-number"><?= $later ?></div><div class="stat-label">Later</div></div>
</div>

<a href="schedule_add.php" class="add-btn">+ Add Schedule</a>
</div>

<!-- LIST -->
<div class="schedule-list">

<?php foreach($data as $row): ?>

<div class="schedule-item">

<div class="schedule-header">
<h4><?= htmlspecialchars($row['task_name']) ?></h4>
<span class="schedule-date"><?= $row['schedule_date'] ?></span>
</div>

<p class="schedule-description">
<b>Project:</b> <?= $row['project_name'] ?><br>
<b>Time:</b> <?= $row['start_time'] ?><br>
<b>Assigned:</b> <?= $row['assigned_to'] ?><br>
<b>Description:</b> <?= $row['description'] ?><br>
</p>

<div class="action-group">
    <a href="schedule_view.php?id=<?= $row['id'] ?>" class="action-btn view">View</a>
    <a href="schedule_edit.php?id=<?= $row['id'] ?>" class="action-btn edit">Edit</a>
    <button class="action-btn delete" onclick="deleteSchedule(<?= $row['id'] ?>)">
    Delete
    </button>
</div>

</div>

<?php endforeach; ?>

</div>

</main>
</body>

<script>
function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        window.location.href = 'schedule.php?delete_id=' + id;
    }
}
</script>

</html>