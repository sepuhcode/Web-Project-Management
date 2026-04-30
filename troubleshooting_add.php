<?php
require_once 'config.php';

$project_id = $_GET['project_id'] ?? null;

// ambil project
$project = null;
if ($project_id) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
}

// ================== POST ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // INSERT ISSUE
    $stmt = $conn->prepare("
        INSERT INTO issues 
        (project_id, issue_type, area, description, resolution, priority, status, error_code, system_logs, network_status, root_cause, reported_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isssssssssss",
        $_POST['project_id'],
        $_POST['issue_type'],
        $_POST['area'],
        $_POST['description'],
        $_POST['resolution'],
        $_POST['priority'],
        $_POST['status'],
        $_POST['error_code'],
        $_POST['system_logs'],
        $_POST['network_status'],
        $_POST['root_cause'],
        $_POST['reported_date']
    );

    $stmt->execute();
    $issue_id = $conn->insert_id;

    // ================== UPLOAD ==================
    $upload_dir = "uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['files']['name'][0])) {

        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {

            $original = $_FILES['files']['name'][$key];
            $size = $_FILES['files']['size'][$key];

            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg','png','pdf','doc','docx','xls','xlsx','zip','txt','log'];

            if (!in_array($ext, $allowed)) continue;

            $new_name = time().'_'.$original;
            $path = $upload_dir.$new_name;

            if (move_uploaded_file($tmp_name, $path)) {

                $size_kb = round($size / 1024, 2) . " KB";

                $stmt2 = $conn->prepare("
                    INSERT INTO attachments 
                    (related_type, related_id, file_name, file_path, file_size)
                    VALUES ('issue', ?, ?, ?, ?)
                ");

                $stmt2->bind_param("isss",
                    $issue_id,
                    $original,
                    $path,
                    $size_kb
                );

                $stmt2->execute();
            }
        }
    }

    header("Location: troubleshooting.php?project_id=".$_POST['project_id']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Troubleshooting</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="upload-enhanced.css">
<style>



</style>
</head>

<body>

<header class="navbar">
<div class="nav-container">
    <div class="logo">
        <h1>Project Team Report</h1>
    </div>
</div>
</header>

<main class="dashboard">

<nav class="breadcrumb">
<a href="index.php">Home</a>
<span class="breadcrumb-separator">></span>

<a href="all_projects.php">Active Projects</a>
<span class="breadcrumb-separator">></span>

<?php if($project): ?>
<a href="project_details.php?id=<?= $project['id']; ?>">
<?= htmlspecialchars($project['name']); ?>
</a>
<span class="breadcrumb-separator">></span>

<a href="troubleshooting.php?project_id=<?= $project['id']; ?>">Troubleshooting</a>
<span class="breadcrumb-separator">></span>
<?php endif; ?>

<span class="breadcrumb-current">Add New Issue</span>
</nav>

<section class="edit-form">
<div class="form-card">

<h2>Add New Troubleshooting</h2>

<form method="POST" enctype="multipart/form-data" class="task-form">

<input type="hidden" name="project_id" value="<?= $project_id ?>">

<div class="form-grid">

<div class="form-group">
<label>Issue Type *</label>
<input type="text" name="issue_type" required>
</div>

<div class="form-group">
<label>Area *</label>
<input type="text" name="area" required>
</div>

<div class="form-group">
<label>Priority *</label>
<select name="priority" required>
<option value="">Select</option>
<option value="low">Low</option>
<option value="medium">Medium</option>
<option value="high">High</option>
<option value="critical">Critical</option>
</select>
</div>

<div class="form-group">
<label>Status</label>
<select name="status">
<option value="pending">Pending</option>
<option value="in-progress">In Progress</option>
<option value="completed">Completed</option>
</select>
</div>

<div class="form-group">
<label>Reported Date *</label>
<input type="date" name="reported_date" required>
</div>


<div class="form-group full-width">
<label>Description *</label>
<textarea name="description" rows="4"></textarea>
</div>

<div class="form-group full-width">
<label>Resolution *</label>
<textarea name="resolution" rows="4"></textarea>
</div>

</div>

    <!-- DIAGNOSTIC -->
    <div class="form-group">
        <h3>Diagnostic Information</h3>

        <div class="form-grid">
        <input type="text" name="error_code" placeholder="Error Code">
        <input type="text" name="system_logs" placeholder="System Logs">
        <input type="text" name="network_status" placeholder="Network Status">
        <input type="text" name="root_cause" placeholder="Root Cause">
        </div>
    </div>

<div class="form-section">

    <h3>Attachments</h3>

    <!-- UPLOAD AREA -->
    <div class="upload-section">
        <h4>Add Files:</h4>
        <div class="upload-area" id="uploadArea">
            <input 
                type="file" 
                name="files[]" 
                id="file-input" 
                multiple 
                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.txt,.log"
                hidden
            >

            <div class="upload-content" id="uploadTrigger">
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

            <div class="" id="file-preview"></div>
        </div>
    </div>
    <!-- ACTION BUTTON -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Add Issue</button>
        <button type="button" class="btn btn-secondary"
            onclick="window.location.href='troubleshooting.php?project_id=<?= $project_id ?>'">
            Cancel
        </button>
    </div>
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
const input = document.getElementById('file-input');
const preview = document.getElementById('file-preview');
const trigger = document.getElementById('uploadTrigger');
const area = document.getElementById('uploadArea');

let filesArray = [];

trigger.onclick = () => input.click();

input.onchange = e => {
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
    
    filesArray = filesArray.concat(validFiles);
    render();
};

area.ondragover = e => {
    e.preventDefault();
    area.classList.add('dragging');
};

area.ondragleave = () => area.classList.remove('dragging');

area.ondrop = e => {
    e.preventDefault();
    area.classList.remove('dragging');
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
    
    filesArray = filesArray.concat(validFiles);
    render();
};

function render(){
    preview.innerHTML = '';
    
    if (filesArray.length === 0) {
        preview.innerHTML = '<div class="no-files">No files selected</div>';
        return;
    }
    
    // Create file list container
    const file_list = document.createElement('div');
    file_list.className = 'file-list';
    
    filesArray.forEach((f,i)=>{
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
            <button type="button" class="remove-btn" onclick="removeFile(${i})" title="Remove file">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2.5"/>
                    <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2.5"/>
                </svg>
            </button>
        </div>`;
    });
    
    preview.appendChild(file_list);
}

function removeFile(i){
    filesArray.splice(i,1);
    render();
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
render();
</script>

</body>
</html>