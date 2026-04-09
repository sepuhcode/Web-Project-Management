<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;

/* ===== GET REPORT ===== */
$stmt = $conn->prepare("
    SELECT r.*, p.name as project_name, p.id as project_id
    FROM reports r
    LEFT JOIN projects p ON r.project_id = p.id
    WHERE r.id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    die("Report tidak ditemukan");
}

/* ===== GET FILES ===== */
$stmt = $conn->prepare("
    SELECT * FROM attachments 
    WHERE related_type='report' AND related_id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$files = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>View Report</title>
<link rel="stylesheet" href="styles.css">

<style>

/* ===== LAYOUT ===== */
.report-view {
    max-width: 900px;
    margin: auto;
    padding: 20px;
}

h3 {
    margin-bottom: 10px;
}
.report-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-bottom: 25px;
}

.report-card p {
    margin-top: 5px;
    line-height: 1.6;
}
/* ===== GRID ===== */
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px,1fr));
    gap: 15px;
    padding-bottom: 10px;
    text-align: center;
}

.image-item {
    text-align: center;
}

/* ===== IMAGE ===== */
.image-item img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    padding-bottom: 10px;
    text-align: center;
}

/* ===== PDF ===== */
.pdf-preview {
    width: 100%;
    height: 140px;
    background: #f1f5f9;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
}

/* ===== FILE NAME ===== */
.file-name {
    font-size: 12px;
    margin-top: 6px;
    color: #555;
    word-break: break-word;
}

/* ===== MODAL ===== */
.img-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.95);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal-content {
    max-width: 95%;
    max-height: 90%;
    border-radius: 10px;
}

#pdfViewer {
    width: 95%;
    height: 90%;
    border: none;
}

/* ===== BUTTON ===== */
.close {
    position: absolute;
    top: 20px;
    right: 25px;
    color: white;
    font-size: 32px;
    cursor: pointer;
}

.download-btn {
    position: absolute;
    top: 20px;
    left: 25px;
    background: #3b82f6;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
}

.back-btn {
    display: inline-block;
    margin-top: 10px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px){

    .modal-content {
        max-width: 100%;
        max-height: 85%;
    }

    #pdfViewer {
        width: 100%;
        height: 85%;
    }
}
</style>

</head>

<body>

<!-- NAVBAR -->
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
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span>></span>

    <a href="all_projects.php">Active Projects</a>
    <span>></span>

    <a href="project_details.php?id=<?= $report['project_id']; ?>">
        <?= htmlspecialchars($report['project_name']); ?>
    </a>
    <span>></span>

    <a href="report_progress.php?project_id=<?= $report['project_id']; ?>">
        Report Progress
    </a>
    <span>></span>

    <span>View Report</span>
</nav>

<section class="report-view">

<!-- DATE -->
<div class="report-card">
    <h3>Report Date</h3>
    <p><?= formatDate($report['report_date']); ?></p>
</div>

<!-- SUMMARY -->
<div class="report-card">
    <h3>Progress Summary</h3>
    <p><?= nl2br(htmlspecialchars($report['progress_summary'])); ?></p>
</div>

<!-- FILE -->
<div class="report-card">
    <h3>Documentation</h3>

    <div class="image-grid">

        <?php while($f = $files->fetch_assoc()): 
        $ext = strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
        ?>

        <div class="image-item">

            <?php if(in_array($ext,['jpg','jpeg','png','gif'])): ?>

                <img src="<?= $f['file_path']; ?>" 
                     onclick="openImage('<?= $f['file_path']; ?>')">

            <?php elseif($ext === 'pdf'): ?>

                <div class="pdf-preview"
                     onclick="openPDF('<?= $f['file_path']; ?>')">
                    📄 PDF
                </div>

            <?php endif; ?>

            <div class="file-name">
                <?= htmlspecialchars($f['file_name']); ?>
            </div>

        </div>

        <?php endwhile; ?>

    </div>

</div>

<!-- BACK -->
<a href="report_progress.php?project_id=<?= $report['project_id']; ?>" 
class="btn-secondary back-btn">
    Back
</a>

</section>

</main>

<!-- MODAL -->
<div id="imgModal" class="img-modal">

<span class="close" onclick="closeModal()">&times;</span>

<a id="downloadBtn" class="download-btn" download>Download</a>

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

window.onclick = function(e){
    if(e.target.id === "imgModal"){
        closeModal();
    }
}

</script>

</body>
</html>