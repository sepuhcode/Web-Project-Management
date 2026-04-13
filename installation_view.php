<?php
require_once 'config.php';

$task_id = $_GET['id'] ?? 0;

// GET TASK + PROJECT
$query = "SELECT t.*, p.name as project_name, p.id as project_id 
          FROM tasks t 
          JOIN projects p ON t.project_id = p.id 
          WHERE t.id = ? AND t.type='installation'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    echo "Task not found";
    exit;
}

// DEFAULT VALUE
$status = $task['status'] ?: 'pending';
$priority = $task['priority'] ?: 'medium';

// GET SPEC
$query_spec = "SELECT * FROM task_specifications WHERE task_id=?";
$stmt_spec = $conn->prepare($query_spec);
$stmt_spec->bind_param("i", $task_id);
$stmt_spec->execute();
$spec = $stmt_spec->get_result()->fetch_assoc();

// HITUNG DURASI
$start = strtotime($task['start_date']);
$end = strtotime($task['end_date']);
$days = ($start && $end) ? ($end - $start) / 86400 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Installation Task - SIS Dashboard</title>
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
        <section class="task-detail-section">
            <div class="task-header">
                <div class="task-title">
                    <h1>Installation Task Details</h1>
                    <span class="task-id">Task #<?= str_pad($task['id'],3,'0',STR_PAD_LEFT); ?></span>
                </div>
                <div class="task-status">
                    <span class="status-badge <?= $status; ?>">
                        <?= ucfirst($status); ?>
                    </span>
                </div>
            </div>

            <div class="task-content">
                <div class="task-info-grid">
                    <!-- LEFT -->
                    <div class="info-card">
                        <h3>Task Information</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Task Name:</span>
                                <span class="info-value"><?= htmlspecialchars($task['title']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Area:</span>
                                <span class="info-value"><?= htmlspecialchars($task['area'] ?? '-'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Priority:</span>
                                <span class="info-value">
                                    <span class="priority-badge <?= $priority; ?>">
                                        <?= ucfirst($priority); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Progress:</span>
                                <span class="info-value"><?= $task['progress']; ?>%</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Status:</span>
                                <span class="info-value"><?= ucfirst($status); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="info-card">
                        <h3>Timeline</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Start Date:</span>
                                <span class="info-value"><?= formatDate($task['start_date']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Finish Date:</span>
                                <span class="info-value"><?= formatDate($task['end_date']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Duration:</span>
                                <span class="info-value"><?= $days; ?> days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div class="info-card full-width">
                    <h3>Task Description</h3>
                    <div class="task-description">
                        <textarea name="description" rows="10" readonly><?= htmlspecialchars($task['description']); ?></textarea>
                    </div>
                </div>

                <!-- SPEC -->
                <div class="info-card full-width">
                    <h3>Technical Specifications</h3>
                    <div class="spec-grid">
                        <div class="spec-item">
                            <span class="spec-label">Cable Type:</span>
                            <span class="spec-value"><?= $spec['cable_type'] ?? '-'; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Cable Length:</span>
                            <span class="spec-value"><?= $spec['cable_length'] ?? '-'; ?> m</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Connection Type:</span>
                            <span class="spec-value"><?= $spec['connection_type'] ?? '-'; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Cable Label:</span>
                            <span class="spec-value"><?= $spec['cable_label'] ?? '-'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION -->
            <div class="task-actions">
                <a href="installation_edit.php?id=<?= $task['id']; ?>" class="btn btn-primary">Edit Task</a>
                <a href="installation.php?project_id=<?= $task['project_id']; ?>" class="btn btn-secondary">
                    Back to List
                </a>
                
            </div>
        </section>
    </main>

    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>

    <script>
        function confirmDelete(id) {
            if (confirm('Yakin mau hapus task ini?')) {
                window.location.href = 'installation_delete.php?id=' + id + '&redirect=installation.php?project_id=<?= $task['project_id']; ?>';
            }
        }
    </script>
</body>
</html>