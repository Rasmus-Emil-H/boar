<?php
  /** @var $model app\models\User */
?>

<div class="screen fade-in">
  <div class="screen__content">
      <form class="login" method="POST">
        <div class="login__field">
            <i class="login__icon fas fa-user"></i>
            <input type="text" class="login__input" name="email" placeholder="Email">
        </div>
        <div class="login__field">
            <i class="login__icon fas fa-lock"></i>
            <input type="password" class="login__input" name="password" placeholder="Password">
        </div>
        <button class="button login__submit">
          <span class="button__text w-100">Login</span>
        </button>       
      </form>
  </div>
  <div class="screen__background">
      <span class="screen__background__shape screen__background__shape4"></span>
      <span class="screen__background__shape screen__background__shape3"></span>    
      <span class="screen__background__shape screen__background__shape2"></span>
      <span class="screen__background__shape screen__background__shape1"></span>
  </div>
</div>