<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="#" class="logo">
                <img src="assets/img/kaiadmin/logo_amb.svg" alt="navbar brand" class="navbar-brand" height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item">
                    <a href="{{ url('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Master Data</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/kendaraan') }}">
                        <i class="fas fa-car-side"></i>
                        <p>Kendaraan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/proyek') }}">
                        <i class="fas fa-warehouse"></i>
                        <p>Proyek</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/ac') }}">
                        <i class="fas fa-snowflake"></i>
                        <p>AC</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/bbm') }}">
                        <i class="fas fa-burn"></i>
                        <p>BBM</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/bubut') }}">
                        <i class="fas fa-screwdriver"></i>
                        <p>Bubut</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/cat') }}">
                        <i class="fas fa-paint-roller"></i>
                        <p>Cat</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/operasional') }}">
                        <i class="fas fa-chart-pie"></i>
                        <p>Operasional</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#pembangunan">
                        <i class="fas fa-hotel"></i>
                        <p>Pembangunan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="pembangunan">
                        <ul class="nav nav-collapse">
                            <li class="nav-item">
                                <a data-bs-toggle="collapse" href="#kontruksi">
                                    <span class="sub-item">Kontruksi</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="kontruksi">
                                    <ul class="nav nav-collapse subnav">
                                        <li class="nav-item">
                                            <a href="{{ url('/batu') }}">
                                                <span class="sub-item">Batu</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/besi') }}">
                                                <span class="sub-item">Besi</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/pasir') }}">
                                                <span class="sub-item">Pasir</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/pengurugan') }}">
                                    <span class="sub-item">Pengurugan</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/poles') }}">
                        <i class="fas fa-car-alt"></i>
                        <p>Poles Kaca Mobil</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/sembako') }}">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Sembako</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/sparepartamb') }}">
                        <i class="fas fa-cogs"></i>
                        <p>Sparepart</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/trip') }}">
                        <i class="fas fa-shuttle-van"></i>
                        <p>Perjalanan</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('.nav-item a, .sub-item');

        navLinks.forEach(link => {
            if (link.href === currentUrl) {
                let navItem = link.closest('li');
                navItem.classList.add('active');

                // Open all parent collapses
                let parentCollapse = navItem.closest('.collapse');
                while (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const parentNavItem = parentCollapse.closest('li.nav-item');
                    if (parentNavItem) {
                        parentNavItem.classList.add('active');
                    }
                    parentCollapse = parentNavItem ? parentNavItem.closest('.collapse') : null;
                }
            }
        });
    });
</script>
<!-- End Sidebar -->