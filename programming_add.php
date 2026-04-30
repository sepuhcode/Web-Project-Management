<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? 0;
$project = null;
$message = '';
$message_type = '';

// Get project
$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit();
}

// HANDLE SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($conn, $_POST['task_name']);
    $description = $_POST['description'];
    $area = sanitize($conn, $_POST['area']);
    $priority = sanitize($conn, $_POST['priority'] ?? 'low');
    $status = sanitize($conn, $_POST['status'] ?? 'pending');
    $progress = (int)($_POST['progress'] ?? 0);
    $start_date = sanitize($conn, $_POST['start_date']);
    $end_date = sanitize($conn, $_POST['end_date']);

    $query = "INSERT INTO tasks (project_id, title, description, area, type, status, progress, start_date, end_date, priority) 
              VALUES (?, ?, ?, ?, 'programming', ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssisss", $project_id, $title, $description, $area, $status, $progress, $start_date, $end_date, $priority);

    if ($stmt->execute()) {
        header("Location: programming.php?project_id=$project_id");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "error";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Programming Task</title>
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
            <a href="project_details.php?id=<?= $project['id']; ?>">
                <?= htmlspecialchars($project['name']); ?>
            </a>
            <span>></span>
            <a href="programming.php?project_id=<?= $project['id']; ?>">Programming</a>
            <span>></span>
            <span>Add Task</span>
        </nav>

        <?php if ($message): ?>
            <div class="alert alert-error"><?= $message; ?></div>
        <?php endif; ?>

        <section class="edit-form">
            <div class="form-card">
                <h2>Add New Programming Task</h2>
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Task Name *</label>
                            <input type="text" name="task_name" required>
                        </div>
                        <div class="form-group">
                            <label>Area *</label>
                            <input type="text" name="area" required>
                        </div>
                        <div class="form-group">
                            <label>Priority *</label>
                            <select name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Progress (%)</label>
                            <input type="number" name="progress" min="0" max="100" value="0" required>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label>Finish Date</label>
                            <input type="date" name="end_date" required>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    <div class="form-actions" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">Add Task</button>
                        <button type="button" class="btn btn-secondary"
                            
                            onclick="window.location.href='programming.php?project_id=<?= $project['id']; ?>'">
                            Cancel
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
        document.querySelector('input[name="start_date"]').valueAsDate = new Date();
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