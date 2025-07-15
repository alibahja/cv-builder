<?php
session_start();
require_once '../includes/db.php';

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$message = "";
$formData = ['username' => '', 'email' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"] ?? '';
    
    // Store form data for repopulation
    $formData = [
        'username' => htmlspecialchars($username),
        'email' => htmlspecialchars($email)
    ];

    // Validate inputs
    $errors = [];
    
    if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors['username'] = "Username must be 3-20 characters (letters, numbers, underscores)";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }
    
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Check if username/email exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['general'] = "Username or email already exists";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Resume Maker</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .register-form input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .register-form input.error {
            border-color: #e74c3c;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: block;
        }
        .register-form button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 1rem;
        }
        .register-form button:hover {
            background-color: #27ae60;
        }
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        .register-footer a {
            color: #3498db;
            text-decoration: none;
        }
        .register-footer a:hover {
            text-decoration: underline;
        }
        .password-hint {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div class="nav-buttons">
            <a href="../index.php" class="button secondary">Home</a>
            <a href="login.php" class="button">Login</a>
        </div>
    </header>

    <main>
        <div class="register-container">
            <div class="register-header">
                <h1>Create Account</h1>
            </div>

            <?php if (isset($errors['general'])): ?>
                <div class="alert error">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert success">
                    Registration successful! Please log in.
                </div>
            <?php endif; ?>

            <form class="register-form" method="POST" novalidate>
                <div>
                    <input type="text" name="username" placeholder="Username" 
                           value="<?= $formData['username'] ?>" 
                           <?= isset($errors['username']) ? 'class="error"' : '' ?> 
                           required>
                    <?php if (isset($errors['username'])): ?>
                        <span class="error-message"><?= $errors['username'] ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <input type="email" name="email" placeholder="Email Address" 
                           value="<?= $formData['email'] ?>" 
                           <?= isset($errors['email']) ? 'class="error"' : '' ?> 
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <input type="password" name="password" placeholder="Password" 
                           <?= isset($errors['password']) ? 'class="error"' : '' ?> 
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?= $errors['password'] ?></span>
                    <?php else: ?>
                        <span class="password-hint">Minimum 8 characters</span>
                    <?php endif; ?>
                </div>

                <div>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" 
                           <?= isset($errors['confirm_password']) ? 'class="error"' : '' ?> 
                           required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error-message"><?= $errors['confirm_password'] ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit">Register</button>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Log in here</a></p>
            </div>
        </div>
    </main>
</body>
</html>
