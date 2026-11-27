<nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Buka atau tutup menu">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <div class="hud-system" aria-hidden="true">
        <span class="hud-status-dot"></span>
        SYSTEM ONLINE
    </div>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item hud-user">
            <span>{{ auth()->user()->name }}</span>
            <small>{{ ucfirst(auth()->user()->role) }} // access</small>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" aria-label="Layar penuh">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a href="/logout" class="nav-link" aria-label="Keluar">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>
