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
/* Enhanced Attachment Styles */
.form-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.form-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h3::before {
    content: '📎';
    font-size: 1.5rem;
}

.form-section h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 1rem;
}

/* Attachment List */
.attachment-list {
    margin-bottom: 2rem;
}

.attachment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.attachment-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.attachment-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.attachment-details {
    flex: 1;
    min-width: 0;
}

.attachment-details strong {
    display: block;
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.25rem;
    word-break: break-all;
}

.attachment-details small {
    color: #6b7280;
    font-size: 0.875rem;
}

.attachment-actions {
    flex-shrink: 0;
}

.btn-view {
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    margin-left: 8px;
    font-size: 13px;
    background: #3b82f6;
    }

.btn-view:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}
.btn-delete {
    background: #ef4444;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    margin-left: 8px;
    font-size: 13px;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* No Attachments */
.no-attachments {
    text-align: center;
    padding: 3rem 2rem;
    background: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    margin-bottom: 2rem;
    color: #9ca3af;
}

.no-attachments svg {
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-attachments p {
    font-size: 1rem;
    font-weight: 500;
}

/* Upload Section */
.upload-section {
    margin-top: 1.5rem;
}

.upload-section h4 {
    margin-bottom: 1rem;
}

/* Upload Area Override */
.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    background: #f9fafb;
    transition: all 0.3s ease;
    overflow: hidden;
    width: 100%;
}

.upload-area:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.upload-area.dragging {
    border-color: #3b82f6;
    background: #dbeafe;
}

.upload-content {
    padding: 2.5rem 2rem;
    text-align: center;
    cursor: pointer;
    width: 100%;
}

.upload-icon {
    margin-bottom: 1.5rem;
    color: #6b7280;
    transition: color 0.3s ease;
}

.upload-area:hover .upload-icon {
    color: #3b82f6;
}

.upload-text h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.upload-text p {
    color: #6b7280;
    margin-bottom: 0.75rem;
}

.upload-hint {
    font-size: 0.875rem;
    color: #9ca3af;
    display: block;
    margin-top: 0.5rem;
}

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.upload-btn:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.file-preview {
    border-top: 1px solid #e5e7eb;
    background: white;
    min-height: 200px;
    max-height: 500px;
    overflow-y: auto;
    padding: 1.5rem;
    width: 100%;
}

.no-files {
    padding: 3rem 2rem;
    text-align: center;
    color: #9ca3af;
    font-style: italic;
    font-size: 1.1rem;
    background: #f8fafc;
    border: 2px dashed #e5e7eb;
    border-radius: 8px;
    margin: 1rem 0;
}

/* Responsive */
@media (max-width: 768px) {
    .attachment-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .attachment-actions {
        width: 100%;
    }
    
    .btn-view {
        width: 100%;
        justify-content: center;
    }
    
    .upload-content {
        padding: 2rem 1rem;
    }
    
    .file-preview {
        min-height: 150px;
        max-height: 400px;
        padding: 1rem;
    }
    
    .no-files {
        padding: 2rem 1rem;
        font-size: 1rem;
    }
}
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
                <div class="form-section">
                    <h3>Attachments</h3>

                    <!-- EXISTING FILES -->
                    <?php if($attachments->num_rows > 0): ?>
                    <div class="attachment-list">
                        <h4>Current Attachments:</h4>
                        <?php 
                        // Reset pointer to start
                        $attachments->data_seek(0);
                        while($file = $attachments->fetch_assoc()): 
                        ?>
                        <div class="attachment-item">
                            <div class="attachment-info">
                                <div class="attachment-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="#6b7280" stroke-width="2"/>
                                        <path d="M14 2v6h6" stroke="#6b7280" stroke-width="2"/>
                                        <path d="M8 13h8M8 17h6" stroke="#6b7280" stroke-width="2"/>
                                    </svg>
                                </div>
                                <div class="attachment-details">
                                    <strong><?= htmlspecialchars($file['file_name']); ?></strong>
                                    <small><?= $file['file_size']; ?></small>
                                </div>
                            </div>
                            <div class="attachment-actions">
                                <!-- VIEW -->
                                <a href="<?= htmlspecialchars($file['file_path']); ?>" 
                                target="_blank" 
                                class="btn-view">
                                View
                                </a>

                                <!-- DELETE -->
                                <a href="?id=<?= $issue_id ?>&delete_file=<?= $file['id']; ?>" 
                                class="btn-delete"
                                onclick="return confirm('Yakin mau hapus file ini?')">
                                Delete
                                </a>
                            </div>
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
                            <div class="file-preview" id="file-preview-edit"></div>
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

</body>
</html>