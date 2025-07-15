<?php
session_start();
require_once './includes/db.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Function to convert result object to array
function resultToArray($result) {
    $array = [];
    while ($row = $result->fetch_assoc()) {
        $array[] = $row;
    }
    return $array;
}

// Fetch and normalize data
if ($is_logged_in) {
    // Get data from database
    $personal = $conn->query("SELECT * FROM personal_info WHERE user_id = $user_id ORDER BY id DESC LIMIT 1")->fetch_assoc();
    
    // Convert all results to arrays
    $education = resultToArray($conn->query("SELECT * FROM education WHERE user_id = $user_id"));
    $skills_result = resultToArray($conn->query("SELECT * FROM skills WHERE user_id = $user_id"));
    $projects = resultToArray($conn->query("SELECT * FROM projects WHERE user_id = $user_id"));
    $languages_result = resultToArray($conn->query("SELECT * FROM languages WHERE user_id = $user_id"));
    $volunteering_result = resultToArray($conn->query("SELECT * FROM volunteering WHERE user_id = $user_id"));
    
    // Normalize to match non-logged-in structure
    $skills = array_column($skills_result, 'skill_name');
    $languages = array_column($languages_result, 'language_name');
    $volunteering = implode("\n", array_column($volunteering_result, 'activities'));
} else {
    // Get data from session
    $personal = $_SESSION['personal_info'] ?? null;
    $education = $_SESSION['education'] ?? [];
    $skills = $_SESSION['skills'] ?? [];
    $projects = $_SESSION['projects'] ?? [];
    $languages = $_SESSION['languages'] ?? [];
    $volunteering = $_SESSION['volunteering']['activities'] ?? '';
    
    // Ensure consistent array structure
    if (!empty($education) && !isset($education[0])) {
        $education = [$education];
    }
    if (!empty($projects) && !isset($projects[0])) {
        $projects = [$projects];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Resume | Resume Maker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        section {
            margin-bottom: 25px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        h2 {
            color: #3498db;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 5px;
        }
        .button {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .button.secondary {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>
<header>
    <h3>Resume Maker</h3>
    <div class="nav-buttons">
        <a href="<?= $is_logged_in ? 'set_action.php?mode=save' : 'php/login.php?redirect=set_action.php?mode=save' ?>" class="button secondary">Save to Profile</a>
        <a href="set_action.php?mode=download" class="button">Download Resume</a>
    </div>
</header>

<main>
    <h1>Your Resume</h1>

    <?php if ($personal): ?>
    <section>
        <h2>Personal Info</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($personal['first_name'] . ' ' . $personal['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($personal['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($personal['phone']) ?></p>
        <?php if (!empty($personal['linkedin'])): ?>
        <p><strong>LinkedIn:</strong> <?= htmlspecialchars($personal['linkedin']) ?></p>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($skills)): ?>
    <section>
        <h2>Skills</h2>
        <ul>
            <?php foreach ($skills as $skill): ?>
                <li><?= htmlspecialchars($skill) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($education)): ?>
    <section>
        <h2>Education</h2>
        <ul>
            <?php foreach ($education as $edu): ?>
                <li>
                    <strong><?= htmlspecialchars($edu['institution'] ?? $edu['university'] ?? '') ?></strong>
                    <?php if (!empty($edu['degree'] ?? $edu['major'] ?? '')): ?>
                        — <?= htmlspecialchars($edu['degree'] ?? $edu['major'] ?? '') ?>
                    <?php endif; ?>
                    <?php if (!empty($edu['gpa'])): ?>
                        (GPA: <?= htmlspecialchars($edu['gpa']) ?>)
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($projects)): ?>
    <section>
        <h2>Projects</h2>
        <ul>
            <?php foreach ($projects as $proj): ?>
                <li>
                    <strong><?= htmlspecialchars($proj['title']) ?>:</strong>
                    <?= htmlspecialchars($proj['description']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($languages)): ?>
    <section>
        <h2>Languages</h2>
        <ul>
            <?php foreach ($languages as $lang): ?>
                <li><?= htmlspecialchars($lang) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($volunteering)): ?>
    <section>
        <h2>Experience</h2>
        <p><?= nl2br(htmlspecialchars($volunteering)) ?></p>
    </section>
    <?php endif; ?>
</main>
</body>
</html>

