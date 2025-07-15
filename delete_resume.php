<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    exit('Unauthorized or missing resume ID.');
}

require_once 'includes/db.php';

$resume_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get file name
$stmt = $conn->prepare("SELECT file_name FROM user_resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resume_id, $user_id);
$stmt->execute();
$stmt->bind_result($file_name);
$stmt->fetch();
$stmt->close();

if ($file_name) {
    $filepath = __DIR__ . "/users/" . $file_name;

    // Delete file
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    // Delete DB entry
    $stmt = $conn->prepare("DELETE FROM user_resumes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $resume_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: profile.php?deleted=1");
exit;
