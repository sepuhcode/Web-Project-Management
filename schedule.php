<?php
require_once 'config.php';

/* ===== DELETE ===== */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("DELETE FROM schedules WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: schedule.php");
    exit;
}

/* ===== DATA ===== */
$query = "
SELECT s.*, p.name as project_name 
FROM schedules s
LEFT JOIN projects p ON s.project_id = p.id
ORDER BY s.schedule_date DESC, s.start_time DESC
";

$result = $conn->query($query);

$total = 0;
$this_week = 0;
$next_week = 0;
$later = 0;

$today = date('Y-m-d');
$week = date('Y-m-d', strtotime('+7 days'));
$next = date('Y-m-d', strtotime('+14 days'));

$data = [];

while ($row = $result->fetch_assoc()) {
    $total++;

    if ($row['schedule_date'] <= $week) $this_week++;
    elseif ($row['schedule_date'] <= $next) $next_week++;
    else $later++;

    $data[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1>Project Team Report</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="all_projects.php">Active Projects</a>
                <a href="all_projects.php?status=completed">Completed Project</a>
                <a href="schedule.php" class="active">Schedule</a>
                <a href="troubleshooting.php">Issue</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">

        <section class="edit-form">
            <div class="form-card">
                <!-- HEADER CARD (SAMA KONSEP INDEX) -->
                <div class="card">
                    <h2 style="text-align:center; margin-bottom:20px;">
                            Schedule Overview
                    </h2>
                    
                        <!-- <div class="header-top">
                            <h2>Schedule Overview</h2>
                            <a href="schedule_add.php" class="add-btn">+ Add Schedule</a>
                        </div> -->
                          
                        <div class="projects-header responsive-header">
                        
                            <div class="project-stats">
                                <div class="stat-item">
                                    <div class="stat-number"><?= $total ?></div>
                                    <div class="stat-label">Total</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number"><?= $this_week ?></div>
                                    <div class="stat-label">This Week</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number"><?= $next_week ?></div>
                                    <div class="stat-label">Next Week</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number"><?= $later ?></div>
                                    <div class="stat-label">Later</div>
                                </div>
                            </div>

                        <a href="schedule_add.php" class="btn btn-primary">
                            + Add Schedule
                        </a>
                </div>

                <!-- SCHEDULE CARD GRID -->
                <div class="schedule-list">

                    <?php foreach ($data as $row): ?>

                        <div class="schedule-card">

                            <!-- HEADER -->
                            <div class="schedule-card-header">
                                <h3><?= htmlspecialchars($row['task_name']) ?></h3>
                                <span class="schedule-badge"><?= $row['schedule_date'] ?> <?= $row['start_time'] ?></span>
                            </div>

                            <!-- BODY -->
                            <div class="schedule-card-body">

                                <div class="schedule-progress">
                                    <span><b><?= $row['project_name'] ?></b></span>
                                    
                                </div>

                                <div class="schedule-details">
                                    <p><b>Assigned:</b> <?= $row['assigned_to'] ?></p>
                                    <p><?= $row['description'] ?></p>
                                </div>

                            </div>

                            <!-- FOOTER -->
                            <div class="schedule-card-footer">
                                <a href="schedule_view.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                                <a href="schedule_edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                <button class="btn btn-delete" onclick="deleteSchedule(<?= $row['id'] ?>)">Delete</button>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>
        </section>
    </main>

    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>

    <script>
        function deleteSchedule(id) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                window.location.href = 'schedule.php?delete_id=' + id;
            }
        }
    </script>

</body>

</html>