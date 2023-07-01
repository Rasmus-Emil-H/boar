<p><?= $exception->getMessage(); ?></p>
<?php if($isDev): ?>
    <p>Line: <?= $exception->getLine(); ?></p>
<?php endif; ?>