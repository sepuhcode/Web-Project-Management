<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;

// Ambil data existing
$q = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
$q->bind_param("i", $id);
$q->execute();
$data = $q->get_result()->fetch_assoc();

// Validasi
if (!$data) {
    die("Data tidak ditemukan");
}

// ================= UPDATE =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $project_id = $_POST['project_id'];

    // 🔥 HANDLE CUSTOM PROJECT
    if ($project_id === 'custom') {

        $custom_name = trim($_POST['custom_project']);

        if (empty($custom_name)) {
            die("Custom project tidak boleh kosong");
        }

        // 🔎 cek apakah sudah ada
        $check = $conn->prepare("SELECT id FROM projects WHERE name = ?");
        $check->bind_param("s", $custom_name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $project_id = $result->fetch_assoc()['id'];
        } else {
            $insert = $conn->prepare("INSERT INTO projects (name) VALUES (?)");
            $insert->bind_param("s", $custom_name);
            $insert->execute();
            $project_id = $insert->insert_id;
        }
    }

    // 🔥 UPDATE SCHEDULE
    $stmt = $conn->prepare("
        UPDATE schedules 
        SET task_name = ?, project_id = ?, schedule_date = ?, start_time = ?, assigned_to = ?, description = ?, priority = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sisssssi",
        $_POST['task_name'],
        $project_id,
        $_POST['schedule_date'],
        $_POST['start_time'],
        $_POST['assigned_to'],
        $_POST['description'],
        $_POST['priority'],
        $id
    );

    $stmt->execute();

    header("Location: schedule.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
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

            <h2>Edit Schedule Item</h2>

            <form method="POST" class="task-form">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Task Name</label>
                        <input type="text" name="task_name"
                               value="<?= htmlspecialchars($data['task_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Project</label>

                        <select name="project_id" id="projectSelect" onchange="toggleCustomProject()" required>
                            <option value="">-- Select Project --</option>

                            <?php
                            $p = $conn->query("SELECT * FROM projects");
                            while ($pr = $p->fetch_assoc()) {
                                $selected = ($pr['id'] == $data['project_id']) ? "selected" : "";
                                echo "<option value='{$pr['id']}' $selected>{$pr['name']}</option>";
                            }
                            ?>

                            <option value="custom">+ Custom Project</option>
                        </select>
                    </div>

                    <div class="form-group" id="customProjectField" style="display: none;">
                        <label>Custom Project Name</label>
                        <input type="text" name="custom_project" placeholder="Enter project name">
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="schedule_date"
                               value="<?= $data['schedule_date'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority">
                            <option value="low" <?= $data['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= $data['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= $data['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tim Yang Bertugas</label>
                        <input type="text" name="assigned_to"
                               value="<?= htmlspecialchars($data['assigned_to']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="start_time"
                               value="<?= $data['start_time'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" required><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <button class="btn btn-primary">Save Changes</button>
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