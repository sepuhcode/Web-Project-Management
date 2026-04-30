<?php
require_once 'config.php';



$issue_id = $_GET['id'] ?? 0;
// ===== DELETE ATTACHMENT =====
if (isset($_GET['delete_file'])) {

    $file_id = intval($_GET['delete_file']);

    // ambil file
    $stmt = $conn->prepare("
        SELECT file_path FROM attachments 
        WHERE id=? AND related_type='issue' AND related_id=?
    ");
    $stmt->bind_param("ii", $file_id, $issue_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {

        $file_path = $result['file_path'];

        // hapus file dari server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // hapus dari DB
        $stmt = $conn->prepare("DELETE FROM attachments WHERE id=?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
    }

    // reload
    header("Location: troubleshooting_edit.php?id=".$issue_id);
    exit;
}
// GET DATA
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
    echo "Issue not found";
    exit;
}

// ATTACHMENT
$stmt = $conn->prepare("
SELECT * FROM attachments 
WHERE related_type='issue' AND related_id=?
");
$stmt->bind_param("i", $issue_id);
$stmt->execute();
$attachments = $stmt->get_result();
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== UPDATE ISSUE =====
    $stmt = $conn->prepare("
        UPDATE issues SET 
            issue_type = ?, 
            description = ?, 
            resolution = ?,
            priority = ?, 
            status = ?, 
            error_code = ?, 
            system_logs = ?, 
            network_status = ?, 
            root_cause = ?, 
            reported_date = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssssssssi",
        $_POST['issue_type'],
        $_POST['description'],
        $_POST['resolution'],
        $_POST['priority'],
        $_POST['status'],
        $_POST['error_code'],
        $_POST['system_logs'],
        $_POST['network_status'],
        $_POST['root_cause'],
        $_POST['reported_date'],
        $issue_id
    );

    $stmt->execute();

    // ===== UPLOAD FILE BARU =====
    $upload_dir = "uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['files']['name'][0])) {

        foreach ($_FILES['files']['tmp_name'] as $key => $tmp) {

            $name = $_FILES['files']['name'][$key];
            $size = $_FILES['files']['size'][$key];

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx','zip','txt','log'];

            if (!in_array($ext, $allowed)) {
                continue;
            }

            $new_name = time() . '_' . $name;
            $path = $upload_dir . $new_name;

            if (move_uploaded_file($tmp, $path)) {

                $size_kb = round($size / 1024, 2) . " KB";

                $stmt2 = $conn->prepare("
                    INSERT INTO attachments 
                    (related_type, related_id, file_name, file_path, file_size)
                    VALUES ('issue', ?, ?, ?, ?)
                ");

                $stmt2->bind_param(
                    "isss",
                    $issue_id,
                    $name,
                    $path,
                    $size_kb
                );

                $stmt2->execute();
            }
        }
    }

    // ===== REDIRECT =====
    header("Location: troubleshooting_view.php?id=" . $issue_id);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Troubleshooting</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="upload-enhanced.css">
<style>

</style>
</head>

<body>

<!-- 🔥 NAVBAR -->
<header class="navbar">
    <div class="nav-container">
        <div class="logo">
            <h1>Project Team Report</h1>
        </div>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="all_projects.php">Active Projects</a>
            <a href="all_projects.php?status=completed">Completed Project</a>
            <a href="schedule.php">Schedule</a>
            <a href="troubleshooting.php" class="active">Issue</a>
        </nav>
    </div>
</header>

<!-- 🔥 MAIN -->
<main class="dashboard">

<!-- 🔥 CONTAINER -->
<div class="container">

    <!-- 🔥 BREADCRUMB -->
    <nav class="breadcrumb">
        <a href="index.php">Home</a>
        <span class="breadcrumb-separator">></span>

        <a href="all_projects.php">Active Projects</a>
        <span class="breadcrumb-separator">></span>

        <a href="project_details.php?id=<?= $issue['project_id']; ?>">
            <?= htmlspecialchars($issue['project_name']); ?>
        </a>
        <span class="breadcrumb-separator">></span>

        <a href="troubleshooting.php?project_id=<?= $issue['project_id']; ?>">Troubleshooting</a>
        <span class="breadcrumb-separator">></span>

        <span class="breadcrumb-current">
            Edit Issue #<?= $issue['id']; ?>
        </span>
    </nav>

    <!-- 🔥 CARD -->
    <section class="edit-form">
        <div class="form-card">

            <h2>Edit Troubleshooting Task</h2>

            <form method="POST" enctype="multipart/form-data" class="task-form">

                <!-- GRID -->
                <div class="form-grid">

                    <div class="form-group">
                        <label>Issue Type *</label>
                        <input type="text" name="issue_type"
                               value="<?= htmlspecialchars($issue['issue_type']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Area *</label>
                        <input type="text" name="area"
                               value="<?= htmlspecialchars($issue['area']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Priority *</label>
                        <select name="priority">
                            <option value="low" <?= ($issue['priority'] ?? '')=='low'?'selected':'' ?>>Low</option>
                            <option value="medium" <?= ($issue['priority'] ?? '')=='medium'?'selected':'' ?>>Medium</option>
                            <option value="high" <?= ($issue['priority'] ?? '')=='high'?'selected':'' ?>>High</option>
                            <option value="critical" <?= ($issue['priority'] ?? '')=='critical'?'selected':'' ?>>Critical</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="pending" <?= ($issue['status'] ?? '')=='pending'?'selected':'' ?>>Pending</option>
                            <option value="in-progress" <?= ($issue['status'] ?? '')=='in-progress'?'selected':'' ?>>In Progress</option>
                            <option value="completed" <?= ($issue['status'] ?? '')=='completed'?'selected':'' ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Reported Date</label>
                        <input type="date" name="reported_date"
                               value="<?= date('Y-m-d', strtotime($issue['reported_date'])); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description"><?= htmlspecialchars($issue['description']); ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Resolution</label>
                        <textarea name="resolution"><?= htmlspecialchars($issue['resolution']); ?></textarea>
                    </div>

                </div>

                <!-- DIAGNOSTIC -->
                <div class="form-group">
                    <h3>Diagnostic Information</h3>

                    <div class="form-grid">
                        <input type="text" name="error_code" placeholder="Error Code"
                               value="<?= $issue['error_code']; ?>">

                        <input type="text" name="system_logs" placeholder="System Logs"
                               value="<?= $issue['system_logs']; ?>">

                        <input type="text" name="network_status" placeholder="Network Status"
                               value="<?= $issue['network_status']; ?>">

                        <input type="text" name="root_cause" placeholder="Root Cause"
                               value="<?= $issue['root_cause']; ?>">
                    </div>
                </div>

                
                <!-- ATTACHMENT -->
                <div class="info-card full-width">
                    <h3>Attachments</h3>

                    <!-- EXISTING FILES -->
                    <?php if($attachments->num_rows > 0): ?>
                    <h4>Current Attachments:</h4>
                    <div class="doc-grid">
                        <?php while($file = $attachments->fetch_assoc()): 
                        $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                        ?>
                        
                        <div class="doc-item">
                        
                        <?php if(in_array($ext,['jpg','jpeg','png','gif'])): ?>
                        
                        <img src="<?= $file['file_path']; ?>" onclick="openImage('<?= $file['file_path']; ?>')">
                        
                        <?php elseif($ext=='pdf'): ?>
                        
                        <div class="pdf-preview" onclick="openPDF('<?= $file['file_path']; ?>')">
                        📄 PDF
                        </div>
                        
                        <?php else: ?>
                        
                        <div class="file-preview" onclick="window.open('<?= $file['file_path']; ?>', '_blank')">
                        📄 <?= strtoupper($ext); ?>
                        </div>
                        
                        <?php endif; ?>
                        
                        <div class="file-name">
                        <?= htmlspecialchars($file['file_name']); ?>
                        </div>
                        
                        <a href="<?= $file['file_path']; ?>" download class="download-btn-small">
                        Download
                        </a>

                        <button type="button" class="delete-btn" onclick="confirmDelete(<?= $file['id']; ?>)">
                        X
                        </button>
                        
                        </div>
                        
                        <?php endwhile; ?>
                    </div>
                    
                    <?php else: ?>
                    <div class="no-attachments">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#d1d5db" stroke-width="2"/>
                            <path d="M14 2v6h6" stroke="#d1d5db" stroke-width="2"/>
                            <path d="M8 13h8M8 17h6" stroke="#d1d5db" stroke-width="2"/>
                        </svg>
                        <p>No attachments yet</p>
                    </div>
                    <?php endif; ?>

                    <!-- UPLOAD AREA -->
                    <div class="upload-section">
                        <h4>Add New Files:</h4>
                        <div class="upload-area">
                            <input type="file" name="files[]" multiple id="file-input-edit" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.txt,.log" style="display: none;">
                            
                            <div class="upload-content" id="upload-trigger-edit">
                                <div class="upload-icon">
                                ⬆️
                                </div>
                                <div class="upload-text">
                                    <h4>Drop files here or click to browse</h4>
                                    <p>Support for PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT, LOG</p>
                                    <span class="upload-hint">Maximum file size: 10MB per file</span>
                                </div>
                                <button type="button" class="upload-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Choose Files
                                </button>
                            </div>
                            <div class="" id="file-preview-edit"></div>
                        </div>
                    </div>
                    <!-- ACTION -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Edit</button>

                        <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='troubleshooting.php?project_id=<?= $issue['project_id']; ?>'">
                            Cancel
                        </button>
                        <button class="btn btn-danger" onclick="confirmDelete(<?= $issue['id']; ?>)">
                            Delete Task
                        </button>
                    </div>
                </div>

                

            </form>

        </div>
    </section>

</div>

</main>

<!-- 🔥 FOOTER -->
<footer class="footer">
    <p>© <?= date('Y'); ?> Project Team Report</p>
</footer>

<!-- 🔥 JAVASCRIPT -->
<script>
// Edit Page Upload Functionality
const inputEdit = document.getElementById('file-input-edit');
const previewEdit = document.getElementById('file-preview-edit');
const triggerEdit = document.getElementById('upload-trigger-edit');
const areaEdit = document.querySelector('.upload-area');

let filesArrayEdit = [];

if (triggerEdit) {
    triggerEdit.onclick = () => inputEdit.click();
}

if (inputEdit) {
    inputEdit.onchange = e => {
        const newFiles = Array.from(e.target.files);
        const validFiles = newFiles.filter(file => {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validTypes = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','zip','txt','log'];
            const ext = file.name.split('.').pop().toLowerCase();
            return validTypes.includes(ext) && file.size <= maxSize;
        });
        
        if (validFiles.length !== newFiles.length) {
            showNotification('Some files were invalid or too large and were skipped', 'warning');
        }
        
        filesArrayEdit = filesArrayEdit.concat(validFiles);
        renderEdit();
    };
}

if (areaEdit) {
    areaEdit.ondragover = e => {
        e.preventDefault();
        areaEdit.classList.add('dragging');
    };

    areaEdit.ondragleave = () => areaEdit.classList.remove('dragging');

    areaEdit.ondrop = e => {
        e.preventDefault();
        areaEdit.classList.remove('dragging');
        const droppedFiles = Array.from(e.dataTransfer.files);
        
        const validFiles = droppedFiles.filter(file => {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validTypes = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','zip','txt','log'];
            const ext = file.name.split('.').pop().toLowerCase();
            return validTypes.includes(ext) && file.size <= maxSize;
        });
        
        if (validFiles.length !== droppedFiles.length) {
            showNotification('Some files were invalid or too large and were skipped', 'warning');
        }
        
        filesArrayEdit = filesArrayEdit.concat(validFiles);
        renderEdit();
    };
}

function renderEdit(){
    if (!previewEdit) return;
    
    previewEdit.innerHTML = '';
    
    if (filesArrayEdit.length === 0) {
        previewEdit.innerHTML = '<div class="no-files">No additional files selected</div>';
        return;
    }
    
    // Create file list container
    const file_list = document.createElement('div');
    file_list.className = 'file-list';
    
    filesArrayEdit.forEach((f,i)=>{
        const fileSize = formatFileSize(f.size);
        const fileIcon = getFileIcon(f.name);
        
        file_list.innerHTML += `
        <div class="file-item">
            <div class="file-info">
                <div class="file-icon">${fileIcon}</div>
                <div class="file-details">
                    <span class="file-name">${f.name}</span>
                    <span class="file-size">${fileSize}</span>
                </div>
            </div>
            <button type="button" class="remove-btn" onclick="removeFileEdit(${i})" title="Remove file">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2.5"/>
                    <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2.5"/>
                </svg>
            </button>
        </div>`;
    });
    
    previewEdit.appendChild(file_list);
}

function removeFileEdit(i){
    filesArrayEdit.splice(i,1);
    renderEdit();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        pdf: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#dc2626" stroke-width="2"/><path d="M14 2v6h6" stroke="#dc2626" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#dc2626" stroke-width="2"/></svg>',
        doc: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#2563eb" stroke-width="2"/><path d="M14 2v6h6" stroke="#2563eb" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#2563eb" stroke-width="2"/></svg>',
        docx: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#2563eb" stroke-width="2"/><path d="M14 2v6h6" stroke="#2563eb" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#2563eb" stroke-width="2"/></svg>',
        xls: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#16a34a" stroke-width="2"/><path d="M14 2v6h6" stroke="#16a34a" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#16a34a" stroke-width="2"/></svg>',
        xlsx: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#16a34a" stroke-width="2"/><path d="M14 2v6h6" stroke="#16a34a" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#16a34a" stroke-width="2"/></svg>',
        jpg: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#10b981" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke="#10b981" stroke-width="2"/><path d="M21 15l-5-5L5 21" stroke="#10b981" stroke-width="2"/></svg>',
        jpeg: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#10b981" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke="#10b981" stroke-width="2"/><path d="M21 15l-5-5L5 21" stroke="#10b981" stroke-width="2"/></svg>',
        png: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#10b981" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke="#10b981" stroke-width="2"/><path d="M21 15l-5-5L5 21" stroke="#10b981" stroke-width="2"/></svg>',
        gif: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#10b981" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke="#10b981" stroke-width="2"/><path d="M21 15l-5-5L5 21" stroke="#10b981" stroke-width="2"/></svg>',
        zip: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M16 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4z" stroke="#f59e0b" stroke-width="2"/><path d="M16 3v4h4" stroke="#f59e0b" stroke-width="2"/><path d="M8 13h0M8 17h0M12 13h0M12 17h0" stroke="#f59e0b" stroke-width="2"/></svg>',
        txt: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#6b7280" stroke-width="2"/><path d="M14 2v6h6" stroke="#6b7280" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#6b7280" stroke-width="2"/></svg>',
        log: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#6b7280" stroke-width="2"/><path d="M14 2v6h6" stroke="#6b7280" stroke-width="2"/><path d="M8 13h8M8 17h6" stroke="#6b7280" stroke-width="2"/></svg>'
    };
    return icons[ext] || icons.txt;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            ${type === 'warning' ? 
                '<path d="M12 9v4m0 4h.01M5.07 19h13.86a2 2 0 001.75-2.98L13.75 4.98A2 2 0 0010.25 5l-7.54 11.04A2 2 0 015.07 19z" stroke="currentColor" stroke-width="2"/>' :
                '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2"/>'
            }
        </svg>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize
if (previewEdit) {
    renderEdit();
}
</script>

<!-- Image Modal -->
<div id="imageModal" class="img-modal" onclick="closeModal()">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <a href="#" id="modalDownload" class="download-btn-modal" download>Download</a>
</div>

<script>
function openImage(src) {
    document.getElementById('imageModal').style.display = 'flex';
    document.getElementById('modalImage').src = src;
    document.getElementById('modalDownload').href = src;
}

function openPDF(src) {
    document.getElementById('imageModal').style.display = 'flex';
    document.getElementById('modalImage').src = '';
    document.getElementById('modalImage').innerHTML = '<iframe src="' + src + '" style="width:100%; height:100%; border:none;"></iframe>';
    document.getElementById('modalDownload').href = src;
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('modalImage').src = '';
    document.getElementById('modalImage').innerHTML = '';
}

function confirmDelete(fileId) {
    if (confirm('Yakin mau hapus file ini?')) {
        window.location.href = '?id=<?= $issue_id ?>&delete_file=' + fileId;
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>

</body>
</html>