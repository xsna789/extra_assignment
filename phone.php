<?php require 'header.php'; ?>


<!-- Results Section -->
<?php if (isset($_SESSION['source'])): ?>
    <div class="results-container">
        <h2>Results from: <?= htmlspecialchars($_SESSION['source']) ?></h2>

        <!-- <ul class="tab-list">
                    <li class="tab-item <?= isset($_SESSION['tab_order'][0]) && $_SESSION['tab_order'][0] === 'emails' ? 'active' : '' ?>">
                        <a href="#emails" class="tab-link" data-tab="emails">Emails</a>
                    </li>
                    <li class="tab-item <?= isset($_SESSION['tab_order'][1]) && $_SESSION['tab_order'][1] === 'phones' ? 'active' : '' ?>">
                        <a href="#phones" class="tab-link" data-tab="phones">Phone Numbers</a>
                    </li>
                    <li class="tab-item <?= isset($_SESSION['tab_order'][2]) && $_SESSION['tab_order'][2] === 'images' ? 'active' : '' ?>">
                        <a href="#images" class="tab-link" data-tab="images">Image URLs</a>
                    </li>
                </ul> -->




            <div id="phones" class="tab-pane <?= isset($_SESSION['tab_order'][1]) && $_SESSION['tab_order'][1] === 'phones' ? 'active' : '' ?>">
                <?php displaySection('Phone Numbers', 'phones', 'tel:'); ?>
            </div>

        </div>

        
    </div>
<?php endif; ?>
</div>


<?php
require 'footer.php';  
?>