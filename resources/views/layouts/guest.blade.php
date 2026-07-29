<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Login aplikasi kasir warung kopi berbasis Laravel.">
        <meta name="robots" content="noindex,nofollow">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kasir Kopi') }}</title>

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-cream px-4 py-10 dark:bg-slate-950">
            <div>
                <a href="/">
                    <x-application-logo />
                </a>
            </div>

            <div class="mt-4 text-center">
                <h1 class="text-2xl font-extrabold text-coffee-950 dark:text-coffee-200">Kasir Kopi</h1>
                <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-400">Masuk untuk mulai transaksi</p>
            </div>

            <div class="app-card mt-7 w-full max-w-md p-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
