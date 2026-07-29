@php
    $user = auth()->user();
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'roles' => ['admin'], 'icon' => 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z'],
        ['label' => 'Kasir / POS', 'route' => 'pos.index', 'active' => 'pos.*', 'roles' => ['admin', 'cashier'], 'icon' => 'M4 4h16v4H4V4Zm0 6h16v10H4V10Zm3 3v2h4v-2H7Zm7 0v2h3v-2h-3Z'],
        ['label' => 'Riwayat', 'route' => 'orders.index', 'active' => 'orders.*', 'roles' => ['admin', 'cashier'], 'icon' => 'M5 3h14v18H5V3Zm3 4v2h8V7H8Zm0 4v2h8v-2H8Zm0 4v2h5v-2H8Z'],
        ['label' => 'Produk/Menu', 'route' => 'products.index', 'active' => 'products.*', 'roles' => ['admin'], 'icon' => 'M4 5h16v4H4V5Zm2 6h12v8H6v-8Zm3 2v4h6v-4H9Z'],
        ['label' => 'Kategori', 'route' => 'categories.index', 'active' => 'categories.*', 'roles' => ['admin'], 'icon' => 'M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z'],
        ['label' => 'Pengguna', 'route' => 'users.index', 'active' => 'users.*', 'roles' => ['admin'], 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-8 8a8 8 0 0 1 16 0H4Z'],
        ['label' => 'Pengaturan', 'route' => 'store-settings.edit', 'active' => 'store-settings.*', 'roles' => ['admin'], 'icon' => 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm8.7 5.4v-2.8l-2.1-.4a7.7 7.7 0 0 0-.8-1.9l1.2-1.8-2-2-1.8 1.2c-.6-.3-1.2-.6-1.9-.8L12.9 2h-2.8l-.4 2.1c-.7.2-1.3.4-1.9.8L6 3.7l-2 2 1.2 1.8c-.3.6-.6 1.2-.8 1.9l-2.1.4v2.8l2.1.4c.2.7.4 1.3.8 1.9L4 16.7l2 2 1.8-1.2c.6.3 1.2.6 1.9.8l.4 2.1h2.8l.4-2.1c.7-.2 1.3-.4 1.9-.8l1.8 1.2 2-2-1.2-1.8c.3-.6.6-1.2.8-1.9l2.1-.4Z'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Aplikasi kasir warung kopi berbasis Laravel untuk POS, transaksi, laporan, dan cetak struk.">
        <meta name="robots" content="noindex,nofollow">

        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'Kasir Kopi') }}</title>

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased">
        <div x-data="{ sidebarOpen: false, darkMode: document.documentElement.classList.contains('dark') }" class="min-h-screen bg-cream dark:bg-slate-950">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden" @click="sidebarOpen = false"></div>

            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-coffee-200 bg-white/95 px-5 py-5 shadow-2xl transition duration-300 dark:border-slate-800 dark:bg-slate-950 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
            >
                <div class="flex items-center justify-between">
                    <a href="{{ route($user?->hasRole('admin') ? 'dashboard' : 'pos.index') }}" class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-coffee-950 text-lg font-black text-coffee-200 dark:bg-coffee-200 dark:text-coffee-950">K</span>
                        <span>
                            <span class="block text-base font-extrabold text-coffee-950 dark:text-coffee-200">Kasir Kopi</span>
                            <span class="block text-xs font-semibold text-olive-700 dark:text-olive-200">POS warung modern</span>
                        </span>
                    </a>
                    <button type="button" class="btn-secondary px-3 py-2 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                        <span class="text-lg leading-none">&times;</span>
                    </button>
                </div>

                <nav class="mt-8 flex-1">
                    <ul class="space-y-2">
                        @foreach ($navItems as $item)
                            @if ($user?->hasAnyRole($item['roles']))
                                @php($active = request()->routeIs($item['active']))
                                <li>
                                    <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm font-bold transition duration-300 {{ $active ? 'border-coffee-950 bg-coffee-950 text-white shadow-soft dark:border-coffee-200 dark:bg-coffee-200 dark:text-coffee-950' : 'border-transparent text-slate-700 hover:border-coffee-200 hover:bg-coffee-200/40 hover:text-coffee-950 dark:text-slate-200 dark:hover:border-slate-700 dark:hover:bg-slate-900 dark:hover:text-coffee-200' }}">
                                        <span class="relative h-5 w-5 shrink-0">
                                            <svg class="absolute h-5 w-5 fill-current transition-all duration-300 {{ $active ? 'opacity-0' : 'opacity-100 group-hover:opacity-0' }}" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="{{ $item['icon'] }}" />
                                            </svg>
                                            <svg class="absolute h-5 w-5 fill-current text-coffee-500 opacity-0 transition-all duration-300 {{ $active ? 'opacity-100 text-current' : 'group-hover:opacity-100' }}" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="{{ $item['icon'] }}" />
                                            </svg>
                                        </span>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                <div class="rounded-2xl border border-coffee-200 bg-cream p-4 dark:border-slate-800 dark:bg-slate-900">
                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $user?->name }}</div>
                    <div class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $user?->email }}</div>
                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn-secondary flex-1 px-3 py-2 text-xs">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-secondary px-3 py-2 text-xs" type="submit">Keluar</button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="lg:pl-72">
                <header class="sticky top-0 z-20 border-b border-coffee-200/80 bg-cream/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/90">
                    <div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" class="btn-secondary px-3 py-2 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                                <span class="text-lg leading-none">&#9776;</span>
                            </button>
                            <div>
                                <h1 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200 sm:text-xl">{{ $headerTitle ?? 'Kasir Kopi' }}</h1>
                                @isset($headerSubtitle)
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 sm:text-sm">{{ $headerSubtitle }}</p>
                                @endisset
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn-secondary px-3 py-2"
                            @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode); localStorage.theme = darkMode ? 'dark' : 'light'"
                            aria-label="Toggle dark mode"
                        >
                            <span x-show="!darkMode">Dark</span>
                            <span x-show="darkMode">Light</span>
                        </button>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    <x-flash />
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
