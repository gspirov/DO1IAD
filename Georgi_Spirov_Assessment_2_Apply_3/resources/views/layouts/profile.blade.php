<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light d-flex flex-column min-vh-100">
        @include('partials.nav')

        <main class="flex-grow-1">
            <div class="container-fluid py-4">
                <div class="row g-4">
                    <div class="col-12 col-md-4 col-lg-2">
                        <ul class="nav flex-column nav-pills">
                            <li class="nav-item mb-1">
                                <a href="{{ route('profile.edit') }}"
                                   class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : 'text-dark' }}"
                                >
                                    <i class="bi bi-person me-2"></i>
                                    Profile
                                </a>
                            </li>

                            <li class="nav-item mb-1">
                                <a href="{{ route('profile.change-password') }}"
                                   class="nav-link {{ request()->routeIs('profile.change-password') ? 'active' : 'text-dark' }}"
                                >
                                    <i class="bi bi-lock me-2"></i>
                                    Change Password
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Content -->
                    <div class="col-12 col-md-8 col-lg-9">
                        @include('partials.flash')
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4 p-lg-5">
                                <h3 class="mb-4">
                                    @yield('profile-page-header')
                                </h3>
                                @yield('profile-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include('partials.footer')
    </body>
</html>
