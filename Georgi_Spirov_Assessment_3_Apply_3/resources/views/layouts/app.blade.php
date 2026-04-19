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
            <div class="container py-4">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>

        @include('partials.footer')
    </body>
</html>
