<?php
// Ensure variables are set
$personal = $personal ?? [];
$skills = $skills ?? [];
$education = $education ?? [];
$projects = $projects ?? [];
$languages = $languages ?? [];
$volunteering = $volunteering ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume</title>
    <style>
        body {
            font-family: Georgia, serif; /* Use Georgia throughout */
            line-height: 1.6;
            margin: 40px;
            color: black;
            font-size: 12px; /* Default size for content under categories */
        }
        .header {
            margin-bottom: 10px;
        }
        .name {
            font-family: Georgia, serif;
            font-size: 22px; /* Name: 22px */
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        .contact {
            font-family: Georgia, serif;
            font-size: 11px; /* Contact: 11px */
            color: black;
            text-align: center;
        }
        h2 {
            font-family: Georgia, serif;
            font-size: 12px; /* Category Title: 12px */
            color:rgba(12, 13, 14, 0.92);
            border-bottom: 2px solid black;
            padding-bottom: 5px;
            margin: 10px 0 10px 0;
        }
        ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        li {
            margin-bottom: 3px;
        }
        .project-title {
            font-weight: bold;
        }
        .education-item {
            margin-bottom: 10px;
        }
        .institution {
            font-weight: bold;
        }
        /* Override defaults for paragraphs or other text if needed */
        p, div {
            font-family: Georgia, serif;
            font-size: 10.5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="name">
            <?= htmlspecialchars(($personal['first_name'] ?? '') . ' ' . ($personal['last_name'] ?? '')) ?>
        </div>
        <div class="contact">
            <?= htmlspecialchars($personal['phone'] ?? '') ?> |
            <?= htmlspecialchars($personal['email'] ?? '') ?> |
            <?= !empty($personal['linkedin']) ? htmlspecialchars($personal['linkedin']) : '' ?>
        </div>
    </div>

    <section>
        <h2>SKILLS</h2>
        <ul>
            <?php foreach ($skills as $skill): ?>
                <li><?= htmlspecialchars($skill) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>EDUCATION</h2>
        <?php foreach ($education as $edu): ?>
            <div class="education-item">
                <div class="institution"><?= htmlspecialchars($edu['institution'] ?? $edu['university'] ?? '') ?></div>
                <div><?= htmlspecialchars($edu['degree'] ?? $edu['major'] ?? '') ?></div>
                <?php if (!empty($edu['gpa'])): ?>
                    <div>GPA: <?= htmlspecialchars($edu['gpa']) ?></div>
                <?php endif; ?>
                <?php if (!empty($edu['coursework'])): ?>
                    <div>Relevant Coursework: <?= htmlspecialchars($edu['coursework']) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section>
        <h2>PROJECTS</h2>
        <?php foreach ($projects as $proj): ?>
            <div style="margin-bottom: 15px;">
                <div class="project-title"><?= htmlspecialchars($proj['title'] ?? '') ?></div>
                <div><?= htmlspecialchars($proj['description'] ?? '') ?></div>
            </div>
        <?php endforeach; ?>
    </section>

    <?php if (!empty($languages)): ?>
    <section>
        <h2>LANGUAGES</h2>
        <ul>
            <?php foreach ($languages as $lang): ?>
                <li><?= htmlspecialchars($lang) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($volunteering)): ?>
    <section>
        <h2>EXPERIENCE</h2>
        <p><?= nl2br(htmlspecialchars($volunteering)) ?></p>
    </section>
    <?php endif; ?>
</body>
</html>
