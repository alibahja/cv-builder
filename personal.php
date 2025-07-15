<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM personal_info WHERE user_id = $user_id");
        $stmt = $conn->prepare("INSERT INTO personal_info (user_id, first_name, last_name, email, phone, linkedin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $first_name, $last_name, $email, $phone, $linkedin);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['personal_info'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'linkedin' => $linkedin
        ];
    }

    header("Location: skills.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information | Resume Maker</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="../index.php" class="button secondary">Prev</a>
        </div>
    </header>

    <main>
        <h1>Personal Information</h1>
        <form method="POST">
            <label class="required">First Name</label>
            <input type="text" name="first_name" required>
            <br>
            <label class="required">Last Name</label>
            <input type="text" name="last_name" required>
            <br>
            <label>Phone Number</label>
            <input type="tel" name="phone" pattern="[0-9]{2}/[0-9]{3}/[0-9]{3}" 
                   placeholder="00/000/000" title="Format: 00/000/000">
            <br>
            <label class="required">Email Address</label>
            <input type="email" name="email" required>
            <br>
            <label>LinkedIn</label>
            <input type="url" name="linkedin" placeholder="https://linkedin.com/in/username">
            
            <div class="button-group">
                <button type="submit" class="button">Next</button>
                
            </div>
        </form>
    </main>

</body>
</html>
