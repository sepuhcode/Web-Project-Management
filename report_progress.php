<?php
require_once 'config.php';

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

/* ================= GET PROJECT ================= */
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

/* ================= DELETE ================= */
if (isset($_GET['delete_id'])) {

    $delete_id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("
        SELECT file_path FROM attachments 
        WHERE related_type='report' AND related_id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $files = $stmt->get_result();

    while ($f = $files->fetch_assoc()) {
        if (file_exists($f['file_path'])) unlink($f['file_path']);
    }

    $stmt = $conn->prepare("
        DELETE FROM attachments 
        WHERE related_type='report' AND related_id=?
    ");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM reports WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: report_progress.php?project_id=".$project_id);
    exit;
}

/* ================= ADD ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {

    $project_id_form = intval($_POST['project_id']);
    $date = $_POST['report_date'];
    $summary = $_POST['progress_summary'];

    $stmt = $conn->prepare("
        INSERT INTO reports (project_id, report_date, progress_summary)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $project_id_form, $date, $summary);
    $stmt->execute();

    $report_id = $conn->insert_id;

    if (!empty($_FILES['files']['name'][0])) {

        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {

            $name = $_FILES['files']['name'][$i];
            $size = $_FILES['files']['size'][$i];

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','pdf'];

            if (!in_array($ext, $allowed)) continue;

            $new = time().'_'.uniqid().'.'.$ext;
            $path = $dir.$new;

            if (move_uploaded_file($tmp, $path)) {

                $size_mb = round($size/1024/1024,2)." MB";

                $stmt2 = $conn->prepare("
                    INSERT INTO attachments 
                    (related_type, related_id, file_name, file_path, file_size)
                    VALUES ('report', ?, ?, ?, ?)
                ");
                $stmt2->bind_param("isss", $report_id, $name, $path, $size_mb);
                $stmt2->execute();
            }
        }
    }

    header("Location: report_progress.php?project_id=".$project_id_form);
    exit;
}

/* ================= EDIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {

    $id = intval($_POST['edit_id']);
    $date = $_POST['report_date'];
    $summary = $_POST['progress_summary'];

    $stmt = $conn->prepare("
        UPDATE reports 
        SET report_date=?, progress_summary=? 
        WHERE id=?
    ");
    $stmt->bind_param("ssi", $date, $summary, $id);
    $stmt->execute();

    header("Location: report_progress.php?project_id=".$project_id);
    exit;
}

/* ================= GET DATA ================= */
$stmt = $conn->prepare("
    SELECT * FROM reports 
    WHERE project_id=? 
    ORDER BY report_date DESC
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$reports = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Progress</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
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

<!-- ================= BREADCRUMB ================= -->
<nav class="breadcrumb">
<a href="index.php">Home</a>
<span class="breadcrumb-separator">></span>

<a href="all_projects.php">Active Projects</a>
<span class="breadcrumb-separator">></span>

<a href="project_details.php?id=<?= $project_id ?>">
<?= htmlspecialchars($project['name']); ?>
</a>
<span class="breadcrumb-separator">></span>

<span class="breadcrumb-current">Report Progress</span>
</nav>

<!-- HEADER -->
        <section class="project-header-section">
            <div class="project-title">
                <h1><?= htmlspecialchars($project['name']); ?></h1>
                <span class="project-status-badge <?= $project['status']; ?>">
                    <?= ucfirst($project['status']); ?>
                </span>
            </div>

            <div class="project-meta">
                <div class="meta-item">
                    <span class="meta-label">Start Date:</span>
                    <span class="meta-value"><?= formatDate($project['start_date']); ?></span>
                </div>

                <div class="meta-item">
                    <span class="meta-label">End Date:</span>
                    <span class="meta-value"><?= formatDate($project['end_date']); ?></span>
                </div>

                <div class="meta-item">
                    <span class="meta-label">PIC:</span>
                    <span class="meta-value"><?= htmlspecialchars($project['pic'] ?? 'Fahmi'); ?></span>
                </div>
                <!-- PROGRESS -->
                <div class="meta-item">
                    <div class="progress-info">
                        <span class="meta-label">Progress:</span>
                        <span class="meta-label" style="visibility:hidden;">..</span>
                        <span class="progress-percent"> <?= $project['progress']; ?>%</span>
                    </div>

                    <div class="progress-bar">
                        <?php
                        $color = 'red';
                        if ($project['progress'] >= 80) $color = 'green';
                        elseif ($project['progress'] >= 50) $color = 'orange';
                        elseif ($project['progress'] >= 30) $color = 'blue';
                        ?>
                        <div class="progress-fill <?= $color; ?>" style="width: <?= $project['progress']; ?>%"></div>
                    </div>
                </div>
            </div>
        </section>

<!-- ================= TABS ================= -->
<section class="table-container">
<div class="tabs">

<a href="project_details.php?id=<?= $project_id ?>" class="tab-btn">Overview</a>

<a href="installation.php?project_id=<?= $project_id ?>" class="tab-btn">Installation</a>

<a href="programming.php?project_id=<?= $project_id ?>" class="tab-btn">Programming</a>

<a href="troubleshooting.php?project_id=<?= $project_id ?>" class="tab-btn">Troubleshooting</a>

<a href="#" class="tab-btn active">Report Progress</a>

<a href="documentation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Documentation</a>

</div>
</section>


<section class="table-container">
<div class="tab-pane active">

<div class="page-header">
    <h2>Progress Reporting</h2>
    <p>Submit daily/weekly progress reports and documentation</p>
</div>

<!-- ===== FORM ===== -->
<div class="report-form-container">
<div class="form-card">

<h3>Submit Progress Report</h3>

<form method="POST" enctype="multipart/form-data" class="progress-form">

    <input type="hidden" name="project_id" value="<?= $project_id ?>">

    <div class="form-grid">

        <div class="form-group">
            <label>Report Date</label>
            <input type="date" name="report_date" required>
        </div>

        <div class="form-group full-width">
            <label>Progress Summary</label>
            <textarea name="progress_summary" rows="4" required></textarea>
        </div>

    </div>

    <!-- UPLOAD -->
    <div class="upload-section">

        <h4>Documentation Upload</h4>

        <div class="upload-area" id="uploadArea">

            <input 
                type="file" 
                name="files[]" 
                id="fileInput" 
                multiple 
                hidden
            >

            <div class="upload-content" id="uploadTrigger">

                <div class="upload-icon">
                    ⬆️
                </div>

                <div class="upload-text">
                    <h4>Drop files here or click to browse</h4>
                    <p>Support for PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT, LOG</p>
                    <span>Maximum file size: 10MB per file</span>
                </div>

                <button type="button" class="upload-btn" id="chooseBtn">
                    Choose Files
                </button>

            </div>

<div class="file-preview" id="filePreview">
    <div class="no-files">No files selected</div>
</div>

</div>
</div>

<div class="form-actions" style="margin-bottom:40px;">
    <button type="submit" class="btn btn-primary">Submit Report</button>
</div>



</form>



</div>

<!-- ===== RECENT REPORT ===== -->
<div class="recent-reports" style="margin-top:40px;">
    <h3>Recent Reports</h3>

    <div class="reports-list">

        <?php while($r = $reports->fetch_assoc()): ?>
        <div class="report-item">

            <div class="report-info">
                <h4><?= formatDate($r['report_date']); ?></h4>

                <p><?= nl2br(htmlspecialchars(substr($r['progress_summary'],0,120))); ?>...</p>

                <span class="report-date">
                    Submitted: <?= formatDate($r['report_date']); ?>
                </span>
            </div>

            <div class="report-actions">

                <a href="report_view.php?id=<?= $r['id']; ?>" class="action-btn view">View</a>

                <a href="report_edit.php?id=<?= $r['id']; ?>" class="action-btn edit">
                    Edit
                </a>

                <button class="action-btn delete"
                        onclick="deleteReport(<?= $r['id']; ?>)">
                    Delete
                </button>

            </div>

        </div>
        <?php endwhile; ?>

</div>
</div>
</div>

</div>
</section>

</main>

<!-- 🔥 FOOTER -->
<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

<script>
function deleteReport(id){
    if(confirm("Delete report?")){
        window.location.href = "report_progress.php?delete_id="+id+"&project_id=<?= $project_id ?>";
    }
}

function editReport(id,date,summary){

    let form = document.querySelector(".progress-form");

    let input = document.createElement("input");
    input.type = "hidden";
    input.name = "edit_id";
    input.value = id;
    form.appendChild(input);

    form.report_date.value = date;
    form.progress_summary.value = summary;

    window.scrollTo({top:0,behavior:"smooth"});
}

const input = document.getElementById('fileInput');
const preview = document.getElementById('filePreview');
const trigger = document.getElementById('uploadTrigger');
const area = document.getElementById('uploadArea');

trigger.onclick = () => input.click();

input.onchange = () => render();

area.ondragover = e => {
    e.preventDefault();
    area.style.borderColor = '#3b82f6';
};

area.ondragleave = () => {
    area.style.borderColor = '#d1d5db';
};

area.ondrop = e => {
    e.preventDefault();
    input.files = e.dataTransfer.files;
    render();
};

function render(){

    preview.innerHTML = "";

    if(input.files.length === 0){
        preview.innerHTML = "<div class='no-files'>No files selected</div>";
        return;
    }

    for(let file of input.files){
        let div = document.createElement("div");
        div.innerText = file.name;
        preview.appendChild(div);
    }
}
</script>



</body>
</html>
