<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $languages = $_POST['languages'] ?? [];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM languages WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO languages (user_id, language_name) VALUES (?, ?)");
        foreach ($languages as $language) {
            $lang = trim($language);
            if (!empty($lang)) {
                $stmt->bind_param("is", $user_id, $lang);
                $stmt->execute();
            }
        }
        $stmt->close();
    } else {
        $_SESSION['languages'] = $languages;
    }

    header("Location: ../review.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Languages | Resume Maker</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <header>
    <h3>Resume Maker</h3>
    <div class="nav-buttons">
      <a href="volunteering.php" class="button secondary">Prev</a>
    </div>
  </header>
  <main>
    <h1>Languages</h1>
    <form method="POST" id="languages-form">
      <div id="languages-container">
        <div class="dynamic-field">
          <label class="required">Language</label>
          <div class="input-with-button">
            <input type="text" name="languages[]" placeholder="e.g., English" required>
            <div class="dynamic-buttons">
              <button type="button" class="button delete-button" onclick="removeLanguage(this)">Delete</button>
            </div>
          </div>
        </div>
      </div>
      <button type="button" class="button add-button" onclick="addLanguage()">Add Language</button>
      
      <div class="button-group">
        <button type="submit" class="button">Submit</button>
      </div>
    </form>
  </main>
  <script>
    function addLanguage() {
      const container = document.getElementById('languages-container');
      const newLang = document.createElement('div');
      newLang.className = 'dynamic-field';
      newLang.innerHTML = `
        <label class="required">Language</label>
        <div class="input-with-button">
          <input type="text" name="languages[]" placeholder="e.g., French" required>
          <div class="dynamic-buttons">
            <button type="button" class="button delete-button" onclick="removeLanguage(this)">Delete</button>
          </div>
        </div>
      `;
      container.appendChild(newLang);
    }
    
    function removeLanguage(button) {
      const langField = button.closest('.dynamic-field');
      const allFields = document.querySelectorAll('.dynamic-field');
      if (allFields.length > 1) {
        langField.remove();
      } else {
        langField.querySelector('input').value = '';
      }
    }

  </script>
</body>
</html>
