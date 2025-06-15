<?php require_once 'handlers.php'; 
// $_SESSION['source'] = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Image, Email & Phone Numbers </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <!-- Theme Toggle -->
        <div class="theme-toggle">
            <button id="theme-toggle-btn"><i class="fas fa-moon"></i></button>
        </div>

        <h1>Find Image, Email & Phone Numbers </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Main Form -->
        <div id="drop-area" class="drop-zone">
            <form method="post" id="main-form">
                <textarea name="input" id="input-text" placeholder="Enter URL..." autofocus><?=
                                                                                            htmlspecialchars($_POST['input'] ?? '') ?></textarea>
                <div class="buttons">
                    <button type="submit" name="action" value="process">
                        <i class="fas fa-search"></i> Find Data
                    </button>
                    <button type="submit" name="action" value="clear" class="secondary">
                        <i class="fas fa-trash"></i> Reset
                    </button>
                </div>
            </form>
        </div>
        <div class="tab-content">
            <!-- Tabs List -->
            <ul class="tab-list">
                <li class="tab-item" <?= isset($_SESSION['tab_order'][0]) && $_SESSION['tab_order'][0] === 'emails' ? 'active' : '' ?>">
                    <a href="index.php" class="tab-link" data-tab="emails">Emails</a>
                </li>
                <li class="tab-item" <?= isset($_SESSION['tab_order'][1]) && $_SESSION['tab_order'][1] === 'phones' ? 'active' : '' ?>">
                    <a href="phone.php" class="tab-link" data-tab="phones">Phone Numbers</a>
                </li>
                <li class="tab-item" <?= isset($_SESSION['tab_order'][2]) && $_SESSION['tab_order'][2] === 'images' ? 'active' : '' ?>">
                    <a href="image.php" class="tab-link" data-tab="images">Image URLs</a>
                </li>
            </ul>
        </div>

           