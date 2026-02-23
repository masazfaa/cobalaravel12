<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'WebGIS Admin')</title>
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/src/assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('GAS.png') }}" />

    <style>
        .custom-navbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0;
            line-height: 1.2;
        }
        .custom-navbar-subtitle {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* HARGA MATI BUAT MOBILE BIAR RAPI DAN GAK ILANG */
        @media (max-width: 991px) {
            .navbar .navbar-brand-wrapper {
                display: none !important; /* Matikan wrapper kiri bawaan biar ga ngedorong */
            }
            .navbar .navbar-menu-wrapper {
                width: 100% !important; /* Paksa menu full layar KHUSUS DI MOBILE */
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
        }
    </style>
</head>
<body class="with-welcome-text">
    <div class="container-scroller">

        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">

            <div class="text-center navbar-brand-wrapper d-none d-lg-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="icon-menu"></span>
                    </button>
                </div>
            </div>

            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">

                <div class="d-flex align-items-center">
                    <a class="d-flex align-items-center text-decoration-none" href="{{ route('dashboard') }}">
                        <img src="{{ asset('GAS.png') }}" alt="Logo Geo Anfa" style="width: 40px; height: auto; border-radius: 5px;" class="me-2 shadow-sm">
                        <div class="d-block">
                            <h4 class="custom-navbar-title text-primary">WEBGIS ADMIN</h4>
                            <p class="custom-navbar-subtitle">@yield('title', 'Dashboard')</p>
                        </div>
                    </a>
                </div>

                <div class="d-flex align-items-center">

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown user-dropdown">
                            <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 18px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                                <div class="dropdown-header text-center">
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm mb-2" style="width: 60px; height: 60px; font-size: 24px;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <p class="mb-1 mt-3 fw-semibold">{{ Auth::user()->name }}</p>
                                    <p class="fw-light text-muted mb-0">Role: <span class="badge {{ Auth::user()->role === 'superadmin' ? 'bg-danger' : 'bg-primary' }} ms-1">{{ ucfirst(Auth::user()->role) }}</span></p>
                                    <p class="fw-light text-muted mb-0 mt-1">{{ Auth::user()->email }}</p>
                                </div>
                                <a class="dropdown-item mt-2" href="{{ route('profile.edit') }}">
                                    <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> Profile Anda
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="dropdown-item-icon mdi mdi-power text-danger me-2"></i> Sign Out
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>

                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center ms-3 border-0 bg-transparent p-0" type="button" data-bs-toggle="offcanvas">
                        <span class="mdi mdi-menu text-primary" style="font-size: 2rem;"></span>
                    </button>

                </div>

            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">

            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-category">Menu Utama</li>
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="mdi mdi-database menu-icon"></i>
                            <span class="menu-title">Manajemen Data</span>
                        </a>
                    </li>

                    @if(Auth::user()->role === 'superadmin')
                    <li class="nav-item {{ request()->routeIs('user.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.index') }}">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">Kelola User</span>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item nav-category">Pengaturan</li>
                    <li class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <i class="menu-icon mdi mdi-account-cog"></i>
                            <span class="menu-title">Setting Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                <footer class="footer border-top mt-auto">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">WebGIS Admin Dashboard &copy; Geo Anfa Spasial</span>
                        <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">2026. All rights reserved.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/dashboard/src/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/dashboard/src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/src/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/dashboard/src/assets/js/template.js') }}"></script>
</body>
</html>
