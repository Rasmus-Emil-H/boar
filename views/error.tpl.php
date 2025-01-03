<?= $css[array_key_first($css)]; ?>

<div class="fullscreen error-template">
    <div class="error fullscreen">
        <h1 class="mb-4">
            ⚒️
            <?= $exception->getCode(); ?>
            👨🏼‍🔧
        </h1>
        <hr class="w-50" />
        <h4 class="mb-4 mt-4">
            <?= app()::isDevSite() ? 
                $exception->getMessage() . ' Line: ' . $exception->getLine() : 
                ths('Error'); 
            ?>
        </h4>
        <a href="<?= hs($home); ?>" class="p-2 btn btn-success"><?= ths('GO HOME'); ?></a>
    </div>
    <div class="image"></div>
</div>