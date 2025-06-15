<?php require 'header.php'; ?>
<?php if (isset($_SESSION['source'])): ?>
    <div class="results-container">
        <h2>Results from: <?= htmlspecialchars($_SESSION['source']) ?></h2>
        <div class="tab-content">
            <div id="images" class="tab-pane <?= isset($_SESSION['tab_order'][2]) && $_SESSION['tab_order'][2] === 'images' ? 'active' : '' ?>">
                <?php displaySection('Image URLs', 'images'); ?>
            </div>
        </div>

<?php
endif;
require 'footer.php';  ?>