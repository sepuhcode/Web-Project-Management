<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? 0;

/* ===== DELETE FILE ===== */
if (isset($_GET['delete_file'])) {
    $file_id = (int)$_GET['delete_file'];
    
    // Get file info before deletion
    $stmt = $conn->prepare("
        SELECT a.file_path, r.project_id 
        FROM attachments a 
        JOIN reports r ON a.related_id = r.id 
        WHERE a.id = ? AND a.related_type = 'report'
    ");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $file_info = $stmt->get_result()->fetch_assoc();
    
    if ($file_info && $file_info['project_id'] == $project_id) {
        // Delete physical file
        if (file_exists($file_info['file_path'])) {
            unlink($file_info['file_path']);
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM attachments WHERE id = ? AND related_type = 'report'");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
    }
    
    header("Location: documentation.php?project_id=" . $project_id);
    exit;
}

/* ===== GET PROJECT ===== */
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    die("Project tidak ditemukan");
}

/* ===== GET FILES ===== */
$stmt = $conn->prepare("
SELECT a.*, r.report_date
FROM attachments a
JOIN reports r ON a.related_id = r.id
WHERE a.related_type='report'
AND r.project_id=?
ORDER BY a.uploaded_at DESC
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$files = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Documentation</title>
<link rel="stylesheet" href="styles.css">

</head>

<body>

<!-- ===== NAVBAR ===== -->
<header class="navbar">
<div class="nav-container">

        <div class="logo">
            <h1>Project Team Report</h1>
        </div>

            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="all_projects.php" class="active">Active Projects</a>
                <a href="#">Completed Project</a>
                <a href="schedule.php">Schedule</a>
                <a href="troubleshooting.php">Issue</a>
            </nav>

</div>
</header>

<main class="dashboard">

<!-- ===== BREADCRUMB ===== -->
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span class="breadcrumb-separator">></span>

    <a href="all_projects.php">Active Projects</a>
    <span class="breadcrumb-separator">></span>

    <a href="project_details.php?id=<?= $project['id']; ?>">
    <?= htmlspecialchars($project['name']); ?>
    </a>
<span class="breadcrumb-separator">></span>

<span class="breadcrumb-current">Documentation</span>
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

<!-- ===== TABS ===== -->
<section class="table-container">
    <div class="tabs">
        <a href="project_details.php?id=<?= $project_id ?>" class="tab-btn">Overview</a>
        <a href="installation.php?project_id=<?= $project_id ?>" class="tab-btn">Installation</a>
        <a href="programming.php?project_id=<?= $project_id ?>" class="tab-btn">Programming</a>
        <a href="troubleshooting.php?project_id=<?= $project_id ?>" class="tab-btn">Troubleshooting</a>
        <a href="report_progress.php?project_id=<?= $project_id ?>" class="tab-btn">Report Progress</a>
        <a href="documentation.php?project_id=<?= $project_id ?>" class="tab-btn active">Documentation</a>
    </div>
</section>

<!-- ===== CONTENT ===== -->
<div class="form-card">

<h2>All Documentation</h2>

<div class="doc-grid">

<?php while($f = $files->fetch_assoc()): 
$ext = strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
?>

<div class="doc-item">

<?php if(in_array($ext,['jpg','jpeg','png','gif'])): ?>

<img src="<?= $f['file_path']; ?>" onclick="openImage('<?= $f['file_path']; ?>')">

<?php elseif($ext=='pdf'): ?>

<div class="pdf-preview" onclick="openPDF('<?= $f['file_path']; ?>')">
📄 PDF
</div>

<?php endif; ?>

<div class="file-name">
<?= htmlspecialchars($f['file_name']); ?>
</div>

<button type="button" class="delete-btn" onclick="deleteFile(<?= $f['id']; ?>)">
    X
</button>

<a href="<?= $f['file_path']; ?>" download class="download-btn-small">
    Download
</a>

</div>

<?php endwhile; ?>

</div>

</div>

<!-- ===== BACK (BAWAH) ===== -->
<div style="margin-top:20px;">
<a href="project_details.php?id=<?= $project_id ?>" class="btn-secondary">
← Back
</a>
</div>

</main>

<!-- 🔥 FOOTER -->
<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

<!-- ===== MODAL ===== -->
<div id="imgModal" class="img-modal">

<span class="close" onclick="closeModal()">&times;</span>

<a id="downloadBtn" class="download-btn-modal" download>Download</a>

<img id="modalImg" class="modal-content">

<iframe id="pdfViewer" style="display:none;"></iframe>

</div>

<script>

function openImage(src){
    const modal = document.getElementById("imgModal");
    modal.style.display = "flex";

    document.getElementById("modalImg").style.display = "block";
    document.getElementById("pdfViewer").style.display = "none";

    document.getElementById("modalImg").src = src;
    setDownload(src);
}

function openPDF(src){
    const modal = document.getElementById("imgModal");
    modal.style.display = "flex";

    document.getElementById("modalImg").style.display = "none";
    document.getElementById("pdfViewer").style.display = "block";

    document.getElementById("pdfViewer").src = src;
    setDownload(src);
}

function setDownload(src){
    const d = document.getElementById("downloadBtn");
    d.href = src;
    d.setAttribute("download", src.split('/').pop());
}

function closeModal(){
    document.getElementById("imgModal").style.display = "none";
}

function deleteFile(id){
    if(confirm("Delete this file? This action cannot be undone.")){
        window.location.href = "documentation.php?project_id=<?= $project_id ?>&delete_file="+id;
    }
}

window.onclick = function(e){
    const modal = document.getElementById("imgModal");
    if(e.target === modal){
        closeModal();
    }
}

</script>

</body>
</html>