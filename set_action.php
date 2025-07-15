<?php
session_start();

// Validate and sanitize the mode parameter
$validModes = ['save', 'download'];
$mode = isset($_GET['mode']) ? strtolower(trim($_GET['mode'])) : '';

// Check if mode is valid
if (in_array($mode, $validModes)) {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Store the action in session
    $_SESSION['template_action'] = $mode;
    
    // Redirect securely
    header("Location: template_select.php");
    exit();
} else {
    // Log the invalid attempt (in a real application, you'd log to a file or system)
    error_log("Invalid mode attempted: " . htmlspecialchars($mode));
    
    // Set proper HTTP headers
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(400); // Bad Request
    
    // Secure error message
    die("Invalid action. Please use either 'save' or 'download'.");
}

