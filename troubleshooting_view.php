<?php
require_once 'config.php';

$issue_id = $_GET['id'] ?? 0;

// ===== GET ISSUE =====
$stmt = $conn->prepare("
SELECT i.*, p.name as project_name, p.id as project_id 
FROM issues i 
LEFT JOIN projects p ON i.project_id = p.id 
WHERE i.id=?
");
$stmt->bind_param("i", $issue_id);
$stmt->execute();
$issue = $stmt->get_result()->fetch_assoc();

if (!$issue) {
    header("Location: troubleshooting.php");
    exit;
}

// ===== ADD NOTE =====
if (isset($_POST['add_note'])) {

    $content = trim($_POST['note_content']);
    $author = trim($_POST['author']);;
    if (!empty($content)) {

        $stmt = $conn->prepare("
            INSERT INTO issue_notes (issue_id, content, author, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->bind_param("iss",
            $issue_id,
            $content,
            $author
        );

        $stmt->execute();
    }

    header("Location: troubleshooting_view.php?id=" . $issue_id);
    exit;
}

// ===== DELETE NOTE =====
if (isset($_GET['delete_note'])) {
    $note_id = (int)$_GET['delete_note'];
    
    // Verify note belongs to this issue
    $stmt = $conn->prepare("SELECT issue_id FROM issue_notes WHERE id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $note = $stmt->get_result()->fetch_assoc();
    
    if ($note && $note['issue_id'] == $issue_id) {
        $stmt = $conn->prepare("DELETE FROM issue_notes WHERE id = ?");
        $stmt->bind_param("i", $note_id);
        $stmt->execute();
    }
    
    header("Location: troubleshooting_view.php?id=" . $issue_id);
    exit;
}

// ===== DELETE TASK =====
if (isset($_GET['delete_task'])) {
    // Delete related notes first
    $stmt = $conn->prepare("DELETE FROM issue_notes WHERE issue_id = ?");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    
    // Delete related attachments
    $stmt = $conn->prepare("DELETE FROM attachments WHERE related_type='issue' AND related_id = ?");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    
    // Delete the issue
    $stmt = $conn->prepare("DELETE FROM issues WHERE id = ?");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    
    header("Location: troubleshooting.php?project_id=" . $issue['project_id']);
    exit;
}

// ===== GET ATTACHMENTS =====
$stmt = $conn->prepare("
SELECT * FROM attachments 
WHERE related_type='issue' AND related_id=? 
ORDER BY uploaded_at DESC
");
$stmt->bind_param("i", $issue_id);
$stmt->execute();
$attachments = $stmt->get_result();

// ===== GET NOTES =====
$stmt = $conn->prepare("
SELECT * FROM issue_notes 
WHERE issue_id=? 
ORDER BY created_at ASC
");
$stmt->bind_param("i", $issue_id);
$stmt->execute();
$notes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Troubleshooting Task - SIS Dashboard</title>
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
                <a href="index.php">Home</a>
                <a href="all_projects.php">Active Projects</a>
                <a href="#">Completed Project</a>
                <a href="schedule.php">Schedule</a>
                <a href="troubleshooting.php" class="active">Issue</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-separator">></span>
            <a href="all_projects.php">Active Projects</a>
            <span class="breadcrumb-separator">></span>
            <a href="project_details.php?id=<?= $issue['project_id']; ?>"><?= htmlspecialchars($issue['project_name']); ?></a>
            <span class="breadcrumb-separator">></span>
            <a href="troubleshooting.php?project_id=<?= $issue['project_id']; ?>">Troubleshooting</a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">Task #<?= str_pad($issue['id'], 3, '0', STR_PAD_LEFT); ?> - <?= htmlspecialchars($issue['issue_type']); ?></span>
        </nav>

        <!-- Task Details -->
        <section class="task-detail-section">
            <div class="task-header">
                <div class="task-title">
                    <h1>Troubleshooting Task Details</h1>
                    <span class="task-id">Task #<?= str_pad($issue['id'], 3, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="task-status">
                    <span class="status-badge <?= $issue['status']; ?>">
                        <?= ucfirst($issue['status']); ?>
                    </span>
                </div>
            </div>

            <div class="task-content">
                <div class="task-info-grid">
                    <div class="info-card">
                        <h3>Task Information</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Issue Type:</span>
                                <span class="info-value"><?= htmlspecialchars($issue['issue_type']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Area:</span>
                                <span class="info-value"><?= htmlspecialchars($issue['area']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Priority:</span>
                                <span class="info-value"><?= getPriorityBadge($issue['priority']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Status:</span>
                                <span class="info-value"><?= ucfirst($issue['status']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Timeline</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Reported Date:</span>
                                <span class="info-value"><?= formatDate($issue['reported_date']); ?></span>
                            </div>
                                                        
                        </div>
                    </div>
                </div>

                <div class="info-card full-width">
                    <h3>Issue Description</h3>
                    <div class="task-description">
                        <p><?= nl2br(htmlspecialchars($issue['description'])); ?></p>
                    </div>
                </div>
                
                <div class="info-card full-width">
                    <h3>Resolution Steps</h3>
                        <div class="task-description">
                                <h4>Resolution Applied</h4>
                                <p><?= nl2br(htmlspecialchars($issue['resolution'])); ?></p>
                        </div>
                </div>

                <div class="info-card full-width">
                    <h3>Diagnostic Information</h3>
                    <div class="diagnostic-grid">
                        <div class="diag-item">
                            <span class="diag-label">Error Code:</span>
                            <span class="diag-value"><?= $issue['error_code'] ?: 'N/A'; ?></span>
                        </div>
                        <div class="diag-item">
                            <span class="diag-label">System Logs:</span>
                            <span class="diag-value"><?= $issue['system_logs'] ?: 'N/A'; ?></span>
                        </div>
                        <div class="diag-item">
                            <span class="diag-label">Network Status:</span>
                            <span class="diag-value"><?= $issue['network_status'] ?: 'N/A'; ?></span>
                        </div>
                        <div class="diag-item">
                            <span class="diag-label">Root Cause:</span>
                            <span class="diag-value"><?= $issue['root_cause'] ?: 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="info-card full-width">
                    <h3>Attachments</h3>
                    <div class="attachment-list">
                        <?php while($file = $attachments->fetch_assoc()): ?>
                        <div class="attachment-item">
                            <div class="attachment-icon">📎</div>
                            <div class="attachment-info">
                                <h4><?= htmlspecialchars($file['file_name']); ?></h4>
                                <p><?= $file['file_size']; ?> • Uploaded <?= formatDate($file['uploaded_at']); ?></p>
                            </div>
                            <a href="<?= $file['file_path']; ?>" class="action-btn download" download>Download</a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="info-card full-width">
                    <h3>Notes & Comments</h3>
                    <div class="notes-section">
                        <?php while($note = $notes->fetch_assoc()): ?>
                        <div class="note-item">
                            <div class="note-header">
                                <span class="note-author"><?= htmlspecialchars($note['author']); ?></span>
                                <span class="note-date"><?= formatDateTime($note['created_at']); ?></span>
                                <a href="?id=<?= $issue_id; ?>&delete_note=<?= $note['id']; ?>" 
                                   class="action-btn delete" 
                                   onclick="return confirm('Are you sure you want to delete this note?');"
                                   style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    Delete
                                </a>
                            </div>
                            <p class="note-content"><?= htmlspecialchars($note['content']); ?></p>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- ADD NOTE -->
                    <div class="add-note">
                        <form method="POST">
                            <textarea name="author" placeholder="Enter Name..." required></textarea>
                            <textarea name="note_content" placeholder="Add a note..." required></textarea>
                            <button type="submit" name="add_note" class=" btn btn-primary">
                                Add Note
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="task-actions">
                <a href="troubleshooting_edit.php?id=<?= $issue['id']; ?>" class="btn btn-primary">Edit Task</a>
                <a href="troubleshooting.php?project_id=<?= $issue['project_id']; ?>" class="btn btn-secondary">Back to List</a>
                
            </div>
        </section>
    </main>
    <!-- 🔥 FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> Project Team Report</p>
    </footer>
</body>
</html>