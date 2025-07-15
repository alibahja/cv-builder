<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Validate session and referrer
if (!isset($_SESSION['template_action']) || !isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'review.php') === false) {
    header("Location: review.php");
    exit;
}

// Sanitize action
$action = $_SESSION['template_action'] === 'download' ? 'download' : 'save';
$templates = [
    1 => ['name' => 'Template 1', 'image' => 'templates/template00.png'],
    2 => ['name' => 'Template 2', 'image' => 'templates/template0.png']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Select Template | Resume Maker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .template-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .template-box {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .template-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .template-box img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #eee;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
        }
        .template-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            cursor: pointer;
        }
        .template-modal img {
            max-width: 90vw;
            max-height: 90vh;
            border-radius: 10px;
            border: 2px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        @media (max-width: 768px) {
            .template-container {
                flex-direction: column;
                align-items: center;
            }
            .template-box {
                width: 90%;
            }
        }
    </style>
</head>
<body>
<header>
    <h3>Resume Maker</h3>
    <nav>
        <a href="review.php" class="button secondary">Back to Review</a>
    </nav>
</header>

<main>
    <h1>Select a Template</h1>
    
    <div class="template-container">
        <?php foreach ($templates as $id => $template): ?>
        <div class="template-box">
            <h2><?= htmlspecialchars($template['name']) ?></h2>
            <img src="<?= htmlspecialchars($template['image']) ?>" 
                 alt="<?= htmlspecialchars($template['name']) ?> preview" 
                 onclick="openModal('<?= htmlspecialchars($template['image']) ?>')">
            <div class="button-group">
                <button class="button secondary" onclick="openModal('<?= htmlspecialchars($template['image']) ?>')">
                    View Template
                </button>
                <a class="button" href="<?= $action === 'download' ? "generate_pdf.php?template=$id" : "save_template.php?template=$id" ?>">
                    Choose Template
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<div id="modal" class="template-modal" style="display:none" onclick="closeModal()">
    <img id="modal-img" src="" alt="Template preview">
</div>

<script>
    // Modal functionality
    function openModal(src) {
        const modal = document.getElementById('modal');
        const modalImg = document.getElementById('modal-img');
        
        modalImg.src = src;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
</body>
</html>

