<?php
require_once 'config.php';

$project_id = $_GET['id'] ?? 0;

$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "Project not found";
    exit;
}

// DELETE PROJECT
if (isset($_GET['delete_project']) && $_GET['delete_project'] == 'confirm') {
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related records from all tables in correct order
        
        // 1. Delete issue notes first (child records)
        $stmt = $conn->prepare("
            DELETE FROM issue_notes 
            WHERE issue_id IN (
                SELECT id FROM issues WHERE project_id = ?
            )
        ");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 2. Get all attachment file paths before deletion
        $stmt = $conn->prepare("
            SELECT a.file_path 
            FROM attachments a 
            WHERE a.related_type IN ('report', 'issue', 'task', 'project') 
            AND (
                (a.related_type = 'report' AND a.related_id IN (SELECT id FROM reports WHERE project_id = ?))
                OR (a.related_type = 'issue' AND a.related_id IN (SELECT id FROM issues WHERE project_id = ?))
                OR (a.related_type = 'task' AND a.related_id IN (SELECT id FROM tasks WHERE project_id = ?))
                OR (a.related_type = 'project' AND a.related_id = ?)
            )
        ");
        $stmt->bind_param("iiii", $project_id, $project_id, $project_id, $project_id);
        $stmt->execute();
        $files = $stmt->get_result();
        
        // Delete physical files
        while ($file = $files->fetch_assoc()) {
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
        }
        
        // 3. Delete attachments from database
        $stmt = $conn->prepare("
            DELETE FROM attachments 
            WHERE related_type IN ('report', 'issue', 'task', 'project') 
            AND (
                (related_type = 'report' AND related_id IN (SELECT id FROM reports WHERE project_id = ?))
                OR (related_type = 'issue' AND related_id IN (SELECT id FROM issues WHERE project_id = ?))
                OR (related_type = 'task' AND related_id IN (SELECT id FROM tasks WHERE project_id = ?))
                OR (related_type = 'project' AND related_id = ?)
            )
        ");
        $stmt->bind_param("iiii", $project_id, $project_id, $project_id, $project_id);
        $stmt->execute();
        
        // 4. Delete reports
        $stmt = $conn->prepare("DELETE FROM reports WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 5. Delete issues (troubleshooting)
        $stmt = $conn->prepare("DELETE FROM issues WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 6. Delete task specifications (child of tasks)
        $stmt = $conn->prepare("
            DELETE FROM task_specifications 
            WHERE task_id IN (
                SELECT id FROM tasks WHERE project_id = ?
            )
        ");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 7. Delete tasks (installation & programming)
        $stmt = $conn->prepare("DELETE FROM tasks WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 8. Delete schedules
        $stmt = $conn->prepare("DELETE FROM schedules WHERE project = (SELECT name FROM projects WHERE id = ?)");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // 9. Finally delete the project
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to projects list
        header("Location: all_projects.php?deleted=success");
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "Error deleting project: " . $e->getMessage();
        exit;
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = sanitize($conn, $_POST['project_name']);
    $status = $_POST['project_status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pic = $_POST['pic'];
    $progress = $_POST['project_progress'];
    $description = sanitize($conn, $_POST['project_description']);

    $query = "UPDATE projects 
              SET name=?, status=?, start_date=?, end_date=?, pic=?, progress=?, description=? 
              WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssi", $name, $status, $start_date, $end_date, $pic, $progress, $description, $project_id);
    $stmt->execute();

    header("Location: project_details.php?id=" . $project_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - SIS Dashboard</title>
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

<!-- FORM -->
<section class="edit-form">
<div class="form-card">

<h2>Edit Project Details</h2>

<form method="POST" class="project-form">

<div class="form-grid">

<div class="form-group">
    <label>Project Name</label>
    <input type="text" name="project_name" value="<?= htmlspecialchars($project['name']); ?>" required>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="project_status">
        <option value="active" <?= $project['status']=='active'?'selected':''; ?>>Active</option>
        <option value="completed" <?= $project['status']=='completed'?'selected':''; ?>>Completed</option>
        <option value="on-hold" <?= $project['status']=='on-hold'?'selected':''; ?>>On Hold</option>
    </select>
</div>

<div class="form-group">
    <label>Start Date</label>
    <input type="date" name="start_date" value="<?= $project['start_date']; ?>" required>
</div>

<div class="form-group">
    <label>End Date</label>
    <input type="date" name="end_date" value="<?= $project['end_date']; ?>" required>
</div>

<div class="form-group">
    <label>PIC</label>
    <input type="text" name="pic" min="1" value="<?= htmlspecialchars($project['pic']); ?>" required>
</div>

<div class="form-group">
    <label>Overall Progress (%)</label>
    <input type="number" name="project_progress" value="<?= $project['progress']; ?>" required>
</div>

</div>

<div class="form-group full-width">
    <label>Project Description</label>
    <textarea name="project_description" rows="4"><?= htmlspecialchars($project['description']); ?></textarea>
</div>

<!-- STAGE PROGRESS (visual only, sama seperti HTML) -->
<div class="progress-section">
    <h3>Stage Progress</h3>

    <div class="stage-progress-grid">

        <div class="form-group">
            <label>Survey (%)</label>
            <input type="number" value="100">
        </div>

        <div class="form-group">
            <label>Installation (%)</label>
            <input type="number" value="85">
        </div>

        <div class="form-group">
            <label>Programming (%)</label>
            <input type="number" value="10">
        </div>

        <div class="form-group">
            <label>Testing (%)</label>
            <input type="number" value="0">
        </div>

        <div class="form-group">
            <label>Training (%)</label>
            <input type="number" value="0">
        </div>

    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <button type="button" class="btn btn-secondary" onclick="window.location.href='project_details.php?id=<?= $project['id']; ?>'">Cancel</button>
    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete Project</button>
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
    
    function confirmDelete() {
        const projectName = '<?= htmlspecialchars($project['name']); ?>';
        const confirmMessage = `⚠️ PERINGATAN! ⚠️\n\n` +
            `Anda akan menghapus project "${projectName}" dan SEMUA data yang terkait:\n` +
            `• Semua reports dan attachment files\n` +
            `• Semua issues/troubleshooting dan notes\n` +
            `• Semua tasks (installation & programming)\n` +
            `• Semua task specifications\n` +
            `• Semua schedules\n` +
            `• Project itu sendiri\n\n` +
            `Tindakan ini TIDAK DAPAT dibatalkan!\n\n` +
            `Apakah Anda yakin ingin melanjutkan?`;
            
        if (confirm(confirmMessage)) {
            window.location.href = 'project_edit.php?id=<?= $project_id; ?>&delete_project=confirm';
        }
    }
</script>
</body>
</html>