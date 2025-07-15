<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Maker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h3>Resume Maker</h3>
        <div>
            <a href="pages/personal.php" class="button">Create CV</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="button secondary">My Profile</a>
                <a href="php/logout.php" class="button secondary">Logout</a>
            <?php else: ?>
                <a href="php/login.php" class="button secondary">Log in</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <section class="hero">
            <h1>Create your Professional CV with Resume Maker</h1>
            <a href="pages/personal.php" class="button" style="padding: 0.75rem 2rem; font-size: 1.1rem;">Create Resume</a>
        </section>

        <section>
            <h2>How Resume Maker Works</h2>
            <div class="steps-container">
                <div class="step-box">
                    <h4>1. Fill in the blanks</h4>
                    <p>Start by filling the related information for your resume.</p>
                </div>
                <div class="step-box">
                    <h4>2. Pick a template</h4>
                    <p>Choose a template that fits your preference.</p>
                </div>
                <div class="step-box">
                    <h4>3. Download your CV</h4>
                    <p>You can download your CV and edit it later.</p>
                </div>
            </div>
        </section>

        <section class="feature-boxes">
            <div class="feature-box">
                <h4>Quick and Easy Resume Builder</h4>
                <p>With our Resume Maker, anyone can create a professional resume just by following simple steps.</p>
            </div>
            <div class="feature-box">
                <h4>Higher Chances of Getting a Job</h4>
                <p>A strong and professional resume helps you stand out among other applicants.</p>
            </div>
            <div class="feature-box">
                <h4>Organize Your Goals</h4>
                <p>Create and manage multiple resumes tailored to different job applications.</p>
            </div>
        </section>
    </main>
</body>
</html>
