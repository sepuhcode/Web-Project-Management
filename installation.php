<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? 0;

// GET PROJECT
$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit;
}

// GET TASKS (IMPORTANT FIX: handle NULL / kosong status)
$query = "SELECT t.*, ts.cable_label FROM tasks t
          LEFT JOIN task_specifications ts ON t.id = ts.task_id
          WHERE t.project_id=? AND t.type='installation'
          ORDER BY t.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

// INIT
$pending = 0;
$progress = 0;
$completed = 0;
$total_progress = 0;
$count = 0;

$tasks = [];

// LOOP DATA
while ($row = $result->fetch_assoc()) {
    // FIX: default status kalau kosong
    if (empty($row['status'])) {
        $row['status'] = 'pending';
    }

    $tasks[] = $row;

    $count++;
    $total_progress += (int)$row['progress'];

    if ($row['status'] == 'pending') $pending++;
    elseif ($row['status'] == 'in-progress') $progress++;
    elseif ($row['status'] == 'completed') $completed++;
}

// HITUNG OVERALL
$overall = $count ? round($total_progress / $count, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - <?= htmlspecialchars($project['name']); ?></title>
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

            <a href="project_details.php?id=<?= $project['id']; ?>">
            <?= htmlspecialchars($project['name']); ?>
            </a>
        <span class="breadcrumb-separator">></span>

        <span class="breadcrumb-current">Installation   </span>
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

        <!-- TAB -->
        <section class="table-container">
            <div class="tabs">
                <a href="project_details.php?id=<?= $project['id']; ?>" class="tab-btn">Overview</a>
                <a href="#" class="tab-btn active">Installation</a>
                <a href="programming.php?project_id=<?= $project['id']; ?>" class="tab-btn">Programming</a>
                <a href="troubleshooting.php?project_id=<?= $project['id']; ?>" class="tab-btn">Troubleshooting</a>
                <a href="report_progress.php?project_id=<?= $project['id']; ?>" class="tab-btn">Report Progress</a>
                <a href="documentation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Documentation</a>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="tab-content">
            <div class="tab-pane active">
                <div class="page-header">
                    <h2>Installation Tasks</h2>
                    <p>Detailed installation work items and progress tracking</p>
                </div>

                <!-- TABLE -->
                <div class="table-container">
                    <table class="installation-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>LIST PEKERJAAN</th>
                                <th>AREA</th>
                                <th>PRIORITY</th>
                                <th>PROGRESS</th>
                                <th>STATUS</th>
                                <th>LABEL KABEL</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($tasks) > 0): ?>
                                <?php $no = 1; foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <a href="installation_view.php?id=<?= $task['id']; ?>">
                                                <?= htmlspecialchars($task['title']); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($task['area'] ?? '-'); ?></td>
                                        <td>
                                            <span class="priority-badge <?= $task['priority']; ?>">
                                                <?= ucfirst($task['priority'] ?? 'low'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="background:#eee; width:100%; height:8px; border-radius:5px;">
                                                <div style="width:<?= $task['progress']; ?>%; background:#4caf50; height:100%; border-radius:5px;"></div>
                                            </div>
                                            <?= $task['progress']; ?>%
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $task['status']; ?>">
                                                <?= ucfirst($task['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($task['cable_label'] ?? '-'); ?></td>
                                        <td>
                                            <button class="action-btn view" onclick="location.href='installation_view.php?id=<?= $task['id']; ?>'">View</button>
                                            <button class="action-btn edit" onclick="location.href='installation_edit.php?id=<?= $task['id']; ?>'">Edit</button>
                                            <button class="action-btn delete"
                                                onclick="confirmDelete(<?= $task['id']; ?>)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align:center;">No installation data found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- SUMMARY -->
                <div class="progress-summary">
                    <h3>Installation Progress Summary</h3>
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
                            <div class="summary-number"><?= $overall; ?>%</div>
                            <div class="summary-label">Overall Progress</div>
                        </div>
                    </div>
                </div>

                <!-- ADD -->
                <div class="add-task-section">
                    <button class="add-task-btn"
                    onclick="window.location.href='installation_add.php?project_id=<?= $project['id']; ?>'">
                    + Add New Installation Task
                    </button>
                </div>
            </div>
        </section>
        <script>
            function confirmDelete(id) {
                if (confirm('Yakin mau delete task ini?')) {
                    window.location.href = 'installation_delete.php?id=' + id + '&redirect=installation.php?project_id=<?= $project['id']; ?>';
                }
            }
        </script>
    </main>

    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>

</body>
</html>