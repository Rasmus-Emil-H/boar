<?= $css[array_key_first($css)]; ?>

<div class="fullscreen center">
    <h1>Error</h1>
    <?php if(app()::isDevSite()): ?>
        <div class="error-container">
            <h1><?= $exception->getMessage(); ?></h1>
            <pre>
                <?php var_dump($exception); ?>
            </pre>
        </div>
    <?php endif; ?>
</div>