<nav>
    <div class="nav-wrapper p-2 blue lighten-1">
        <a href="/home" class="brand-logo center"><?= env('appName'); ?></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
        <?php foreach ($navigationItems as $navItem): ?>
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="<?= hs($navItem->href); ?>"><?= ths($navItem->title); ?></a>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</nav>