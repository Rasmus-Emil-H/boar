<?php
  use app\core\Application;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title><?= $this->title; ?></title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php if(Application::isGuest()): ?>
                  <li class="nav-item">
                    <a class="nav-link" href="/about">about</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/auth">Login</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/auth/register">Register</a>
                  </li>
                <?php else: ?>
                  <li class="nav-item active">
                    <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/profile"><?= htmlspecialchars('Profile'); ?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/ticket">Submit ticket</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/posts">Posts</a>
                  </li>
                  <li class="nav-item float-right">
                    <a class="nav-link" href="/logout"><?= Application::$app->user->getDisplayName(); ?> (Logout)</a>
                  </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">
      <?php if (Application::$app->session->getFlashMessage('success')): ?>
        <div class="alert alert-success">
          <?= Application::$app->session->getFlashMessage('success'); ?>
        </div>
      <?php endif; ?>
      {{content}}
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>