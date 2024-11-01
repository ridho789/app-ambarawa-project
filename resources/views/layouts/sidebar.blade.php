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
                @if (in_array(Auth::user()->level, [0, 1]))
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
                    <a data-bs-toggle="collapse" href="#data_utama">
                        <i class="fas fa-cube"></i>
                        <p>Data Utama</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="data_utama">
                        <ul class="nav nav-collapse">
                            <li class="nav-item">
                                <a href="{{ url('/kategori_material') }}">
                                    <span class="sub-item">Kategori Material</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/kendaraan') }}">
                                    <span class="sub-item">Kendaraan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/proyek') }}">
                                    <span class="sub-item">Proyek</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/satuan') }}">
                                    <span class="sub-item">Satuan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/toko') }}">
                                    <span class="sub-item">Toko</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Submission</h4>
                </li>
                @endif
                @if (in_array(Auth::user()->level, [0, 2]))
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#inventaris">
                        <i class="fas fa-dolly-flatbed"></i>
                        <p>Inventaris</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="inventaris">
                        <ul class="nav nav-collapse">
                            <li class="nav-item">
                                <a href="{{ url('/barang_keluar') }}">
                                    <span class="sub-item">Barang Keluar</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/stok') }}">
                                    <span class="sub-item">Stok Barang</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if (in_array(Auth::user()->level, [0, 1]))
                <li class="nav-item">
                    <a href="{{ url('/permintaan_barang') }}">
                        <i class="fas fa-receipt"></i>
                        <p>Permintaan Barang</p>
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
                                        <!-- <li class="nav-item">
                                            <a href="{{ url('/batu') }}">
                                                <span class="sub-item">Batu</span>
                                            </a>
                                        </li> -->
                                        <li class="nav-item">
                                            <a href="{{ url('/besi') }}">
                                                <span class="sub-item">Besi</span>
                                            </a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a href="{{ url('/pasir') }}">
                                                <span class="sub-item">Pasir</span>
                                            </a>
                                        </li> -->
                                        <li class="nav-item">
                                            <a href="{{ url('/material') }}">
                                                <span class="sub-item">Material</span>
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
                @endif
                @if (Auth::user()->level == 0)
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">History</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/activity') }}">
                        <i class="fas fa-book"></i>
                        <p>Catatan Aktivitas</p>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan URL dasar tanpa parameter tambahan
        const baseUrl = window.location.origin + window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-item a, .nav-collapse a');

        navLinks.forEach(link => {
            if (link && link.href && link.href === baseUrl) { // Cocokkan link persis dengan baseUrl
                // Menandai item yang sesuai sebagai aktif
                let navItem = link.closest('li.nav-item');
                if (navItem) {
                    navItem.classList.add('active');
                }

                // Membuka semua parent collapses dari item aktif
                let parentCollapse = link.closest('.collapse');
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