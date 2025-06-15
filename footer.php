
<!-- Image Modal -->
<div id="image-modal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modal-image">
    <div class="modal-caption"></div>
</div>
<?php if (isset($_SESSION['download_link'])): ?>
    <div class="download-link">
        <a href="<?= htmlspecialchars($_SESSION['download_link']) ?>" download>
            <i class="fas fa-download"></i> Download File
        </a>
        <?php unset($_SESSION['download_link']); ?>
    </div>
<?php endif; ?>
<script src="main.js"></script>
</body>

</html>