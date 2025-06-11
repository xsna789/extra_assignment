<?php require_once './handlers.php'; ?>
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
        
        <!-- Results Section -->
        <?php if (isset($_SESSION['source'])): ?>
            <div class="results-container">
                <h2>Results from: <?= htmlspecialchars($_SESSION['source']) ?></h2>
                
                <?php displaySection('Emails', 'emails', 'mailto:'); ?>
                <?php displaySection('Phone Numbers', 'phones', 'tel:'); ?>
                <?php displaySection('Image URLs', 'images'); ?>
                
                <?php if (isset($_SESSION['download_link'])): ?>
                    <div class="download-link">
                        <a href="<?= htmlspecialchars($_SESSION['download_link']) ?>" download>
                            <i class="fas fa-download"></i> Download File
                        </a>
                        <?php unset($_SESSION['download_link']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Image Modal -->
    <div id="image-modal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="modal-image">
        <div class="modal-caption"></div>
    </div>
    
    <script src="main.js"></script>
</body>
</html>

<?php
function displaySection($title, $sessionKey, $prefix = '') {
    if (!empty($_SESSION[$sessionKey])) {
        $count = count($_SESSION[$sessionKey]);
        echo "<div class='result-section'>
            <h3>
                $title ($count)
                <div class='toolbar'>
                    <button class='copy-btn' data-target='$sessionKey'>
                        <i class='far fa-copy'></i> Copy
                    </button>
                    <form method='post' class='inline-form'>
                        <input type='hidden' name='type' value='$sessionKey'>
                        <button type='submit' name='action' value='save' title='Save as TXT'>
                            <i class='far fa-file-alt'></i>
                        </button>
                        <button type='submit' name='action' value='csv' title='Save as CSV'>
                            <i class='far fa-file-excel'></i>
                        </button>
                        <button type='submit' name='action' value='pdf' title='Save as PDF'>
                            <i class='far fa-file-pdf'></i>
                        </button>";

        if ($sessionKey === 'images' ) {
            echo "<button type='button' class='preview-all' data-type='$sessionKey' title='Preview All'>
                    <i class='far fa-images'></i>
                </button>";
        }
        
        echo "</form>
                </div>
            </h3>
            <ul id='$sessionKey'>";
        
        foreach ($_SESSION[$sessionKey] as $item) {
            echo "<li>";
            if (filter_var($item, FILTER_VALIDATE_URL)) {
                if ($sessionKey === 'images') {
                    echo "<img src='$item' class='thumbnail' data-src='$item' alt='Extracted image'>";
                }
                echo "<a href='$item' target='_blank'>" . htmlspecialchars($item) . "</a>";
            } else {
                echo "<a href='{$prefix}{$item}'>" . htmlspecialchars($item) . "</a>";
            }
            echo "</li>";
        }
        
        echo "</ul></div>";
    }
}
?>