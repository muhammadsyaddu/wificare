<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ $title ?? 'WiFiCare' }} — WiFiCare
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>


<body class="
    h-full
    {{ auth()->user()->isAdmin()
        ? 'wc-admin-shell'
        : 'wc-technician-shell bg-gray-50'
    }}
">

@if(auth()->user()->isAdmin())

    {{-- =========================================================
         ADMIN APPLICATION SHELL
         ========================================================= --}}

    <div class="wc-admin-layout">

        {{-- =====================================================
             SIDEBAR
             ===================================================== --}}

        <aside
            id="sidebar"
            class="wc-admin-sidebar"
        >

            {{-- Brand --}}
            <div class="wc-admin-brand">

                <div class="wc-admin-brand-mark">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    >
                        <path d="M2.5 9.8a14 14 0 0 1 19 0"/>
                        <path d="M5.8 13.1a9.2 9.2 0 0 1 12.4 0"/>
                        <path d="M9.2 16.4a4.4 4.4 0 0 1 5.6 0"/>
                        <path d="M12 20h.01"/>
                    </svg>

                </div>

                <div class="wc-admin-brand-copy">

                    <strong>
                        WiFi<span>Care</span>
                    </strong>

                    <small>
                        Operations Console
                    </small>

                </div>

            </div>


            {{-- Navigation --}}
            <nav class="wc-admin-navigation">

                <div class="wc-admin-nav-label">
                    WORKSPACE
                </div>


                <a
                    href="{{ route('admin.dashboard') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <rect x="3.5" y="3.5" width="7" height="7" rx="1"/>
                            <rect x="13.5" y="3.5" width="7" height="7" rx="1"/>
                            <rect x="3.5" y="13.5" width="7" height="7" rx="1"/>
                            <rect x="13.5" y="13.5" width="7" height="7" rx="1"/>
                        </svg>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                <div class="wc-admin-nav-label">
                    OPERATIONS
                </div>


                <a
                    href="{{ route('admin.customers.index') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20"/>
                            <circle cx="9.5" cy="7" r="3"/>
                            <path d="M16 4.5a3 3 0 0 1 0 5.8"/>
                            <path d="M18 14.5a4 4 0 0 1 3 3.5V20"/>
                        </svg>

                    </span>

                    <span>
                        Pelanggan
                    </span>

                </a>


                <a
                    href="{{ route('admin.technicians.index') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.technicians.*') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="9" cy="7" r="3"/>
                            <path d="M3.5 20a5.5 5.5 0 0 1 11 0"/>
                            <path d="m15 12 2 2 4-4"/>
                        </svg>

                    </span>

                    <span>
                        Teknisi
                    </span>

                </a>


                <a
                    href="{{ route('admin.orders.index') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="4" y="3.5" width="16" height="17" rx="2"/>
                            <path d="M8 8h8M8 12h5M8 16h3"/>
                        </svg>

                    </span>

                    <span>
                        Pekerjaan
                    </span>

                </a>


                <a
                    href="{{ route('admin.issues.index') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.issues.*') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 3.5 21 20H3L12 3.5Z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 16.5h.01"/>
                        </svg>

                    </span>

                    <span>
                        Gangguan
                    </span>

                </a>


                <div class="wc-admin-nav-label">
                    INSIGHTS
                </div>


                <a
                    href="{{ route('admin.reports.index') }}"
                    class="wc-admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}"
                >

                    <span class="wc-admin-nav-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        >
                            <path d="M4 19V10"/>
                            <path d="M10 19V5"/>
                            <path d="M16 19v-7"/>
                            <path d="M22 19V3"/>
                        </svg>

                    </span>

                    <span>
                        Laporan
                    </span>

                </a>

            </nav>


            {{-- Sidebar footer --}}
            <div class="wc-admin-sidebar-footer">

                <div class="wc-admin-user">

                    <div class="wc-admin-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>

                    <div class="wc-admin-user-copy">

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <small>
                            {{ auth()->user()->role?->display_name ?? 'Administrator' }}
                        </small>

                    </div>

                </div>

                <div class="wc-admin-security">

                    <span class="wc-admin-online-dot"></span>

                    <span>
                        Sistem aktif
                    </span>

                </div>

            </div>

        </aside>


        {{-- Mobile overlay --}}
        <div
            id="sidebar-overlay"
            class="wc-admin-overlay"
            onclick="toggleSidebar()"
        ></div>


        {{-- =====================================================
             MAIN
             ===================================================== --}}

        <div class="wc-admin-main">

            {{-- Topbar --}}
            <header class="wc-admin-topbar">

                <div class="wc-admin-topbar-left">

                    <button
                        type="button"
                        onclick="toggleSidebar()"
                        class="wc-admin-mobile-button"
                        aria-label="Buka menu"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>

                    </button>


                    <div>

                        <div class="wc-admin-breadcrumb">
                            WiFiCare / Admin
                        </div>

                        <h1>
                            {{ $header ?? 'Dashboard Operasional' }}
                        </h1>

                    </div>

                </div>


                <div class="wc-admin-topbar-right">

                    <div class="wc-admin-role">

                        <span class="wc-admin-role-dot"></span>

                        Administrator

                    </div>


                    <div class="wc-admin-divider"></div>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="wc-admin-logout"
                            title="Keluar"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M14 5H6.5A2.5 2.5 0 0 0 4 7.5v9A2.5 2.5 0 0 0 6.5 19H14"/>
                                <path d="m17 8 4 4-4 4"/>
                                <path d="M21 12H9"/>
                            </svg>

                            <span>
                                Keluar
                            </span>

                        </button>

                    </form>

                </div>

            </header>


            {{-- Flash messages --}}
            <div class="wc-admin-flash-wrap">

                @if(session('success'))

                    <div
                        class="wc-admin-alert wc-admin-alert-success"
                        role="alert"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m8.5 12 2.2 2.2 4.8-5"/>
                        </svg>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>

                @endif


                @if(session('error'))

                    <div
                        class="wc-admin-alert wc-admin-alert-error"
                        role="alert"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 8v4"/>
                            <path d="M12 16h.01"/>
                        </svg>

                        <span>
                            {{ session('error') }}
                        </span>

                    </div>

                @endif

            </div>


            {{-- Page content --}}
            <main class="wc-admin-content">

                {{ $slot }}

            </main>

        </div>

    </div>


@else

    {{-- =========================================================
         EXISTING TECHNICIAN SHELL
         ========================================================= --}}

    <div class="flex h-full">

        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-navy-900 transition-transform duration-200 lg:translate-x-0 -translate-x-full"
        >

            <div class="flex h-16 items-center gap-3 px-5 border-b border-white/10">

                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-600">

                    <svg
                        class="h-5 w-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.858 15.355-5.858 21.213 0"/>
                    </svg>

                </div>

                <span class="text-lg font-bold text-white tracking-tight">
                    WiFi<span class="text-primary-400">Care</span>
                </span>

            </div>


            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                @if(auth()->user()->isTechnician())

                    <a
                        href="{{ route('teknisi.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('teknisi.dashboard') ? 'sidebar-link-active' : '' }}"
                    >

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 8.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 013.75 18v-2.25z"/>
                        </svg>

                        Dashboard

                    </a>


                    <a
                        href="{{ route('teknisi.orders.index') }}"
                        class="sidebar-link {{ request()->routeIs('teknisi.orders.index') || request()->routeIs('teknisi.orders.show') ? 'sidebar-link-active' : '' }}"
                    >

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 12h3.75M9 15h3.75M9 18h3.75"/>
                            <path d="M18 21H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8l4 4v11a2 2 0 0 1-2 2z"/>
                        </svg>

                        Tugas Saya

                    </a>


                    <a
                        href="{{ route('teknisi.orders.history') }}"
                        class="sidebar-link {{ request()->routeIs('teknisi.orders.history') ? 'sidebar-link-active' : '' }}"
                    >

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>

                        Riwayat

                    </a>

                @endif

            </nav>


            <div class="border-t border-white/10 px-4 py-3">

                <div class="flex items-center gap-3">

                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>

                    <div class="min-w-0 flex-1">

                        <p class="truncate text-sm font-medium text-white">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-gray-400">
                            {{ auth()->user()->role?->display_name }}
                        </p>

                    </div>

                </div>

            </div>

        </aside>


        <div
            id="sidebar-overlay"
            class="fixed inset-0 z-20 bg-black/50 hidden lg:hidden"
            onclick="toggleSidebar()"
        ></div>


        <div class="flex flex-1 flex-col lg:pl-64">

            <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8">

                <div class="flex items-center gap-3">

                    <button
                        onclick="toggleSidebar()"
                        class="btn-icon lg:hidden"
                    >

                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>

                    </button>

                    <h1 class="text-lg font-semibold text-gray-900">
                        {{ $header ?? '' }}
                    </h1>

                </div>


                <div class="flex items-center gap-3">

                    <span class="hidden sm:inline-flex badge badge-confirmed">
                        {{ auth()->user()->role?->display_name }}
                    </span>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn-icon"
                            title="Keluar"
                        >

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>

                        </button>

                    </form>

                </div>

            </header>


            <div class="px-4 sm:px-6 lg:px-8">

                @if(session('success'))

                    <div class="mt-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>

                @endif


                @if(session('error'))

                    <div class="mt-4 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>

                @endif

            </div>


            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">

                {{ $slot }}

            </main>

        </div>

    </div>

@endif


<script>
    function toggleSidebar() {

        const sidebar =
            document.getElementById('sidebar');

        const overlay =
            document.getElementById('sidebar-overlay');

        if (!sidebar || !overlay) {
            return;
        }

        sidebar.classList.toggle('is-open');

        overlay.classList.toggle('is-visible');

    }
</script>


@stack('scripts')

</body>
</html>