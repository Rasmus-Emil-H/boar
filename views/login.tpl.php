<div class="screen fade-in">
  <div class="screen__content">
      <form class="login" method="POST" id="login-form">
        <div class="login__field">
            <i class="login__icon fas fa-user"></i>
            <input type="password" class="login__input" required name="email" placeholder="Email" />
            <input type="password" class="login__input" required name="password" placeholder="Password" />
        </div>
        <button class="button login__submit" id="submit">Login</button>
        <?= (new app\core\tokens\CsrfToken())->insertHiddenToken(); ?>
      </form>
  </div>
  <div class="screen__background">
      <span class="screen__background__shape screen__background__shape1"></span>
  </div>
</div>
