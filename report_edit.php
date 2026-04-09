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

if (!$report) die("Report tidak ditemukan");

/* ===== DELETE FILE ===== */
if (isset($_GET['delete_file'])) {

    $fid = intval($_GET['delete_file']);

    $stmt = $conn->prepare("SELECT * FROM attachments WHERE id=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file = $stmt->get_result()->fetch_assoc();

    if ($file && file_exists($file['file_path'])) {
        unlink($file['file_path']);
    }

    $stmt = $conn->prepare("DELETE FROM attachments WHERE id=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();

    header("Location: report_edit.php?id=".$id);
    exit;
}

/* ===== UPDATE ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        UPDATE reports 
        SET report_date=?, progress_summary=? 
        WHERE id=?
    ");
    $stmt->bind_param("ssi",
        $_POST['report_date'],
        $_POST['progress_summary'],
        $id
    );
    $stmt->execute();

    /* ===== UPLOAD ===== */
    if (!empty($_FILES['files']['name'][0])) {

        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {

            $name = $_FILES['files']['name'][$i];
            $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg','jpeg','png','gif','pdf'])) continue;

            $new = time().'_'.uniqid().'.'.$ext;
            $path = $dir.$new;

            if (move_uploaded_file($tmp, $path)) {

                $stmt2 = $conn->prepare("
                    INSERT INTO attachments 
                    (related_type, related_id, file_name, file_path)
                    VALUES ('report', ?, ?, ?)
                ");
                $stmt2->bind_param("iss", $id, $name, $path);
                $stmt2->execute();
            }
        }
    }

    header("Location: report_progress.php?project_id=".$report['project_id']);
    exit;
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
<title>Edit Report</title>
<link rel="stylesheet" href="styles.css">

<style>

.form-card {
    padding: 30px;
}

/* jarak antar section */
.form-group,
.image-grid,
.upload-area,

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

/* jarak judul */
h2 {
    margin-bottom: 20px;
}

h3 {
    margin: 25px 0 10px;
}

/* ===== GRID ===== */
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px,1fr));
    gap: 15px;
}

.image-item {
    position: relative;
    text-align: center;
}

/* ===== IMAGE ===== */
.image-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
}

/* ===== PDF ===== */
.pdf-preview {
    width: 100%;
    height: 120px;
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
    margin-top: 5px;
    color: #555;
    word-break: break-word;
}

/* ===== DELETE ===== */
.delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: red;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 4px 8px;
    cursor: pointer;
}

/* ===== UPLOAD CARD ===== */
.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    background: #f9fafb;
    padding: 30px;
    text-align: center;
    transition: 0.3s;
}
.upload-area:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.upload-content {
    cursor: pointer;
}

.upload-icon {
    font-size: 40px;
    margin-bottom: 15px;
}

.upload-text h4 {
    margin-bottom: 5px;
}

.upload-btn {
    margin-top: 15px;
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

.file-preview {
    margin-top: 15px;
    color: #666;
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

<div class="form-card">

<h2>Edit Report</h2>

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Date</label>
<input type="date" name="report_date" value="<?= $report['report_date']; ?>">
</div>

<div class="form-group">
<label>Summary</label>
<textarea name="progress_summary"><?= $report['progress_summary']; ?></textarea>
</div>

<!-- ===== EXISTING FILE ===== -->
<h3>Existing Files</h3>

<div class="image-grid">

<?php while($f = $files->fetch_assoc()): 
$ext = strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
?>

<div class="image-item">

<?php if(in_array($ext, ['jpg','jpeg','png','gif'])): ?>

<img src="<?= $f['file_path']; ?>" onclick="openImage('<?= $f['file_path']; ?>')">

<?php elseif($ext === 'pdf'): ?>

<div class="pdf-preview" onclick="openPDF('<?= $f['file_path']; ?>')">
📄 PDF
</div>

<?php endif; ?>

<div class="file-name">
<?= htmlspecialchars($f['file_name']); ?>
</div>

<button type="button" class="delete-btn"
onclick="deleteFile(<?= $f['id']; ?>)">
X
</button>

</div>

<?php endwhile; ?>

</div>

<!-- ===== UPLOAD ===== -->
<h3>Add New Files</h3>

<div class="upload-area" id="uploadArea">

    <input 
        type="file" 
        id="fileInput" 
        name="files[]" 
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

    <div class="file-preview" id="preview">
        No files selected
    </div>

</div>

<div class="form-actions">
<button class="btn-primary">UPDATE</button>
<a href="report_progress.php?project_id=<?= $report['project_id']; ?>" class="btn-secondary">BACK</a>
</div>

</form>

</div>

</main>

<!-- ===== MODAL ===== -->
<div id="imgModal" class="img-modal">

<span class="close" onclick="closeModal()">&times;</span>

<a id="downloadBtn" class="download-btn" download>Download</a>

<img id="modalImg" class="modal-content">

<iframe id="pdfViewer" style="display:none;"></iframe>

</div>

<script>

const input = document.getElementById('fileInput');
const preview = document.getElementById('preview');
const trigger = document.getElementById('uploadTrigger');
const area = document.getElementById('uploadArea');
const btn = document.getElementById('chooseBtn');

// klik area
trigger.addEventListener('click', () => input.click());

// klik tombol
btn.addEventListener('click', (e) => {
    e.stopPropagation();
    input.click();
});

// drag
area.addEventListener('dragover', e => {
    e.preventDefault();
    area.style.borderColor = '#3b82f6';
});

area.addEventListener('dragleave', () => {
    area.style.borderColor = '#d1d5db';
});

area.addEventListener('drop', e => {
    e.preventDefault();
    input.files = e.dataTransfer.files;
    renderFiles();
});

// change
input.addEventListener('change', renderFiles);

function renderFiles() {

    if (input.files.length === 0) {
        preview.innerHTML = "No files selected";
        return;
    }

    let html = "";

    Array.from(input.files).forEach(file => {
        html += file.name + "<br>";
    });

    preview.innerHTML = html;
}

function deleteFile(id){
    if(confirm("Delete file?")){
        window.location.href = "report_edit.php?id=<?= $id ?>&delete_file="+id;
    }
}

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
    const modal = document.getElementById("imgModal");
    if(e.target === modal){
        closeModal();
    }
}

/* preview upload */
document.getElementById("fileInput").onchange = function(){
    let txt = "";
    for(let f of this.files){
        txt += f.name + "<br>";
    }
    document.getElementById("preview").innerHTML = txt || "No files selected";
}

</script>

</body>
</html>