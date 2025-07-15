<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';
require 'vendor/autoload.php';
use Dompdf\Dompdf;

// Validate template parameter
$template = filter_input(INPUT_GET, 'template', FILTER_VALIDATE_INT, [
    'options' => [
        'default' => 1, // Default to template 1
        'min_range' => 1,
        'max_range' => 2
    ]
]);

$user_id = $_SESSION['user_id'];
$uploads_dir = 'users';

try {
    // Create directory if it doesn't exist
    if (!file_exists($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    // Verify directory is writable
    if (!is_writable($uploads_dir)) {
        throw new Exception("Resume directory is not writable");
    }

    // Fetch user data using prepared statements
    function fetchUserData($conn, $user_id, $table, $fields = '*') {
        $stmt = $conn->prepare("SELECT $fields FROM $table WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // Personal info
    $stmt = $conn->prepare("SELECT * FROM personal_info WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $personal = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Other data
    $education = fetchUserData($conn, $user_id, 'education');
    $skills = array_column(fetchUserData($conn, $user_id, 'skills', 'skill_name'), 'skill_name');
    $projects = fetchUserData($conn, $user_id, 'projects');
    $languages = array_column(fetchUserData($conn, $user_id, 'languages', 'language_name'), 'language_name');
    $volunteering = implode("\n", array_column(fetchUserData($conn, $user_id, 'volunteering', 'activities'), 'activities'));

    // Generate HTML
    ob_start();
    $templateFile = 'templates/template' . $template . '.php';
    if (!file_exists($templateFile)) {
        throw new Exception("Template file not found");
    }
    include $templateFile;
    $html = ob_get_clean();

    // Generate PDF
    $dompdf = new Dompdf([
        'chroot' => __DIR__, // Security setting
        'isRemoteEnabled' => true
    ]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Generate unique filename
    $filename = 'resume_' . $user_id . '_' . time() . '.pdf';
    $filepath = $uploads_dir . '/' . $filename;

    // Save PDF
    file_put_contents($filepath, $dompdf->output());

    // Verify file was saved
    if (!file_exists($filepath)) {
        throw new Exception("Failed to save resume file");
    }

    // Optionally store file reference in database
    $stmt = $conn->prepare("INSERT INTO user_resumes (user_id, file_name, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $filename);
    $stmt->execute();
    $stmt->close();

    // Redirect with success message
    header("Location: profile.php?saved=1&file=" . urlencode($filename));
    exit();

} catch (Exception $e) {
    // Log error
    error_log("Save Template Error: " . $e->getMessage());
    
    // Redirect with error message
    header("Location: profile.php?error=1");
    exit();
}
