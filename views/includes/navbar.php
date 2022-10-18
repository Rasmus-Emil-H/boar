<?php
  use app\core\Application;
?>

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
                <a class="nav-link" href="/auth/logout"><?= Application::$app->user->getDisplayName(); ?> (Logout)</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>