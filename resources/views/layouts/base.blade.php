<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Ambarawa - Dashboard')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('') }}assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('') }}assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('') }}assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('') }}assets/css/plugins.min.css" />
    <link rel="stylesheet" href="{{ asset('') }}assets/css/kaiadmin.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/bootstrap-daterangepicker/daterangepicker.css">

    <!-- custom style -->
    <style>
        .text-xxs-bold {
            font-size: x-small !important;
            font-weight: bold !important;
        }
    </style>

    <style>
        .alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
            display: none;
        }
    </style>

    <style>
        .select2-container--default .select2-selection--single {
            border: none;
            font-size: small;
        }

        .select2-container--default .select2-selection--multiple {
            border: none;
            border-bottom: 1px solid #ced4da;
            border-radius: 0px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: none;
            border-bottom: 1px solid #ced4da;
            border-radius: 0px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- sidebar -->
        @include('layouts.sidebar')

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
                <!-- Navbar Header -->
                @include('layouts.navbar')
                <!-- End Navbar -->
            </div>

            <!-- content -->
            @yield('content')

            <!-- footer -->
            @include('layouts.footer')
        </div>

    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('') }}assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('') }}assets/js/core/popper.min.js"></script>
    <script src="{{ asset('') }}assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('') }}assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="{{ asset('') }}assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('') }}assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('') }}assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('') }}assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".alert").fadeIn('slow');
            
            setTimeout(function() {
                $(".alert").fadeOut('slow', function() {
                    $(this).alert('close');
                });
            }, 3750);
        });
    </script>
    
    <!-- DataTables -->
    <script src="{{ asset('') }}assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('') }}assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="{{ asset('') }}assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('') }}assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Include SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('') }}assets/js/kaiadmin.min.js"></script>

    <!-- momment js is must -->
    <script src="{{ asset('') }}assets/vendor/moment/moment.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Daterangepicker -->
    <script src="{{ asset('') }}assets/js/plugins-init/bs-daterange-picker-init.js"></script>

    <!-- select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>