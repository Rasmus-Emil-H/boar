<div class="error-container">
    <h2><?= $exception->getMessage(); ?></h2>
    <?php if($isDev): ?>
        <p>Line: <?= $exception->getLine(); ?></p>
    <?php endif; ?>
    <a href="/" class="btn btn-primary w-100"><?= 'Go back'; ?></a>
</div>