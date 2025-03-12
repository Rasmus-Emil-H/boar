<div class="container">
    <div class="card">
        <form class="card-content" method="POST">
            <h4 class="header"><?= hs(app()->getConfig()->get('appName')); ?></h4>
            <div class="form-group w-100 mb-3">
                <input type="email" required name="email" class="form-control" autofocus placeholder="Email" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="form-group w-100 mb-3">
                <input type="text" required name="name" class="form-control" placeholder="Name" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="form-group w-100 mb-3">
                <input type="password" required name="password" class="form-control" placeholder="Password" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?= CSRFTokenInput(); ?>
            <button type="submit" class="btn"><?= ths('Create account'); ?></button>
            <a href="/" class="btn"><?= ths('Go back'); ?></a>
        </form>
    </div>
</div>