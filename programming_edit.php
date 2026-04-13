<?php
require_once 'config.php';

$task_id = $_GET['id'] ?? 0;

// GET TASK
$query = "SELECT t.*, p.name as project_name, p.id as project_id 
          FROM tasks t 
          JOIN projects p ON t.project_id = p.id 
          WHERE t.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task || $task['type'] !== 'programming') {
    echo "Task not found";
    exit();
}

$project_id = $task['project_id'];

// HANDLE UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($conn, $_POST['task_name']);
    $description = $_POST['description'];
    $area        = sanitize($conn, $_POST['area']); // TAMBAHAN
    $priority    = sanitize($conn, $_POST['priority']);
    $status      = sanitize($conn, $_POST['status']);
    $progress    = (int)$_POST['progress'];
    $start_date  = sanitize($conn, $_POST['start_date']);
    $end_date    = sanitize($conn, $_POST['end_date']);

    $update = "UPDATE tasks 
               SET title = ?, description = ?, area = ?, priority = ?, status = ?, progress = ?, start_date = ?, end_date = ? 
               WHERE id = ?";

    $stmt = $conn->prepare($update);
    $stmt->bind_param(
        "sssssissi",
        $title,
        $description,
        $area,
        $priority,
        $status,
        $progress,
        $start_date,
        $end_date,
        $task_id
    );

    if ($stmt->execute()) {
        header("Location: programming.php?project_id=" . $project_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Programming Task</title>
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
            <span>></span>
            <a href="all_projects.php">Active Projects</a>
            <span>></span>
            <a href="project_details.php?id=<?= $project_id; ?>">
                <?= htmlspecialchars($task['project_name']); ?>
            </a>
            <span>></span>
            <a href="programming.php?project_id=<?= $project_id; ?>">Programming</a>
            <span>></span>
            <span>Edit Task</span>
        </nav>

        <section class="edit-form">
            <div class="form-card">
                <h2>Edit Programming Task</h2>
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Task Name *</label>
                            <input type="text" name="task_name" value="<?= htmlspecialchars($task['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Area *</label>
                            <input type="text" name="area" value="<?= htmlspecialchars($task['area'] ?? ''); ?>" required>
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
                            <label>Progress (%)</label>
                            <input type="number" name="progress" min="0" max="100" value="<?= $task['progress']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="pending" <?= ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="in-progress" <?= ($task['status'] == 'in-progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?= ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" value="<?= $task['start_date']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Finish Date</label>
                            <input type="date" name="end_date" value="<?= $task['end_date']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($task['description']); ?></textarea>
                    </div>

                    <div class="form-actions" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">Save Edit</button>
                        <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='programming.php?project_id=<?= $project_id; ?>'">
                            Cancel
                        </button>
                        <button class="btn btn-danger" onclick="confirmDelete(<?= $task['id']; ?>)">
                            Delete Task
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>

    <script>
        document.querySelector('input[name="end_date"]').addEventListener('change', function() {
            const start = document.querySelector('input[name="start_date"]').value;
            if (start && this.value < start) {
                alert('Finish date tidak boleh lebih kecil dari start date');
                this.value = '';
            }
        });
    </script>
</body>
</html>