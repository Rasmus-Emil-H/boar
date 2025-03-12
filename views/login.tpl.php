<div class="container">
    <div class="card">
        <form class="card-content" method="POST" action="/auth/login">
            <h4 class="header"><?= hs(app()->getConfig()->get('appName')); ?></h4>
            <div class="input-field">
                <input id="email" type="email" name="email" required autofocus>
                <label for="email">Email</label>
            </div>

            <div class="input-field">
                <input id="password" type="password" name="password" required>
                <label for="password">Password</label>
            </div>

            <?= (new \app\core\src\tokens\CsrfToken())->insertHiddenToken(); ?>
            
            <button type="submit" class="btn green darken-2 waves-effect waves-light"><?= ths('Log in'); ?></button>
            
            <a href="signup" class="btn blue waves-effect waves-light"><?= ths('Create account'); ?></a>
        </form>
    </div>
</div>