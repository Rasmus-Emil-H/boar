<nav class="navbar navbar-expand-lg bg-body-tertiary bg-dark text-white">
    <div class="container-fluid">
        <a class="navbar-brand" href="/home"><i class="bi bi-house"></i></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav float-end">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list"></i></a>
                    <ul class="dropdown-menu">
                        <?php \app\core\src\miscellaneous\CoreFunctions::loopAndEcho($navigationItems); ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>