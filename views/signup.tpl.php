<div>
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <form class="login-form center flex-column" method="POST" action="/auth/signup">
    <div class="form-group w-100 mb-3">
      <input type="email" required name="email" class="form-control" placeholder="Email" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="form-group w-100 mb-3">
      <input type="text" required name="name" class="form-control" placeholder="Name" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="form-group w-100 mb-3">
      <input type="password" required name="password" class="form-control" placeholder="Password" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <?= (new \app\core\tokens\CsrfToken())->insertHiddenToken(); ?>
    <button type="submit" class="btn btn-primary btn-lg mt-2 w-100"><?= ths('Create account'); ?></button>
    <a href="/" class="btn btn-primary btn-lg mt-2 w-100"><?= ths('Go back'); ?></a>
  </form>
</div>