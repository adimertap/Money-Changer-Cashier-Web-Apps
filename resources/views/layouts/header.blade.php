<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand-lg" data-move-target="#navbarVerticalNav"
    data-navbar-top="combo">
    <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
        <h6 class="mb-0">Selamat Datang <span class="text-primary">{{ Auth::user()->name }}</span></h6>
    </div>
    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
        <li class="nav-item">
            <div class="theme-control-toggle fa-icon-wait px-2">
                <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox"
                    data-theme-control="theme" value="dark">
                <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title=""
                    data-bs-original-title="Switch to light theme" aria-label="Switch to light theme"><span
                        class="fas fa-sun fs-0"></span></label><label
                    class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title=""
                    data-bs-original-title="Switch to dark theme" aria-label="Switch to dark theme"><span
                        class="fas fa-moon fs-0"></span></label></div>
        </li>
      
        <li class="nav-item dropdown"><a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-xl">
                    <img class="rounded-circle" src="../falcon/assets/img/team/avatar.png" alt="">
                </div>
            </a>
            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0"
                aria-labelledby="navbarDropdownUser">
                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                    <a class="dropdown-item" href="../pages/user/profile.html">Profile &amp; account</a>
                    <div class="dropdown-divider"></div>
                    <a onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                        <div class="dropdown-item">Logout
                        <form id="logout-form" action="{{route('logout')}}" method="post" style="display: none">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </a>
                </div>
            </div>
        </li>
    </ul>
</nav>
