<?php
require_once 'config.php';

/* ===============================
   HANDLE SUBMIT
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $task_name     = sanitize($conn, $_POST['task_name']);
    $schedule_date = $_POST['schedule_date'];
    $start_time    = $_POST['start_time'];
    $priority      = $_POST['priority'] ?? 'medium';
    $assigned_to   = sanitize($conn, $_POST['assigned_to']);
    $description   = $_POST['description'];
    $project       = $_POST['project'];


    /* ===============================
    INSERT SCHEDULE
    =============================== */
    $stmt = $conn->prepare("
        INSERT INTO schedules 
        (project, task_name, schedule_date, start_time, priority, assigned_to, description)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssss",
        $project,
        $task_name,
        $schedule_date,
        $start_time,
        $priority,
        $assigned_to,
        $description
    );

    if ($stmt->execute()) {
        header("Location: schedule.php");
        exit;
    } else {
        echo "Gagal menyimpan data ke database! Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="all_projects.php?status=completed">Completed Projects</a>
            <a href="schedule.php" class="active">Schedule</a>
            <a href="troubleshooting.php">Issue</a>
        </nav>
    </div>
</header>

<main class="dashboard">

    <section class="edit-form">
        <div class="form-card">

            <h2>Add New Schedule Item</h2>

            <form method="POST" class="project-form">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Task Name</label>
                        <input type="text" name="task_name" required>
                    </div>

                    <div class="form-group">
                        <label>Project Name</label>
                        <input type="text" name="project" required>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="schedule_date" required>
                    </div>

                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="start_time">
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
                        <label>Tim Yang Bertugas</label>
                        <input type="text" name="assigned_to">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="10" required></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <button class="btn btn-primary">Add Schedule</button>
                    <a href="schedule.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>

        </div>
    </section>

</main>

<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

<script>
    function toggleCustomProject() {
        const select = document.getElementById('projectSelect');
        const customField = document.getElementById('customProjectField');

        if (select.value === 'custom') {
            customField.style.display = 'block';
        } else {
            customField.style.display = 'none';
        }
    }
</script>

</body>
</html>