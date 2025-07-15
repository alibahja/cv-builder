<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = $_POST['skills'] ?? [];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM skills WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name) VALUES (?, ?)");
        foreach ($skills as $skill) {
            $skill = trim($skill);
            if (!empty($skill)) {
                $stmt->bind_param("is", $user_id, $skill);
                $stmt->execute();
            }
        }
        $stmt->close();
    } else {
        $_SESSION['skills'] = $skills;
    }

    header("Location: education.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills | Resume Maker</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="personal.php" class="button secondary">Prev</a>
        </div>
    </header>
    <main>
        <h1>Skills</h1>
        <form method="POST" id="skills-form">
            <div id="skills-container">
                <div class="dynamic-field">
                    <input type="text" name="skills[]" placeholder="Enter a skill" required>
                    <div class="dynamic-buttons">
                        <button type="button" class="button delete-button" onclick="removeField(this)">Delete</button>
                    </div>
                </div>
            </div>
            <button type="button" class="button add-button" onclick="addSkill()">Add Skill</button>
            <div class="button-group">
                <button type="submit" class="button">Next</button>
        
            </div>
        </form>
    </main>
    <script>
        function addSkill() {
            const container = document.getElementById('skills-container');
            const newSkill = document.createElement('div');
            newSkill.className = 'dynamic-field';
            newSkill.innerHTML = `
                <input type="text" name="skills[]" placeholder="Enter another skill" required>
                <div class="dynamic-buttons">
                    <button type="button" class="button delete-button" onclick="removeField(this)">Delete</button>
                </div>
            `;
            container.appendChild(newSkill);
        }

        function removeField(button) {
            const field = button.closest('.dynamic-field');
            if (document.querySelectorAll('.dynamic-field').length > 1) {
                field.remove();
            } else {
                field.querySelector('input').value = '';
            }
        }

       
    </script>
</body>
</html>
