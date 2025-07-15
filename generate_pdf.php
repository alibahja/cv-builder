<?php
require './vendor/autoload.php';
use Dompdf\Dompdf;

session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Validate template parameter
$template = filter_input(INPUT_GET, 'template', FILTER_VALIDATE_INT, [
    'options' => [
        'default' => 1, // Default to template 1
        'min_range' => 1,
        'max_range' => 2
    ]
]);

require_once 'includes/db.php';

// Initialize variables
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$personal = [];
$education = [];
$skills = [];
$projects = [];
$languages = [];
$volunteering = '';

try {
    // Fetch data based on login status
    if ($is_logged_in && $user_id) {
        // Use prepared statements for security
        $stmt = $conn->prepare("SELECT * FROM personal_info WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $personal = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Function to fetch data as array
        function fetchData($conn, $user_id, $table, $fields = '*') {
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

        $education = fetchData($conn, $user_id, 'education');
        $skills = array_column(fetchData($conn, $user_id, 'skills', 'skill_name'), 'skill_name');
        $projects = fetchData($conn, $user_id, 'projects');
        $languages = array_column(fetchData($conn, $user_id, 'languages', 'language_name'), 'language_name');
        $volunteering = implode("\n", array_column(fetchData($conn, $user_id, 'volunteering', 'activities'), 'activities'));
    } else {
        // Session data for non-logged-in users
        $personal = $_SESSION['personal_info'] ?? [];
        $education = $_SESSION['education'] ?? [];
        
        // Ensure education is always array of arrays
        if (!empty($education) && !isset($education[0])) {
            $education = [$education];
        }
        
        $skills = $_SESSION['skills'] ?? [];
        $projects = $_SESSION['projects'] ?? [];
        
        // Ensure projects is always array of arrays
        if (!empty($projects) && !isset($projects[0])) {
            $projects = [$projects];
        }
        
        $languages = $_SESSION['languages'] ?? [];
        $volunteering = $_SESSION['volunteering']['activities'] ?? '';
    }

    // Capture template output
    ob_start();
    $templateFile = 'templates/template' . $template . '.php';
    if (file_exists($templateFile)) {
        include $templateFile;
    } else {
        throw new Exception("Template file not found");
    }
    $html = ob_get_clean();

    // Generate PDF
    $dompdf = new Dompdf([
        'chroot' => __DIR__, // Security setting
        'isRemoteEnabled' => true // Allow loading external resources if needed
    ]);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Generate filename
    $firstName = $personal['first_name'] ?? 'user';
    $lastName = $personal['last_name'] ?? '';
    $filename = "resume_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $firstName) . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $lastName) . ".pdf";

    // Output PDF
    $dompdf->stream($filename, [
        "Attachment" => true,
        "compress" => true
    ]);

} catch (Exception $e) {
    // Log error (in production, log to a file)
    error_log("PDF Generation Error: " . $e->getMessage());
    
    // User-friendly error message
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    die("An error occurred while generating your resume. Please try again later.");
}

