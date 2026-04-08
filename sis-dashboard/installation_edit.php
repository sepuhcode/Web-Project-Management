<?php
require_once 'config.php';

$task_id = $_GET['id'] ?? 0;

// GET TASK
$query = "SELECT * FROM tasks WHERE id=? AND type='installation'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    echo "Task not found";
    exit;
}

// GET PROJECT
$query = "SELECT * FROM projects WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task['project_id']);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

// GET SPEC
$query = "SELECT * FROM task_specifications WHERE task_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$spec = $stmt->get_result()->fetch_assoc();

// HANDLE UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($conn, $_POST['task_name']);
    $area = sanitize($conn, $_POST['area']);
    $priority = $_POST['priority'];
    $progress = min(100, (int)$_POST['progress']);
    $status = $_POST['status'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $description = $_POST['description'];

    // UPDATE TASK
    $query = "UPDATE tasks SET 
        title=?, area=?, priority=?, progress=?, start_date=?, end_date=?, description=?, status=?
        WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssissssi",
        $title,
        $area,
        $priority,
        $progress,
        $start,
        $end,
        $description,
        $status,
        $task_id
    );
    $stmt->execute();

    // SPEC
    $cable_type = sanitize($conn, $_POST['cable_type'] ?? '');
    $cable_length = (float)($_POST['cable_length'] ?? 0);
    $connection_type = sanitize($conn, $_POST['connection_type'] ?? '');
    $cable_label = sanitize($conn, $_POST['cable_label'] ?? '');

    if ($spec) {
        $query = "UPDATE task_specifications 
                  SET cable_type=?, cable_length=?, connection_type=?, cable_label=?
                  WHERE task_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdssi",
            $cable_type,
            $cable_length,
            $connection_type,
            $cable_label,
            $task_id
        );
    } else {
        $query = "INSERT INTO task_specifications 
                  (task_id, cable_type, cable_length, connection_type, cable_label)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isdss",
            $task_id,
            $cable_type,
            $cable_length,
            $connection_type,
            $cable_label
        );
    }

    $stmt->execute();

    header("Location: installation_view.php?id=$task_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Installation Task</title>
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
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-separator">></span>
            <a href="all_projects.php">Active Projects</a>
            <span class="breadcrumb-separator">></span>
            <a href="project_details.php?id=<?= $project['id']; ?>">
                <?= htmlspecialchars($project['name']); ?>
            </a>
            <span class="breadcrumb-separator">></span>
            <a href="installation.php?project_id=<?= $project['id']; ?>">Installation</a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">Edit Task #<?= $task['id']; ?></span>
        </nav>

        <section class="edit-form">
            <div class="form-card">
                <h2>Edit Installation Task</h2>
                <form method="POST" class="task-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Task Name *</label>
                            <input type="text" name="task_name" value="<?= htmlspecialchars($task['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Area *</label>
                            <input type="text" name="area" value="<?= htmlspecialchars($task['area']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Priority *</label>
                            <select name="priority" required>
                                <option value="low" <?= $task['priority']=='low'?'selected':''; ?>>Low</option>
                                <option value="medium" <?= $task['priority']=='medium'?'selected':''; ?>>Medium</option>
                                <option value="high" <?= $task['priority']=='high'?'selected':''; ?>>High</option>
                                <option value="critical" <?= $task['priority']=='critical'?'selected':''; ?>>Critical</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Progress *</label>
                            <input type="number" name="progress" min="0" max="100" value="<?= $task['progress']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" value="<?= $task['start_date']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="10" required><?= htmlspecialchars($task['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Finish Date</label>
                            <input type="date" name="end_date" value="<?= $task['end_date']; ?>" required>
                        </div>
                    </div>

                    <!-- TECH SPEC -->
                    <div class="form-section">
                        <div class="form-grid">
                            <h3>Technical Specifications</h3>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Cable Type</label>
                                <input type="text" name="cable_type" value="<?= htmlspecialchars($spec['cable_type'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Cable Length (meters)</label>
                                <input type="number" step="0.1" name="cable_length" value="<?= $spec['cable_length'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Connection Type</label>
                                <input type="text" name="connection_type" value="<?= htmlspecialchars($spec['connection_type'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Cable Label</label>
                                <input type="text" name="cable_label" value="<?= htmlspecialchars($spec['cable_label'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Edit Task</button>
                        <button type="button" class="btn-secondary"
                            onclick="window.location.href='installation.php?project_id=<?= $project['id']; ?>'">
                            Cancel
                        </button>
                        <!-- <button type="button" class="action-btn delete"
                            onclick="confirmDelete(<?= $task['id']; ?>)">
                            Delete Task
                        </button> -->
                    </div>
                </form>
            </div>
        </section>
    </main>
    <script>
        function confirmDelete(id) {
            if (confirm('Yakin mau delete task ini?')) {
                window.location.href = 'installation_delete.php?id=' + id + '&redirect=installation.php?project_id=<?= $project['id']; ?>';
            }
        }
    </script>
</body>
</html>