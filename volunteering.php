<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activities = trim($_POST['activities'] ?? '');

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM volunteering WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO volunteering (user_id, activities) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $activities);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['volunteering'] = [
            'activities' => $activities
        ];
    }

    header("Location: languages.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteering | Resume Maker</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="projects.php" class="button secondary">Prev</a>
        </div>
    </header>
    <main>
        <h1>Experience</h1>
        <form method="POST">
            <label class="required">Participated Activities</label>
            <textarea name="activities" required></textarea>
            <div class="button-group">
                <button type="submit" class="button">Next</button>
            
            </div>
        </form>
    </main>
</body>
</html>
