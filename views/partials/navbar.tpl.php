<nav class="navbar navbar-expand-lg navbar-light bg-primary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fa fa-bars text-white"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php foreach ( $navigationItems as $navItem => $classes): ?>
          <li class="nav-item text-white <?= implode(' ', $classes); ?>">
            <?= $navItem; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>