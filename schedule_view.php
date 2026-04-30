<?php
require_once 'config.php';

$id = $_GET['id'];

$q = $conn->prepare("
    SELECT s.*, p.name AS project_name
    FROM schedules s
    LEFT JOIN projects p ON s.project = p.name
    WHERE s.id = ?
");

$q->bind_param("i", $id);
$q->execute();
$data = $q->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }
        
        .priority-high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .priority-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .priority-low {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
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

    <section class="task-view">

        <div class="view-header">
            <div class="task-title-section">
                <h1><?= $data['task_name'] ?></h1>
            </div>
        </div>

        <div class="view-content">

            <div class="info-grid">

                <div class="info-card">
                    <h3>Schedule Info</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span>Project:</span>
                            <span><?= $data['project_name'] ?></span>
                        </div>
                        <div class="info-item">
                            <span>Schedule Date:</span>
                            <span><?= $data['schedule_date'] ?></span>
                        </div>
                        <div class="info-item">
                            <span>Priority:</span>
                            <span class="priority-badge <?= $data['priority'] ?>"><?= ucfirst($data['priority']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Timeline</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span>Start Time</span>
                            <span><?= $data['start_time'] ?></span>
                        </div>
                        <div class="info-item">
                            <span>Tim</span>
                            <span><?= $data['assigned_to'] ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Description</h3>
                    <div class="form-group">
                        <textarea rows="10" readonly><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                </div>

            </div>

        </div>

        <div class="view-actions">
            <a href="schedule.php" class="btn btn-secondary">Back</a>
            <a href="schedule_edit.php?id=<?= $id ?>" class="btn btn-primary">Edit</a>
        </div>

    </section>

</main>

<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

</body>
</html>