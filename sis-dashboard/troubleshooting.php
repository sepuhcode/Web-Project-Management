<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? null;

// ================= DELETE ISSUE =================
if (isset($_GET['delete_id'])) {

    $delete_id = intval($_GET['delete_id']);

    // HAPUS FILE ATTACHMENT
    $stmt = $conn->prepare("
        SELECT file_path FROM attachments 
        WHERE related_type='issue' AND related_id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $files = $stmt->get_result();

    while ($file = $files->fetch_assoc()) {
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
    }

    // HAPUS ATTACHMENT DB
    $stmt = $conn->prepare("
        DELETE FROM attachments 
        WHERE related_type='issue' AND related_id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // HAPUS NOTES
    $stmt = $conn->prepare("
        DELETE FROM issue_notes 
        WHERE issue_id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // HAPUS ISSUE
    $stmt = $conn->prepare("
        DELETE FROM issues WHERE id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // REDIRECT
    $redirect = "troubleshooting.php";
    if ($project_id) {
        $redirect .= "?project_id=" . $project_id;
    }

    header("Location: $redirect");
    exit;
}


// ================= PROJECT =================
$project = null;
if ($project_id) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
}

// ================= ISSUES =================
$query = "SELECT * FROM issues";

if ($project_id) {
    $query .= " WHERE project_id=?";
    $stmt = $conn->prepare($query . " ORDER BY created_at DESC");
    $stmt->bind_param("i", $project_id);
} else {
    $stmt = $conn->prepare($query . " ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();

// ================= SUMMARY =================
$pending = $progress = $completed = $total = 0;
$issues = [];

while ($row = $result->fetch_assoc()) {
    $issues[] = $row;
    $total++;

    if ($row['status'] == 'pending') $pending++;
    if ($row['status'] == 'in-progress') $progress++;
    if ($row['status'] == 'completed') $completed++;
}

$resolution = $total ? round(($completed / $total) * 100) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Troubleshooting</title>
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
                <a href="all_projects.php" >Active Projects</a>
                <a href="#">Completed Project</a>
                <a href="schedule.php">Schedule</a>
                <a href="troubleshooting.php" class="active">Issue</a>
            </nav>
        </div>
    </header>

<main class="dashboard">

<?php if ($project): ?>
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span class="breadcrumb-separator">></span>

    <a href="all_projects.php">Active Projects</a>
    <span class="breadcrumb-separator">></span>

    <a href="project_details.php?id=<?= $project['id']; ?>">
    <?= htmlspecialchars($project['name']); ?>
    </a>
<span class="breadcrumb-separator">></span>

<span class="breadcrumb-current">Troubleshooting</span>
</nav>

<!-- HEADER -->
        <section class="project-header-section">
            <div class="project-title">
                <h1><?= htmlspecialchars($project['name']); ?></h1>
                <span class="project-status-badge <?= $project['status']; ?>">
                    <?= ucfirst($project['status']); ?>
                </span>
            </div>

            <div class="project-meta">
                <div class="meta-item">
                    <span class="meta-label">Start Date:</span>
                    <span class="meta-value"><?= formatDate($project['start_date']); ?></span>
                </div>

                <div class="meta-item">
                    <span class="meta-label">End Date:</span>
                    <span class="meta-value"><?= formatDate($project['end_date']); ?></span>
                </div>

                <div class="meta-item">
                    <span class="meta-label">PIC:</span>
                    <span class="meta-value"><?= htmlspecialchars($project['pic'] ?? 'Fahmi'); ?></span>
                </div>
                <!-- PROGRESS -->
                <div class="meta-item">
                    <div class="progress-info">
                        <span class="meta-label">Progress:</span>
                        <span class="meta-label" style="visibility:hidden;">..</span>
                        <span class="progress-percent"> <?= $project['progress']; ?>%</span>
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
            </div>
        </section>

<section class="tab-navigation">
<div class="tabs">
<a href="project_details.php?id=<?= $project['id']; ?>" class="tab-btn">Overview</a>
<a href="installation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Installation</a>
<a href="programming.php?project_id=<?= $project['id']; ?>" class="tab-btn">Programming</a>
<a href="#" class="tab-btn active">Troubleshooting</a>
<a href="report_progress.php?project_id=<?= $project['id']; ?>" class="tab-btn">Report Progress</a>
<a href="documentation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Documentation</a>
</div>
</section>
<?php endif; ?>

<section class="tab-content">
<div class="tab-pane active">

<div class="page-header">
<h2>Troubleshooting Issues</h2>
<p>System issues and resolution tracking</p>
</div>

<div class="table-container">
<table class="troubleshooting-table">

<thead>
<tr>
<th>NO</th>
<th>ISSUE DESCRIPTION</th>
<th>AREA</th>
<th>PRIORITY</th>
<th>STATUS</th>
<th>REPORTED DATE</th>
<th>ACTIONS</th>
</tr>
</thead>

<tbody>

<?php $no=1; foreach ($issues as $issue): ?>

<tr>
<td><?= $no++; ?></td>

<td><?= htmlspecialchars($issue['issue_type']); ?></td>

<td><?= htmlspecialchars($issue['area']); ?></td>

<td>
<span class="priority-badge <?= $issue['priority']; ?>">
<?= ucfirst($issue['priority']); ?>
</span>
</td>

<td>
<span class="status-badge <?= $issue['status']; ?>">
<?= ucfirst($issue['status']); ?>
</span>
</td>

<td><?= formatDate($issue['reported_date']); ?></td>

<td>
<button class="action-btn view"
onclick="location.href='troubleshooting_view.php?id=<?= $issue['id']; ?>'">
View
</button>

<button class="action-btn edit"
onclick="location.href='troubleshooting_edit.php?id=<?= $issue['id']; ?>'">
Edit
</button>

<button class="action-btn delete"
onclick="confirmDelete(<?= $issue['id']; ?>)">
Delete
</button>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>
</div>

<div class="progress-summary">
<h3>Issue Resolution Summary</h3>

<div class="summary-grid">

<div class="summary-item">
<div class="summary-number"><?= $pending; ?></div>
<div class="summary-label">Pending</div>
</div>

<div class="summary-item">
<div class="summary-number"><?= $progress; ?></div>
<div class="summary-label">In Progress</div>
</div>

<div class="summary-item">
<div class="summary-number"><?= $completed; ?></div>
<div class="summary-label">Completed</div>
</div>

<div class="summary-item">
<div class="summary-number"><?= $resolution; ?>%</div>
<div class="summary-label">Resolution Rate</div>
</div>

</div>
</div>

<div class="add-task-section">
<button class="add-task-btn"
onclick="location.href='troubleshooting_add.php<?= $project_id ? '?project_id='.$project_id : '' ?>'">
+ Report New Issue
</button>
</div>

</div>
</section>

</main>

<script>
function confirmDelete(id){
    if(confirm("Delete this issue?")){

        let url = "troubleshooting.php?delete_id=" + id;

        const params = new URLSearchParams(window.location.search);
        if (params.get("project_id")) {
            url += "&project_id=" + params.get("project_id");
        }

        window.location.href = url;
    }
}
</script>

</body>
</html>