<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'team_project';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Helper functions
function sanitize($conn, $data) {
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d M Y H:i', strtotime($datetime));
}

function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'in-progress' => '<span class="badge bg-info">In Progress</span>',
        'completed' => '<span class="badge bg-success">Completed</span>',
        'active' => '<span class="badge bg-primary">Active</span>',
        'on-hold' => '<span class="badge bg-secondary">On Hold</span>'
    ];
    return $badges[$status] ?? $status;
}

function getPriorityBadge($priority) {
    $badges = [
        'low' => '<span class="badge bg-success">Low</span>',
        'medium' => '<span class="badge bg-warning">Medium</span>',
        'high' => '<span class="badge bg-danger">High</span>',
        'critical' => '<span class="badge bg-dark">Critical</span>'
    ];
    return $badges[$priority] ?? $priority;
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}
?>
