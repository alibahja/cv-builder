<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$resume_dir = __DIR__ . '/users/';
$resume_files = [];

// Create directory if it doesn't exist
if (!file_exists($resume_dir)) {
    mkdir($resume_dir, 0755, true);
}

// Get saved resumes from database (more secure than scanning directory)
try {
    $stmt = $conn->prepare("SELECT id, file_name, created_at FROM user_resumes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resumes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $resumes = [];
}

// Process messages
$messages = [];
if (isset($_GET['saved'])) {
    $messages[] = ['type' => 'success', 'text' => 'Your resume has been saved successfully!'];
}
if (isset($_GET['error'])) {
    $messages[] = ['type' => 'error', 'text' => 'There was an error processing your request.'];
}
if (isset($_GET['deleted'])) {
    $messages[] = ['type' => 'info', 'text' => 'The resume has been deleted.'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile | Resume Maker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .alert {
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .resume-list {
            margin: 20px 0;
            padding: 0;
            list-style: none;
        }
        .resume-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        .resume-actions {
            display: flex;
            gap: 10px;
        }
        .button.small {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .no-resumes {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="index.php" class="button">Home</a>
            <a href="index.php" class="button">Create New Resume</a>
            <a href="php/logout.php" class="button secondary">Logout</a>
        </div>
    </header>

    <main class="container">
        <h1>Welcome, <?= $username ?></h1>

        <!-- Display messages -->
        <?php foreach ($messages as $message): ?>
            <div class="alert <?= $message['type'] ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endforeach; ?>

        <section class="resume-section">
            <h2>Your Saved Resumes</h2>
            
            <?php if (!empty($resumes)): ?>
                <ul class="resume-list">
                    <?php foreach ($resumes as $resume): ?>
                        <li class="resume-item">
                            <div>
                                <strong><?= htmlspecialchars($resume['file_name']) ?></strong>
                                <div class="text-muted">
                                    Saved on <?= date('M j, Y g:i a', strtotime($resume['created_at'])) ?>
                                </div>
                            </div>
                            <div class="resume-actions">
                                <a href="download_resume.php?id=<?= $resume['id'] ?>" class="button small">Download</a>
                                <a href="view_resume.php?id=<?= $resume['id'] ?>" target="_blank" class="button small secondary">View</a>
                                <a href="delete_resume.php?id=<?= $resume['id'] ?>" class="button small delete-button" 
                                   onclick="return confirm('Are you sure you want to delete this resume?')">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-resumes">
                    <p>You haven't saved any resumes yet.</p>
                    <a href="review.php" class="button">Create Your First Resume</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        // Confirm before deleting
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this resume?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>