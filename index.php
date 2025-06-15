<?php require 'header.php'; ?>
<?php if (isset($_SESSION['source'])): ?>
    <div class="results-container">
        <h2>Results from: <?= htmlspecialchars($_SESSION['source']) ?></h2>
<div class="tab-content">
    <div id="emails" class="tab-pane <?= isset($_SESSION['tab_order'][0]) && $_SESSION['tab_order'][0] === 'emails' ? 'active' : '' ?>">
        <?php displaySection('Emails', 'emails', 'mailto:'); ?>
    </div>
</div>
<?php endif; ?>
<?php require 'footer.php'; ?>