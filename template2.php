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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .name {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        ul {
            margin: 0;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 5px;
        }
        
        a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .date {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Name Header -->
    <div class="name">
        <?= htmlspecialchars(($personal['first_name'] ?? '') . ' ' . ($personal['last_name'] ?? '')) ?>
    </div>

    <!-- Personal Info Table -->
    <table>
        <tr>
            <th colspan="2">Personal Information</th>
        </tr>
        <tr>
            <td width="30%"><strong>Phone</strong></td>
            <td><?= htmlspecialchars($personal['phone'] ?? '') ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><a href="mailto:<?= htmlspecialchars($personal['email'] ?? '') ?>"><?= htmlspecialchars($personal['email'] ?? '') ?></a></td>
        </tr>
        <tr>
            <td><strong>LinkedIn</strong></td>
            <td><a href="<?= htmlspecialchars($personal['linkedin'] ?? '') ?>"><?= htmlspecialchars($personal['linkedin'] ?? '') ?></a></td>
        </tr>
    </table>

    <!-- Education Table -->
    <table>
        <tr>
            <th colspan="2">Education</th>
        </tr>
        <?php foreach ($education as $edu): ?>
        <tr>
            <td width="30%">
                <strong><?= htmlspecialchars($edu['degree'] ?? $edu['major'] ?? '') ?></strong>
                <div class="date"><?= htmlspecialchars($edu['year'] ?? '') ?></div>
            </td>
            <td>
                <strong><?= htmlspecialchars($edu['institution'] ?? $edu['university'] ?? '') ?></strong>
                <?php if (!empty($edu['gpa'])): ?>
                    <div>GPA: <?= htmlspecialchars($edu['gpa']) ?></div>
                <?php endif; ?>
                <?php if (!empty($edu['coursework'])): ?>
                    <div>Coursework: <?= htmlspecialchars($edu['coursework']) ?></div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Skills Table -->
    <table>
        <tr>
            <th>Skills</th>
        </tr>
        <tr>
            <td>
                <ul>
                    <?php foreach ($skills as $skill): ?>
                        <li><?= htmlspecialchars($skill) ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Projects Table -->
    <table>
        <tr>
            <th colspan="2">Projects</th>
        </tr>
        <?php foreach ($projects as $proj): ?>
        <tr>
            <td width="30%">
                <strong><?= htmlspecialchars($proj['title'] ?? '') ?></strong>
            </td>
            <td>
                <?= nl2br(htmlspecialchars($proj['description'] ?? '')) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Languages Table -->
    <table>
        <tr>
            <th>Languages</th>
        </tr>
        <tr>
            <td>
                <ul>
                    <?php foreach ($languages as $lang): ?>
                        <li><?= htmlspecialchars($lang) ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Volunteering Table -->
    <?php if (!empty($volunteering)): ?>
    <table>
        <tr>
            <th>Experience</th>
        </tr>
        <tr>
            <td>
                <?= nl2br(htmlspecialchars($volunteering)) ?>
            </td>
        </tr>
    </table>
    <?php endif; ?>
</body>
</html>

