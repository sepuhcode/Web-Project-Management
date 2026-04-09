<?php
require_once 'config.php';

$project_id = $_GET['id'] ?? 0;

$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

// ===== DELETE FILE =====
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
    
    header("Location: project_details.php?id=" . $project_id);
    exit;
}

// Documentation querry
$stmt = $conn->prepare("
SELECT a.*
FROM attachments a
JOIN reports r ON a.related_id = r.id
WHERE a.related_type='report' 
AND r.project_id=?
ORDER BY a.uploaded_at DESC
LIMIT 4
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$docs = $stmt->get_result();


if (!$project) {
    echo "Project not found";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['name']); ?> - SIS Dashboard</title>
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
<nav class="breadcrumb">
    <a href="index.php">Home</a>
    <span class="breadcrumb-separator">></span>
    <a href="all_projects.php" class="active">Active Projects</a>
    <span class="breadcrumb-separator">></span>
    <span class="breadcrumb-current"><?= htmlspecialchars($project['name']); ?></span>
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
            <span class="meta-value"><?= htmlspecialchars($project['pic']); ?></span>
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

<!-- TAB NAVIGATION -->
<section class="tab-navigation">
    <div class="tabs">
        <button class="tab-btn active" data-tab="overview" >Overview</button>
        <a href="installation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Installation</a>
        <a href="programming.php?project_id=<?= $project['id']; ?>" class="tab-btn">Programming</a>
        <a href="troubleshooting.php?project_id=<?= $project['id']; ?>" class="tab-btn">Troubleshooting</a>
        <a href="report_progress.php?project_id=<?= $project['id']; ?>" class="tab-btn">Report Progress</a>
        <a href="documentation.php?project_id=<?= $project['id']; ?>" class="tab-btn">Documentation</a>
    </div>
</section>

<!-- TAB CONTENT -->
<section class="tab-content">

<div class="tab-pane active" id="overview">
<div class="content-grid">

<!-- PROGRESS -->
<div class="progress-overview">
    <h2>Progress Overview</h2>

    <div class="progress-stages">

        <?php
        $stages = [
            ['Survey', 100],
            ['Installation', 85],
            ['Programming', 10],
            ['Testing', 0],
            ['Training', 0],
        ];

        foreach ($stages as $stage):
            $color = 'gray';
            if ($stage[1] >= 80) $color = 'green';
            elseif ($stage[1] >= 50) $color = 'orange';
            elseif ($stage[1] > 0) $color = 'red';
        ?>

        <div class="progress-stage">
            <div class="stage-header">
                <span class="stage-name"><?= $stage[0]; ?></span>
                <span class="stage-percentage"><?= $stage[1]; ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill <?= $color; ?>" style="width: <?= $stage[1]; ?>%"></div>
            </div>
        </div>

        <?php endforeach; ?>

    </div>
</div>


<!-- DOCUMENTATION -->
<div class="documentation">

<!-- ===== MODAL ===== -->
<div id="imgModal" class="img-modal">

<span class="close" onclick="closeModal()">&times;</span>

<a id="downloadBtn" class="download-btn-modal" download>Download</a>

<img id="modalImg" class="modal-content">

<iframe id="pdfViewer" style="display:none;"></iframe>

</div>

    <h2>Documentation</h2>

    <div class="documentation-grid">
    

        <?php while($d = $docs->fetch_assoc()): 
        $ext = strtolower(pathinfo($d['file_name'], PATHINFO_EXTENSION));
        ?>

            <div class="doc-item">

                <?php if(in_array($ext,['jpg','jpeg','png','gif'])): ?>

                <img src="<?= $d['file_path']; ?>" onclick="openImage('<?= $d['file_path']; ?>')">

                <?php elseif($ext=='pdf'): ?>

                <div class="pdf-preview" onclick="openPDF('<?= $d['file_path']; ?>')">
                📄 PDF
                </div>

                <?php endif; ?>

                <div class="file-name">
                    <?= htmlspecialchars($d['file_name']); ?>
                </div>

                <button type="button" class="delete-btn" onclick="deleteFile(<?= $d['id']; ?>)">
                    X
                </button>

                <a class="download-btn-small" href="<?= $d['file_path']; ?>" download >
                Download
                </a>

            </div>

        <?php endwhile; ?>
      
    </div>                                                                       
    <div class="doc-footer">
        <a href="documentation.php?project_id=<?= $project_id ?>" class="btn-primary" >
            VIEW ALL DOCUMENTATION >
        </a>
    </div>
    
</div>

</div>
</div>

</section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-tab');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            this.classList.add('active');
            document.getElementById(target)?.classList.add('active');
        });
    });
});
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
        window.location.href = "project_details.php?id=<?= $project_id ?>&delete_file="+id;
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