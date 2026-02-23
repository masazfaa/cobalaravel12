<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Halo, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-gray-600">
                        Anda login sebagai:
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ Auth::user()->role === 'superadmin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Data Pasien</h4>
                                <p class="text-sm text-gray-500 mt-1">Kelola data pasien Medical Checkup.</p>
                                <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Hasil MCU</h4>
                                <p class="text-sm text-gray-500 mt-1">Input hasil pemeriksaan lab.</p>
                                <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 transition ease-in-out duration-150">
                                    Input Hasil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role === 'superadmin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500 bg-red-50">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-red-700">Kelola User Admin</h4>
                                <p class="text-sm text-red-600 mt-1">Setujui pendaftaran admin baru.</p>

                                <a href="{{ route('user.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150">
                                    Buka Approval
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
 Di bagian navbar bisa diubah jadi gini, biar ada perubahan sesuai dengan posisi loginnya apa:
<header id="header" class="header sticky-top">

    <div class="topbar d-flex align-items-center">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">contact@medilab.com</a></i>
                <i class="bi bi-phone d-flex align-items-center ms-4"><span>+62 812 3456 7890</span></i>
            </div>
            <div class="social-links d-none d-md-flex align-items-center">
                <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>

    <div class="branding d-flex align-items-center">

        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">Matriks MCU</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#departments">Departments</a></li>
                    <li><a href="#doctors">Doctors</a></li>
                    <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Menu 1</a></li>
                            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                                <ul>
                                    <li><a href="#">Deep Menu 1</a></li>
                                    <li><a href="#">Deep Menu 2</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Menu 2</a></li>
                        </ul>
                    </li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <div class="d-flex align-items-center">

                <a class="cta-btn d-none d-sm-block" href="#appointment">Make an Appointment</a>

                @if (Route::has('login'))
                    @auth
                        <div class="ms-3 d-none d-lg-block text-end">
                            <span class="d-block fw-bold" style="font-size: 14px; color: #333;">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="badge {{ Auth::user()->role === 'superadmin' ? 'bg-danger' : 'bg-info' }}" style="font-size: 10px;">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="cta-btn d-none d-sm-block ms-2" style="background-color: #16b3ac;">
                            Dashboard
                        </a>

                        @if(Auth::user()->role === 'superadmin')
                            <a href="{{ route('user.index') }}" class="btn btn-sm btn-dark ms-2" style="border-radius: 20px; padding: 8px 15px;" title="Kelola User Pending">
                                <i class="bi bi-person-plus-fill"></i> Approval
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2" style="border-radius: 20px; padding: 8px 12px;" title="Keluar">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>

                    @else
                        <a class="cta-btn d-none d-sm-block ms-2" href="{{ route('login') }}">Login</a>
                    @endauth
                @endif

            </div>

        </div>

    </div>

</header>
