<?php
require_once 'config.php';

$status_filter = $_GET['status'] ?? 'active';

// QUERY PROJECT
$query = "SELECT * FROM projects";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '$status_filter'";
}
$query .= " ORDER BY created_at DESC";

$projects_result = $conn->query($query);

// STATS
$active_count = 0;
$completed_count = 0;

$stats_query = "SELECT status, COUNT(*) as count FROM projects GROUP BY status";
$stats_result = $conn->query($stats_query);

while ($row = $stats_result->fetch_assoc()) {
    if ($row['status'] == 'active') $active_count = $row['count'];
    if ($row['status'] == 'completed') $completed_count = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Projects - SIS Dashboard</title>
<link rel="stylesheet" href="styles.css">

<style>
.project-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.project-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.project-actions {
    margin-top: auto;
    display: flex;
    gap: 10px;
}

.project-actions a {
    flex: 1;
    text-align: center;
}
</style>

</head>

<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
$status = $_GET['status'] ?? '';
?>

<header class="navbar">
    <div class="nav-container">
        <div class="logo">
            <h1>Project Team Report</h1>
        </div>

        <nav class="nav-links">

            <a href="index.php" 
               class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
               Home
            </a>

            <a href="all_projects.php" 
               class="<?= ($current_page == 'all_projects.php' && $status != 'completed') ? 'active' : '' ?>">
               Active Projects
            </a>

            <a href="all_projects.php?status=completed" 
               class="<?= ($current_page == 'all_projects.php' && $status == 'completed') ? 'active' : '' ?>">
               Completed Project
            </a>

            <a href="schedule.php" 
               class="<?= $current_page == 'schedule.php' ? 'active' : '' ?>">
               Schedule
            </a>

            <a href="troubleshooting.php" 
               class="<?= $current_page == 'troubleshooting.php' ? 'active' : '' ?>">
               Issue
            </a>

        </nav>
    </div>
</header>

<main class="dashboard">

<section class="projects-header">
<h1><?= ($status == 'completed') ? 'Completed Projects' : 'Active Projects'; ?></h1>

<div class="project-stats">

<div class="stat-item">
    <span class="stat-number"><?= $active_count; ?></span>
    <span class="stat-label">Active</span>
</div>

<div class="stat-item">
    <span class="stat-number"><?= $completed_count; ?></span>
    <span class="stat-label">Completed</span>
</div>

<div class="stat-item add-project" onclick="window.location.href='project_add.php'" style="cursor: pointer;">
    <span class="stat-number" style="color:#28a745;">+</span>
    <span class="stat-label">Add Project</span>
</div>

</div>
</section>

<section class="projects-grid">

<?php if ($projects_result->num_rows > 0): ?>
<?php while ($project = $projects_result->fetch_assoc()): ?>

<div class="project-card">

    <div class="project-card-body">

        <!-- HEADER -->
        <div class="project-header">
            <h3><?= htmlspecialchars($project['name']); ?></h3>
            <span class="project-status <?= $project['status']; ?>">
                <?= ucfirst($project['status']); ?>
            </span>
        </div>

        <!-- PROGRESS -->
        <div class="project-progress">
            <div class="progress-info">
                <span>Progress</span>
                <span class="progress-percent"><?= $project['progress']; ?>%</span>
            </div>

            <div class="progress-bar">
                <?php
                $color = 'red';
                if ($project['progress'] >= 80) $color = 'green';
                elseif ($project['progress'] >= 50) $color = 'orange';
                elseif ($project['progress'] >= 30) $color = 'blue';
                ?>
                <div class="progress-fill <?= $color; ?>" style="width: <?= $project['progress']; ?>%"></div>
            </div>
        </div>

        <!-- DETAILS -->
        <div class="project-details">
            <div class="detail-item">
                <span class="detail-label">Start Date:</span>
                <span class="detail-value"><?= formatDate($project['start_date']); ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">End Date:</span>
                <span class="detail-value"><?= formatDate($project['end_date']); ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">PIC:</span>
                <span class="detail-value"><?= htmlspecialchars($project['pic'] ?? 'Fahmi'); ?></span>
            </div>
        </div>

    </div>

    <!-- ACTION -->
    <div class="project-actions">
        <a href="project_details.php?id=<?= $project['id']; ?>" class="btn btn-primary">View Details</a>
        <a href="project_edit.php?id=<?= $project['id']; ?>" class="btn btn-secondary">Edit</a>
    </div>

</div>

<?php endwhile; ?>
<?php else: ?>

<p style="text-align:center; width:100%;">No projects found</p>

<?php endif; ?>

</section>

</main>

<!-- 🔥 FOOTER -->
<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

</body>
</html>