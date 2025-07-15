<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university = $_POST['university'] ?? '';
    $major = $_POST['major'] ?? '';
    $gpa = $_POST['gpa'] ?? '';
    $coursework = $_POST['coursework'] ?? '';

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM education WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO education (user_id, institution, degree, gpa, coursework) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $university, $major, $gpa, $coursework);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['education'] = [
            'university' => $university,
            'major' => $major,
            'gpa' => $gpa,
            'coursework' => $coursework
        ];
    }

    header("Location: projects.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Education | Resume Maker</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <header>
    <h3>Resume Maker</h3>
    <div class="nav-buttons">
      <a href="skills.php" class="button secondary">Prev</a>
    </div>
  </header>
  <main>
    <h1>Education</h1>
    <form method="POST">
      <label class="required">University</label>
      <input type="text" name="university" required>

      <label class="required">Major</label>
      <input type="text" name="major" required>

      <label class="required">GPA</label>
      <input type="number" name="gpa" step="0.01" min="0" max="4" required>

      <label class="required">Relevant Coursework</label>
      <textarea name="coursework" required></textarea>

      <div class="button-group">
        <button type="submit" class="button">Next</button>
      </div>
    </form>
  </main>

</body>
</html>
