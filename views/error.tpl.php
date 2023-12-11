<div class="error-container fill-node error" style="background-color: #d62828; color: white !important; padding: 2rem; border-radius: 4px; margin: 0;">
    <h2><?= $exception->getMessage(); ?></h2>
    <?php if(app()::isDevSite()): ?>
        <pre><?php var_dump($exception); ?></pre>
    <?php endif; ?>
    <a href="/" class="btn btn-primary w-100"><?= 'Go back'; ?></a>
</div>