<nav class="navbar navbar-expand-lg navbar-light bg-primary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fa fa-bars text-white"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <a aria-label="logo" class="navbar-brand mt-2 mt-lg-0" href="/">
        <img src="/resources/images/logo.png" height="50" alt="boar application" loading="lazy" />
      </a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a aria-label="dashboardbutton" class="nav-link text-white" href="/">Dashboard</a>
        </li>
      </ul>
    </div>
    <div class="d-flex align-items-center">
      <div class="dropdown">
        <a aria-label="dropdown" class="text-reset me-3 dropdown-toggle hidden-arrow" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user fa-2x text-white"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
          <li>
            <a aria-label="logout" class="dropdown-item" href="/auth/logout">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>