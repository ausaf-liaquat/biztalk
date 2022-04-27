<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index') }}">
                <i class="mdi mdi-account-multiple-outline menu-icon"></i>
                <span class="menu-title">User Management</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('video.index') }}">
                <i class="mdi mdi-file-video menu-icon"></i>
                <span class="menu-title">Video Management</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('banners.index') }}">
                <i class="mdi mdi-image-area menu-icon"></i>
                <span class="menu-title">Banners</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('hashtags.index') }}">
                <i class="fa-solid fa-hashtag menu-icon"></i>
                <span class="menu-title">Hashtag</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('category.index') }}">
                <i class="fa-solid fa-square-plus menu-icon"></i>
                <span class="menu-title">Categories</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-information menu-icon"></i>
                <span class="menu-title">Tickets System</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fa-solid fa-ban menu-icon"></i>
                <span class="menu-title">Reported User</span>
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-video-off menu-icon"></i>
                <span class="menu-title">Flaged Media</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-settings-box menu-icon"></i>
                <span class="menu-title">Settings</span>
            </a>
        </li> --}}
    </ul>
</nav>
