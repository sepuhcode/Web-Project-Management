<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? 0;

// DELETE TASK
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM task_specifications WHERE task_id = $delete_id");
    $conn->query("DELETE FROM tasks WHERE id = $delete_id");
    header("Location: programming.php?project_id=$project_id");
    exit;
}

// GET PROJECT
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit;
}

// GET TASKS
$stmt = $conn->prepare("SELECT * FROM tasks WHERE project_id = ? AND type='programming' ORDER BY created_at DESC");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$tasks = $stmt->get_result();

// SUMMARY
$pending = 0;
$progress = 0;
$completed = 0;
$total_progress = 0;
$total_task = 0;

foreach ($tasks as $t) {
    $total_task++;
    $total_progress += $t['progress'];
    if ($t['status'] == 'pending') $pending++;
    elseif ($t['status'] == 'in-progress') $progress++;
    elseif ($t['status'] == 'completed') $completed++;
}

$overall = $total_task ? round($total_progress / $total_task) : 0;

// reset pointer
$tasks->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Programming - <?= htmlspecialchars($project['name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        
    </style>
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
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-separator">></span>

            <a href="all_projects.php">Active Projects</a>
            <span class="breadcrumb-separator">></span>

            <a href="project_details.php?id=<?= $project['id']; ?>">
            <?= htmlspecialchars($project['name']); ?>
            </a>
        <span class="breadcrumb-separator">></span>

        <span class="breadcrumb-current">Programming</span>
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
                <a href="programming.php?project_id=<?= $project['id']; ?>" class="tab-btn active">Programming</a>
                <a href="troubleshooting.php?project_id=<?= $project['id']; ?>" class="tab-btn">Troubleshooting</a>
                <a href="report_progress.php?project_id=<?= $project['id']; ?>" class="tab-btn">Report Progress</a>
                <a href="documentation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Documentation</a>
            </div>
        </section>

        <section class="tab-content">
            <div class="page-header">
                <h2>Programming Tasks</h2>
                <p>Software configuration and programming work items</p>
            </div>
            <div class="table-container">
                <table class="programming-table">
                    <thead>
                            <th>NO</th>
                            <th>LIST PEKERJAAN</th>
                            <th>AREA</th>
                            <th>PRIORITY</th>
                            <th>PROGRESS</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($task = $tasks->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($task['title']); ?></td>
                                <td><?= htmlspecialchars($task['area']); ?></td>
                                <td>
                                    <span class="priority-badge <?= $task['priority']; ?>">
                                        <?= ucfirst(str_replace('_',' ', $task['priority'])); ?>
                                    </span>
                                </td>
                                <td><?= $task['progress']; ?>%</td>
                                <td>
                                    <span class="status-badge <?= $task['status']; ?>">
                                        <?= ucfirst(str_replace('_',' ', $task['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn view"
                                        onclick="window.location.href='programming_view.php?id=<?= $task['id']; ?>'">
                                        View
                                    </button>
                                    <button class="action-btn edit"
                                        onclick="window.location.href='programming_edit.php?id=<?= $task['id']; ?>'">
                                        Edit
                                    </button>
                                    <button class="action-btn delete"
                                        onclick="confirmDelete(<?= $task['id']; ?>)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="progress-summary">
                <h3>Programming Progress Summary</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-number"><?= $pending; ?></div>
                        <div>Pending</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number"><?= $progress; ?></div>
                        <div>In Progress</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number"><?= $completed; ?></div>
                        <div>Completed</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number"><?= $overall; ?>%</div>
                        <div>Overall Progress</div>
                    </div>
                </div>
            </div>
            <div class="add-task-section">
                <button class="add-task-btn"
                    onclick="window.location.href='programming_add.php?project_id=<?= $project['id']; ?>'">
                    + Add New Programming Task
                </button>
            </div>
        </section>
    </main>
    <script>
        function confirmDelete(id) {
            if (confirm('Yakin mau hapus task ini?')) {
                window.location.href = 'programming.php?project_id=<?= $project['id']; ?>&delete=' + id;
            }
        }
    </script>
</body>
</html>