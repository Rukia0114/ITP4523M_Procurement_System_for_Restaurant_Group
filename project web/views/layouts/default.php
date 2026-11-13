<?php
/**
 * Default Layout Template
 * Base layout for all pages
 */
// This layout would include header, nav, footer based on user type
?>

<div class="wrapper">
    <?php if (isset($nav)): ?>
        <div class="container-fluid bg-primary text-white mb-4 px-3">
            <?= $nav ?>
        </div>
    <?php endif; ?>
    
    <div class="container-fluid pt-4 px-4">
        <?= $content ?? '' ?>
    </div>

</div>

</body>
</html>
