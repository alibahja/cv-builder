<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titles = $_POST['project_titles'] ?? [];
    $descriptions = $_POST['project_descriptions'] ?? [];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM projects WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($titles); $i++) {
            $title = trim($titles[$i]);
            $desc = trim($descriptions[$i]);
            if (!empty($title) && !empty($desc)) {
                $stmt->bind_param("iss", $user_id, $title, $desc);
                $stmt->execute();
            }
        }
        $stmt->close();
    } else {
        $projects = [];
        for ($i = 0; $i < count($titles); $i++) {
            $projects[] = [
                'title' => trim($titles[$i]),
                'description' => trim($descriptions[$i])
            ];
        }
        $_SESSION['projects'] = $projects;
    }

    header("Location: volunteering.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | Resume Maker</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="education.php" class="button secondary">Prev</a>
        </div>
    </header>
    <main>
        <h1>Projects</h1>
        <form method="POST" id="projects-form">
            <div id="projects-container">
                <div class="project-field">
                    <label class="required">Project Title</label>
                    <input type="text" name="project_titles[]" required>

                    <label class="required">Description</label>
                    <textarea name="project_descriptions[]" required></textarea>

                    <div class="dynamic-buttons">
                        <button type="button" class="button delete-button" onclick="removeProject(this)">Delete Project</button>
                    </div>
                </div>
            </div>
            <button type="button" class="button add-button" onclick="addProject()">Add Project</button>
            <div class="button-group">
                <button type="submit" class="button">Next</button>
            </div>
        </form>
    </main>
    <script>
        function addProject() {
            const container = document.getElementById('projects-container');
            const newProject = document.createElement('div');
            newProject.className = 'project-field';
            newProject.innerHTML = `
                <label class="required">Project Title</label>
                <input type="text" name="project_titles[]" required>
                
                <label class="required">Description</label>
                <textarea name="project_descriptions[]" required></textarea>
                
                <div class="dynamic-buttons">
                    <button type="button" class="button delete-button" onclick="removeProject(this)">Delete Project</button>
                </div>
            `;
            container.appendChild(newProject);
        }

        function removeProject(button) {
            const project = button.closest('.project-field');
            const allProjects = document.querySelectorAll('.project-field');
            if (allProjects.length > 1) {
                project.remove();
            } else {
                project.querySelector('input').value = '';
                project.querySelector('textarea').value = '';
            }
        }

      
    </script>
</body>
</html>
