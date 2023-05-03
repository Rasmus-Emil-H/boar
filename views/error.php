<div class="body-height">
    <h2 class="text-center pt-4 alert alert-danger body-height w-100 center flex-column">
        <p>
            ⚒️ <?= $exception->getMessage(); ?> ⚒️
        </p>
        <p>
            Line: <?= $exception->getLine(); ?>
        </p>
    </h2>
</div>