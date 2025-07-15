<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    exit('Unauthorized or missing resume ID.');
}

require_once 'includes/db.php';

$resume_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT file_name FROM user_resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resume_id, $user_id);
$stmt->execute();
$stmt->bind_result($file_name);
$stmt->fetch();
$stmt->close();

if (!$file_name) {
    http_response_code(404);
    exit('Resume not found.');
}

$filepath = __DIR__ . "/users/" . $file_name;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
readfile($filepath);
exit;
