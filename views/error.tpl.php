<?= $css[array_key_first($css)]; ?>

<div class="fullscreen error-template">
    <div class="error fullscreen">
        <h1 class="mb-4"><?= $exception->getCode(); ?></h1>
        <hr class="w-50" />
        <?php if(app()::isDevSite()): ?>
            <h4 class="mb-4 mt-4"><?= $exception->getMessage(); ?></h4>
        <?php else: ?>
            <h4 class="mb-4">Error</h4>
        <?php endif; ?>
        <a href="<?= hs($home); ?>" class="w-50 p-2 btn btn-success">GO HOME</a>
    </div>
    <div class="image"></div>
</div>