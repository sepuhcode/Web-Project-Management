<?php
require_once 'config.php';

// Get statistics
$active_projects = 0;
$completed_projects = 0;
$total_issues = 0;

$projects_query = "SELECT status, COUNT(*) as count FROM projects GROUP BY status";
$projects_result = $conn->query($projects_query);
while ($row = $projects_result->fetch_assoc()) {
    if ($row['status'] == 'active') $active_projects = $row['count'];
    if ($row['status'] == 'completed') $completed_projects = $row['count'];
}

$issues_query = "SELECT COUNT(*) as count FROM issues";
$issues_result = $conn->query($issues_query);
$total_issues = $issues_result->fetch_assoc()['count'];

// Get recent projects for progress overview
$projects_query = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 5";
$projects_result = $conn->query($projects_query);

// Get recent issues
$issues_query = "SELECT * FROM issues ORDER BY created_at DESC LIMIT 3";
$issues_result = $conn->query($issues_query);

// Get upcoming schedules (tasks)
$schedule_query = "SELECT * FROM tasks WHERE status != 'completed' ORDER BY start_date ASC LIMIT 3";
$schedule_result = $conn->query($schedule_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Dashboard</title>
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
     <header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1>Project Team Report</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="all_projects.php" >Active Projects</a>
                <a href="all_projects.php?status=completed">Completed Project</a>
                <a href="schedule.php">Schedule</a>
                <a href="troubleshooting.php">Issue</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">
        <section class="stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="#4A90E2" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="#4A90E2" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Active Projects</h3>
                    <p class="stat-number"><?php echo $active_projects; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="#4CAF50" stroke-width="2"/>
                        <path d="M9 12l2 2 4-4" stroke="#4CAF50" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Completed Projects</h3>
                    <p class="stat-number"><?php echo $completed_projects; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#FF9800" stroke-width="2"/>
                        <path d="M14 2v6h6" stroke="#FF9800" stroke-width="2"/>
                        <path d="M8 13h8M8 17h6" stroke="#FF9800" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Issues Reported</h3>
                    <p class="stat-number"><?php echo $total_issues; ?></p>
                </div>
            </div>
            
            <div class="stat-card" onclick="window.location.href='project_add.php'" style="cursor: pointer;">
                <span class="stat-number" style="color: #28a745;">+</span>
                <span class="stat-label">Add Project</span>
            </div>
        </section>

        <section class="cards">
            <div class="card">
                <h2>Progress Overview</h2>
                <div class="progress-list">
                    <?php while ($project = $projects_result->fetch_assoc()): ?>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span><?php echo htmlspecialchars($project['name']); ?></span>
                            <span><?php echo $project['progress']; ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <?php
                            $color = 'red';
                            if ($project['progress'] >= 80) $color = 'green';
                            elseif ($project['progress'] >= 50) $color = 'orange';
                            ?>
                            <div class="progress-fill <?php echo $color; ?>" style="width: <?php echo $project['progress']; ?>%"></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <a href="all_projects.php" class="card-btn blue">View All Project ></a>
            </div>

            <div class="card">
                <h2>Recent Issues</h2>
                <div class="issues-list">
                    <?php while ($issue = $issues_result->fetch_assoc()): ?>
                    <div class="issue-item">
                        <div class="issue-content">
                            <h4><?php echo htmlspecialchars($issue['issue_type']); ?></h4>
                            <p>Reported: <?php echo formatDate($issue['reported_date']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <a href="troubleshooting.php" class="card-btn blue">View All Issue ></a>
            </div>

            <!-- Schedule List -->
            <div class="card">
                <h2>Project Schedule</h2>
                <div class="schedule-list">
                    <?php while ($schedule = $schedule_result->fetch_assoc()): ?>
                    <div class="schedule-item">
                        <div class="schedule-header">
                            <h4><?php echo htmlspecialchars($schedule['title']); ?></h4>
                            <span class="schedule-date"><?php echo formatDate($schedule['start_date']); ?></span>
                        </div>
                        <p class="schedule-description"><?php echo htmlspecialchars($schedule['description']); ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
                <a href="schedule.php" class="card-btn blue">View All Schedule ></a>
            </div>
        </section>
    </main>
    
    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>

</body>
</html>
