<div>
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <form class="login-form center flex-column">
    <h1 class="header mb-4"><?= hs('B🐗AR') ?></h1>
    <div class="form-group w-100 mb-3">
      <input type="email" required name="email" class="form-control" placeholder="Email" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="form-group w-100 mb-3">
      <input type="text" required name="password" class="form-control" placeholder="Password" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <?= (new \app\core\tokens\CsrfToken())->insertHiddenToken(); ?>
    <button type="submit" class="btn btn-primary btn-lg mt-2 w-100"><?= ths('Log in'); ?></button>
    <a href="signup" class="btn btn-primary btn-lg mt-2 w-100"><?= ths('Create account'); ?></a>
  </form>
</div>