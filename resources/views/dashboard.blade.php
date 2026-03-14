<x-layout
    :title="$pageTitle ?? 'Indonesia Mining Watch - Pantau Wilayah Pertambangan di Indonesia'"
    :meta-description="$metaDescription ?? 'WebGIS pemantauan wilayah pertambangan di Indonesia - Indonesia Mining Watch.'"
    :canonical-url="$canonicalUrl ?? route('dashboard')"
    :og-title="$pageTitle ?? 'Indonesia Mining Watch - Dashboard Pantau Wilayah Pertambangan'"
    :og-description="$metaDescription ?? 'WebGIS pemantauan wilayah pertambangan di Indonesia - Indonesia Mining Watch.'"
    :og-image="$ogImage ?? null">
    @push('styles')
        <style>
            #dashboard-root {
                background:
                    radial-gradient(circle at top left, rgba(16, 185, 129, 0.10), transparent 28%),
                    linear-gradient(180deg, #020617 0%, #020617 100%);
                transition: background-color 0.25s ease, color 0.25s ease;
            }

            #dashboard-root.theme-light {
                background:
                    radial-gradient(circle at top left, rgba(16, 185, 129, 0.10), transparent 30%),
                    linear-gradient(180deg, #e2e8f0 0%, #f8fafc 100%);
            }

            #dashboard-root.theme-light .dashboard-navbar,
            #dashboard-root.theme-light #sidebar,
            #dashboard-root.theme-light #legend,
            #dashboard-root.theme-light #info-panel,
            #dashboard-root.theme-light .dashboard-theme-menu,
            #dashboard-root.theme-light .dashboard-basemap-switch {
                background-color: rgba(255, 255, 255, 0.92) !important;
                border-color: rgba(148, 163, 184, 0.28) !important;
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            }

            #dashboard-root.theme-light #sidebar .bg-gray-800\/40,
            #dashboard-root.theme-light #sidebar .bg-gray-800\/30,
            #dashboard-root.theme-light #dashboard-about-link,
            #dashboard-root.theme-light #sidebar a[href="/admin"],
            #dashboard-root.theme-light #info-panel .bg-gray-800\/40,
            #dashboard-root.theme-light #info-panel .bg-gray-800\/30,
            #dashboard-root.theme-light #info-panel .bg-gray-800\/35,
            #dashboard-root.theme-light #info-panel .bg-red-500\/10,
            #dashboard-root.theme-light .gallery-slider-button,
            #dashboard-root.theme-light .basemap-btn,
            #dashboard-root.theme-light .tambang-item,
            #dashboard-root.theme-light .gallery-trigger,
            #dashboard-root.theme-light .dashboard-theme-trigger,
            #dashboard-root.theme-light .dashboard-filter-control,
            #dashboard-root.theme-light .dashboard-search-control,
            #dashboard-root.theme-light .dashboard-language-control {
                background-color: rgba(248, 250, 252, 0.92) !important;
                border-color: rgba(148, 163, 184, 0.28) !important;
            }

            #dashboard-root.theme-light .text-white,
            #dashboard-root.theme-light .text-gray-200,
            #dashboard-root.theme-light .text-gray-300 {
                color: #0f172a !important;
            }

            #dashboard-root.theme-light .text-gray-400,
            #dashboard-root.theme-light .text-gray-500,
            #dashboard-root.theme-light .text-gray-600 {
                color: #64748b !important;
            }

            #dashboard-root.theme-light .placeholder-gray-500::placeholder {
                color: #94a3b8 !important;
            }

            #dashboard-root.theme-light .border-white\/10,
            #dashboard-root.theme-light .border-gray-700\/30,
            #dashboard-root.theme-light .border-gray-700\/50,
            #dashboard-root.theme-light .border-gray-700\/60,
            #dashboard-root.theme-light .border-gray-800\/50 {
                border-color: rgba(148, 163, 184, 0.28) !important;
            }

            #dashboard-root.theme-light .hover\:bg-gray-700\/50:hover,
            #dashboard-root.theme-light .hover\:bg-gray-800\/60:hover,
            #dashboard-root.theme-light .hover\:bg-gray-800:hover {
                background-color: rgba(226, 232, 240, 0.92) !important;
            }

            #dashboard-root.theme-light .group-hover\:text-white:is(.group:hover *) {
                color: #0f172a !important;
            }

            .dashboard-navbar {
                overflow: visible;
            }

            .dashboard-theme-menu {
                position: fixed;
                z-index: 40;
            }

            .dashboard-theme-option.is-active {
                background-color: rgba(16, 185, 129, 0.12);
                color: #ffffff;
            }

            .dashboard-theme-menu[data-open="true"] {
                opacity: 1;
                transform: translateY(0) scale(1);
                visibility: visible;
                pointer-events: auto;
            }

            .dashboard-theme-menu[data-open="false"] {
                opacity: 0;
                transform: translateY(0.35rem) scale(0.98);
                visibility: hidden;
                pointer-events: none;
            }

            .dashboard-nav-button {
                background-color: rgba(17, 24, 39, 0.70);
                color: #d1d5db;
                border: 1px solid rgba(55, 65, 81, 0.65);
            }

            .dashboard-nav-button:hover {
                border-color: rgba(16, 185, 129, 0.40);
                color: #ffffff;
                background-color: rgba(31, 41, 55, 0.82);
            }

            .dashboard-panel-close {
                background-color: rgba(31, 41, 55, 0.72);
                color: #9ca3af;
                border: 1px solid rgba(55, 65, 81, 0.65);
            }

            .dashboard-panel-close:hover {
                background-color: rgba(55, 65, 81, 0.84);
                color: #ffffff;
            }

            #dashboard-root.theme-light .dashboard-panel-close {
                background-color: rgba(255, 255, 255, 0.94);
                color: #475569;
                border-color: rgba(148, 163, 184, 0.28);
                box-shadow: 0 10px 25px rgba(15, 23, 42, 0.10);
            }

            #dashboard-root.theme-light .dashboard-panel-close:hover {
                background-color: rgba(241, 245, 249, 0.96);
                color: #0f172a;
            }

            #dashboard-root.theme-light .dashboard-nav-button {
                background-color: rgba(248, 250, 252, 0.92);
                color: #475569;
                border-color: rgba(148, 163, 184, 0.28);
            }

            #dashboard-root.theme-light .dashboard-nav-button:hover {
                background-color: rgba(241, 245, 249, 0.96);
                color: #0f172a;
                border-color: rgba(16, 185, 129, 0.28);
            }

            #dashboard-root.theme-light .dashboard-theme-option.is-active {
                background-color: rgba(16, 185, 129, 0.10);
                color: #0f172a;
            }

            .dashboard-share-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 0;
                border-radius: 0.75rem;
                padding: 0.45rem 0.7rem;
                font-size: 0.6875rem;
                font-weight: 700;
                line-height: 1;
                letter-spacing: 0.01em;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            }

            .dashboard-share-button svg {
                flex-shrink: 0;
            }

            .dashboard-share-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }

            .gallery-slider-track {
                display: flex;
                transition: transform 220ms ease;
                will-change: transform;
            }

            .gallery-slider-viewport {
                touch-action: pan-y;
            }

            .gallery-slide {
                flex: 0 0 100%;
                min-width: 100%;
            }

            .gallery-slider-button {
                display: inline-flex;
                height: 2rem;
                width: 2rem;
                align-items: center;
                justify-content: center;
                border-radius: 9999px;
                border: 1px solid rgba(55, 65, 81, 0.55);
                background: rgba(31, 41, 55, 0.68);
                color: #d1d5db;
                transition: all 160ms ease;
            }

            .gallery-slider-button:hover:not(:disabled) {
                background: rgba(55, 65, 81, 0.88);
                color: #ffffff;
            }

            .gallery-slider-button:disabled {
                cursor: not-allowed;
                opacity: 0.45;
            }

            .gallery-slider-dots {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.4rem;
            }

            .gallery-slider-dot {
                height: 0.5rem;
                width: 0.5rem;
                border-radius: 9999px;
                border: 1px solid rgba(148, 163, 184, 0.28);
                background: rgba(100, 116, 139, 0.35);
                transition: all 160ms ease;
            }

            .gallery-slider-dot:hover {
                background: rgba(148, 163, 184, 0.6);
            }

            .gallery-slider-dot.is-active {
                width: 1.35rem;
                background: rgba(16, 185, 129, 0.9);
                border-color: rgba(16, 185, 129, 0.9);
            }

            #dashboard-root.theme-light .gallery-slider-dot {
                background: rgba(148, 163, 184, 0.45);
                border-color: rgba(148, 163, 184, 0.5);
            }

            #dashboard-root.theme-light .gallery-slider-dot:hover {
                background: rgba(100, 116, 139, 0.75);
            }

            #dashboard-root.theme-light .gallery-slider-dot.is-active {
                background: rgba(16, 185, 129, 0.88);
                border-color: rgba(16, 185, 129, 0.88);
            }

            #dashboard-root.theme-light .dashboard-share-button {
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            }

            #dashboard-root.theme-light .share-button-whatsapp {
                background-color: rgba(34, 197, 94, 0.14) !important;
                border-color: rgba(34, 197, 94, 0.28) !important;
                color: #166534 !important;
            }

            #dashboard-root.theme-light .share-button-whatsapp:hover {
                background-color: rgba(34, 197, 94, 0.22) !important;
            }

            #dashboard-root.theme-light .share-button-email {
                background-color: rgba(100, 116, 139, 0.14) !important;
                border-color: rgba(100, 116, 139, 0.28) !important;
                color: #334155 !important;
            }

            #dashboard-root.theme-light .share-button-email:hover {
                background-color: rgba(100, 116, 139, 0.22) !important;
            }

            #dashboard-root.theme-light .share-button-telegram {
                background-color: rgba(14, 165, 233, 0.14) !important;
                border-color: rgba(14, 165, 233, 0.28) !important;
                color: #0c4a6e !important;
            }

            #dashboard-root.theme-light .share-button-telegram:hover {
                background-color: rgba(14, 165, 233, 0.22) !important;
            }

            #dashboard-root.theme-light .share-button-x {
                background-color: rgba(15, 23, 42, 0.08) !important;
                border-color: rgba(15, 23, 42, 0.16) !important;
                color: #0f172a !important;
            }

            #dashboard-root.theme-light .share-button-x:hover {
                background-color: rgba(15, 23, 42, 0.14) !important;
            }

            #dashboard-root.theme-light .share-button-facebook {
                background-color: rgba(59, 130, 246, 0.14) !important;
                border-color: rgba(59, 130, 246, 0.28) !important;
                color: #1d4ed8 !important;
            }

            #dashboard-root.theme-light .share-button-facebook:hover {
                background-color: rgba(59, 130, 246, 0.22) !important;
            }

            #dashboard-root.theme-light .overlap-chart-legend-item {
                background-color: rgba(255, 255, 255, 0.88) !important;
                border-color: rgba(148, 163, 184, 0.28) !important;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            }

            #dashboard-root.theme-light .overlap-chart-legend-name,
            #dashboard-root.theme-light .overlap-chart-legend-value {
                color: #0f172a !important;
            }

            #dashboard-root.theme-light .overlap-chart-legend-subtext,
            #dashboard-root.theme-light .overlap-chart-empty-text {
                color: #64748b !important;
            }

            #dashboard-root.theme-light .overlap-chart-empty-shell {
                background-color: rgba(255, 255, 255, 0.92) !important;
                border-color: rgba(148, 163, 184, 0.28) !important;
                box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            }
        </style>
    @endpush

    <div id="dashboard-root"
        data-dashboard-url="{{ route('dashboard') }}"
        data-shared-url-template="{{ route('dashboard.shared', ['publicUid' => '__PUBLIC_UID__']) }}"
        data-shared-tambang-uid="{{ $sharedTambangUid ?? '' }}"
        data-default-title="{{ $defaultDashboardTitle ?? 'Indonesia Mining Watch - Dashboard' }}"
        data-default-description="{{ $defaultDashboardDescription ?? 'WebGIS pantau wilayah pertambangan - Indonesia Mining Watch.' }}"
        class="flex h-screen overflow-hidden theme-dark">
        <aside id="sidebar"
            class="w-80 flex-shrink-0 bg-gray-900/80 backdrop-blur-xl border-r border-gray-800/50 flex flex-col transition-all duration-300 z-30">
            <div class="px-4 pt-4 pb-4 grid grid-cols-2 gap-3">
                <div class="bg-gray-800/40 rounded-xl p-3 border border-gray-700/30">
                    <p id="stat-label-total-tambang" class="text-xs text-gray-500 mb-1">Jumlah Izin</p>
                    <p id="stat-total-tambang" class="text-xl font-bold text-white">-</p>
                </div>
                <div class="bg-gray-800/40 rounded-xl p-3 border border-gray-700/30">
                    <p id="stat-label-tambang-overlap" class="text-xs text-gray-500 mb-1">Izin Overlap</p>
                    <p id="stat-tambang-overlap" class="text-xl font-bold text-red-400">-</p>
                </div>
                <div class="bg-gray-800/40 rounded-xl p-3 border border-gray-700/30">
                    <p id="stat-label-luas-tambang" class="text-xs text-gray-500 mb-1">Total Luas Izin</p>
                    <p id="stat-luas-tambang" class="text-lg font-bold text-emerald-400"><span>-</span><span
                            class="text-xs font-normal text-gray-500 ml-1">Ha</span></p>
                </div>
                <div class="bg-gray-800/40 rounded-xl p-3 border border-gray-700/30">
                    <p id="stat-label-luas-overlap" class="text-xs text-gray-500 mb-1">Total Luas Overlap</p>
                    <p id="stat-luas-overlap" class="text-lg font-bold text-amber-400"><span>-</span><span
                            class="text-xs font-normal text-gray-500 ml-1">Ha</span></p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-4 pb-4">
                <h3 id="label-tambang-list-title" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Wilayah Tambang</h3>
                <div id="tambang-list" class="space-y-2">
                    <div class="text-sm text-gray-500 text-center py-8">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Memuat data...
                    </div>
                </div>
                <div id="tambang-list-load-more-wrap" class="mt-3 hidden">
                    <button id="tambang-list-load-more" type="button"
                        class="w-full rounded-xl border border-gray-700/50 bg-gray-800/40 px-4 py-2.5 text-sm font-medium text-gray-300 transition hover:border-gray-600/60 hover:bg-gray-700/50 hover:text-white">
                        Muat lebih banyak
                    </button>
                </div>
            </div>

            <div class="p-4 border-t border-gray-800/50">
                <a href="{{ route('about') }}"
                    id="dashboard-about-link"
                    class="mb-3 flex items-center gap-2 rounded-xl bg-gray-800/40 px-4 py-2.5 text-sm text-gray-400 transition-all hover:bg-gray-700/50 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                    </svg>
                    <span id="label-about-link">About</span>
                </a>
            </div>
        </aside>

        {{-- ===== MAIN MAP AREA ===== --}}
        <main class="flex-1 relative">
            <div class="absolute inset-x-0 top-0 z-50">
                <div class="dashboard-navbar border-b border-white/10 bg-gray-950/84 px-3 py-3 shadow-2xl backdrop-blur-xl md:px-4">
                    <div class="flex items-center gap-3 overflow-x-auto whitespace-nowrap">
                        <div class="flex min-w-max items-center gap-3">
                            <button id="toggle-sidebar" type="button"
                                class="dashboard-nav-button flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.6 9h16.8M3.6 15h16.8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1
                                        class="truncate text-base font-bold bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent md:text-lg">
                                        Indonesia Mining Watch
                                    </h1>
                                    <p id="dashboard-subtitle" class="truncate text-xs text-gray-500">Pantau Wilayah Pertambangan</p>
                                </div>
                            </a>
                        </div>

                        <div class="min-w-[20rem] flex-1 px-1 md:min-w-[26rem]">
                            <div class="relative w-full">
                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input id="search-input" type="text" placeholder="Cari wilayah tambang..."
                                    class="dashboard-search-control w-full rounded-xl border border-gray-700/60 bg-gray-900/75 py-3 pl-10 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                            </div>
                        </div>

                        <div class="ml-auto flex min-w-max items-center gap-3">
                            <div class="min-w-[10rem]">
                                    <label id="label-filter-provinsi" for="filter-provinsi" class="sr-only">Provinsi</label>
                                    <select id="filter-provinsi"
                                        class="dashboard-filter-control w-full rounded-xl border border-gray-700/60 bg-gray-900/75 px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                        <option value="">Semua Provinsi</option>
                                    </select>
                            </div>
                            <div class="min-w-[12rem]">
                                    <label id="label-filter-jenis-tambang" for="filter-jenis-tambang" class="sr-only">Jenis Tambang / Komoditas</label>
                                    <select id="filter-jenis-tambang"
                                        class="dashboard-filter-control w-full rounded-xl border border-gray-700/60 bg-gray-900/75 px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                        <option value="">Semua Komoditas</option>
                                    </select>
                            </div>

                            <div class="dashboard-language-control flex items-center gap-2 rounded-xl border border-gray-700/60 bg-gray-900/75 p-1">
                                <button id="lang-switch-id" type="button"
                                    class="dashboard-lang-btn rounded-lg px-3 py-2 text-xs font-semibold text-white bg-emerald-600/30">
                                    ID
                                </button>
                                <button id="lang-switch-en" type="button"
                                    class="dashboard-lang-btn rounded-lg px-3 py-2 text-xs font-semibold text-gray-400">
                                    EN
                                </button>
                            </div>

                            <div class="relative">
                                <button id="theme-menu-button" type="button"
                                    class="dashboard-theme-trigger dashboard-filter-control flex min-w-20 items-center justify-between gap-2 rounded-xl border border-gray-700/60 bg-gray-900/75 px-4 py-2.5 text-sm text-gray-200 transition hover:border-emerald-500/40">
                                    <span class="flex items-center gap-2">
                                        <svg id="theme-current-dark-icon" class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.8A9 9 0 1111.2 3 7 7 0 0021 12.8z" />
                                        </svg>
                                        <svg id="theme-current-light-icon" class="hidden h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.5m0 13V21m9-9h-2.5M5.5 12H3m14.864 6.364-1.768-1.768M7.904 7.904 6.136 6.136m11.728 0-1.768 1.768M7.904 16.096l-1.768 1.768M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                        </svg>
                                        <span id="dashboard-theme-current">Dark</span>
                                    </span>
                                    <svg id="theme-menu-chevron" class="h-4 w-4 text-gray-500 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="theme-menu"
                                    data-open="false"
                                    class="dashboard-theme-menu top-0 left-0 rounded-xl border border-gray-700/60 bg-gray-900/95 p-2 shadow-2xl backdrop-blur-xl transition-all duration-150 ease-out origin-top-right">
                                    <button id="theme-option-dark" type="button"
                                        class="dashboard-theme-option is-active flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-gray-200 transition hover:bg-gray-800/60">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.8A9 9 0 1111.2 3 7 7 0 0021 12.8z" />
                                            </svg>
                                            <span id="label-theme-dark">Dark</span>
                                        </span>
                                        <span id="theme-check-dark" class="text-emerald-400">•</span>
                                    </button>
                                    <button id="theme-option-light" type="button"
                                        class="dashboard-theme-option flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-gray-200 transition hover:bg-gray-800/60">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.5m0 13V21m9-9h-2.5M5.5 12H3m14.864 6.364-1.768-1.768M7.904 7.904 6.136 6.136m11.728 0-1.768 1.768M7.904 16.096l-1.768 1.768M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                            </svg>
                                            <span id="label-theme-light">Light</span>
                                        </span>
                                        <span id="theme-check-light" class="hidden text-emerald-400">•</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="map" class="absolute inset-0 h-screen w-full"></div>

            <div id="legend"
                class="absolute bottom-8 left-4 z-10 bg-gray-900/90 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4 shadow-2xl min-w-[200px] max-w-[280px]">
                <h4 id="label-legend-title" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Legenda</h4>
                <div class="space-y-2.5">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="layer-hutan" checked class="sr-only peer">
                        <div
                            class="w-5 h-5 rounded-md bg-emerald-500/40 border-2 border-emerald-500 peer-checked:bg-emerald-500 transition-all flex items-center justify-center">
                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span id="label-legend-hutan" class="text-sm text-gray-300 group-hover:text-white transition-colors">Kawasan Hutan</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="layer-tambang" checked class="sr-only peer">
                        <div
                            class="w-5 h-5 rounded-md bg-orange-500/40 border-2 border-orange-500 peer-checked:bg-orange-500 transition-all flex items-center justify-center">
                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span id="label-legend-tambang" class="text-sm text-gray-300 group-hover:text-white transition-colors">Wilayah Tambang</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="layer-overlap" checked class="sr-only peer">
                        <div
                            class="w-5 h-5 rounded-md bg-red-500/40 border-2 border-red-500 peer-checked:bg-red-500 transition-all flex items-center justify-center">
                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span id="label-legend-overlap" class="text-sm text-gray-300 group-hover:text-white transition-colors">Area Overlap</span>
                    </label>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-700/50">
                    <h5 id="label-forest-classification" class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.18em] mb-3">Klasifikasi Kawasan Hutan</h5>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#3c5b00;"></span>
                                <span id="label-forest-class-hl" class="text-gray-300">Hutan Lindung</span>
                            </div>
                            <span class="text-[#3c5b00] font-semibold">HL</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#FFFF00;"></span>
                                <span id="label-forest-class-hp" class="text-gray-300">Hutan Produksi Tetap</span>
                            </div>
                            <span class="text-[#FFFF00] font-semibold">HP</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#70A800;"></span>
                                <span id="label-forest-class-hpt" class="text-gray-300">Hutan Produksi Terbatas</span>
                            </div>
                            <span class="text-[#70A800] font-semibold">HPT</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#FFAA00;"></span>
                                <span id="label-forest-class-hpk" class="text-gray-300">Hutan Produksi Konversi</span>
                            </div>
                            <span class="text-[#FFAA00] font-semibold">HPK</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#9C10B5;"></span>
                                <span id="label-forest-class-conservation" class="text-gray-300">Kawasan Konservasi</span>
                            </div>
                            <span class="text-[#C15BDB] font-semibold">KSA/KPA</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm border border-white/10" style="background:#00C5FF;"></span>
                                <span id="label-forest-class-marine" class="text-gray-300">Konservasi Laut</span>
                            </div>
                            <span class="text-[#00C5FF] font-semibold">KKL</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="info-panel"
                class="absolute top-0 right-0 z-30 h-full w-full max-w-[42rem] bg-gray-900/95 pt-20 backdrop-blur-2xl border-l border-gray-700/50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto">
                <button id="close-info-panel"
                    class="dashboard-panel-close absolute top-24 right-4 flex h-8 w-8 items-center justify-center rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="p-6">
                    <div class="mb-6">
                        <h2 id="info-nama" class="text-xl font-bold text-white mb-1">-</h2>
                        <p id="info-jenis" class="text-sm text-gray-400">-</p>
                    </div>

                    <div id="info-share-section" class="mb-6 hidden">
                        <h3 id="label-info-share-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Share :</h3>
                        <div class="rounded-2xl border border-gray-700/30 bg-gray-800/35 p-2">
                            <div class="dashboard-share-list">
                                <a id="share-whatsapp" href="#" target="_blank" rel="noopener"
                                    class="dashboard-share-button share-button-whatsapp gap-1.5 border border-green-500/20 bg-green-500/10 text-center text-green-200 transition hover:bg-green-500/20">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19.05 4.94A9.9 9.9 0 0012 2C6.48 2 2 6.48 2 12c0 1.76.46 3.49 1.33 5.02L2 22l5.12-1.3A9.96 9.96 0 0012 22c5.52 0 10-4.48 10-10 0-2.67-1.04-5.18-2.95-7.06zM12 20.13c-1.5 0-2.97-.4-4.26-1.16l-.3-.18-3.04.77.81-2.96-.2-.31A8.07 8.07 0 013.87 12c0-4.49 3.64-8.13 8.13-8.13 2.17 0 4.22.85 5.75 2.38A8.08 8.08 0 0120.13 12c0 4.49-3.64 8.13-8.13 8.13zm4.46-6.1c-.24-.12-1.43-.7-1.65-.77-.22-.08-.38-.12-.54.12-.16.24-.62.77-.76.93-.14.16-.28.18-.52.06-.24-.12-1-.37-1.9-1.18-.7-.62-1.17-1.39-1.3-1.63-.14-.24-.01-.37.1-.49.1-.1.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.69 2.58 4.1 3.61.57.24 1.02.39 1.36.5.57.18 1.1.16 1.51.1.46-.07 1.43-.58 1.63-1.14.2-.56.2-1.03.14-1.13-.05-.1-.2-.16-.44-.28z" />
                                    </svg>
                                    <span class="sm:hidden">WA</span>
                                    <span class="hidden sm:inline">WhatsApp</span>
                                </a>
                                <a id="share-email" href="#"
                                    class="dashboard-share-button share-button-email gap-1.5 border border-slate-500/20 bg-slate-500/10 text-center text-slate-200 transition hover:bg-slate-500/20">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16v12H4z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8l8 6 8-6" />
                                    </svg>
                                    <span class="sm:hidden">Mail</span>
                                    <span class="hidden sm:inline">Email</span>
                                </a>
                                <a id="share-telegram" href="#" target="_blank" rel="noopener"
                                    class="dashboard-share-button share-button-telegram gap-1.5 border border-sky-500/20 bg-sky-500/10 text-center text-sky-200 transition hover:bg-sky-500/20">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M9.78 15.17l-.4 5.65c.57 0 .81-.25 1.1-.54l2.65-2.54 5.49 4.02c1 .56 1.72.27 1.99-.93l3.6-16.88h.01c.31-1.47-.53-2.04-1.5-1.68L1.53 10.4c-1.43.56-1.41 1.35-.24 1.71l5.28 1.65L18.83 6c.58-.38 1.12-.17.69.21" />
                                    </svg>
                                    <span class="sm:hidden">TG</span>
                                    <span class="hidden sm:inline">Telegram</span>
                                </a>
                                <a id="share-x" href="#" target="_blank" rel="noopener"
                                    class="dashboard-share-button share-button-x gap-1.5 border border-white/15 bg-white/5 text-center text-white transition hover:bg-white/10">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18.9 2H22l-6.77 7.74L23 22h-6.1l-4.78-6.95L6.04 22H2.93l7.24-8.28L1 2h6.25l4.32 6.29L18.9 2zm-1.07 18h1.69L6.33 3.9H4.52L17.83 20z" />
                                    </svg>
                                    <span>X</span>
                                </a>
                                <a id="share-facebook" href="#" target="_blank" rel="noopener"
                                    class="dashboard-share-button share-button-facebook gap-1.5 border border-blue-500/20 bg-blue-500/10 text-center text-blue-200 transition hover:bg-blue-500/20">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.52 1.49-3.92 3.78-3.92 1.1 0 2.24.2 2.24.2v2.48H15.2c-1.24 0-1.62.77-1.62 1.56v1.87h2.77l-.44 2.91h-2.33V22c4.78-.76 8.42-4.92 8.42-9.94z" />
                                    </svg>
                                    <span class="sm:hidden">FB</span>
                                    <span class="hidden sm:inline">Facebook</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="info-company-section" class="mb-6 hidden">
                        <h3 id="label-info-company-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Profil Perusahaan</h3>
                        <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30 space-y-3">
                            <div>
                                <p id="label-info-company-name" class="text-xs text-gray-500">Perusahaan</p>
                                <p id="info-perusahaan" class="text-sm text-gray-200">-</p>
                            </div>
                            <div>
                                <p id="label-info-short-profile" class="text-xs text-gray-500">Profil</p>
                                <div id="info-profil-singkat"
                                    class="mt-2 text-sm text-gray-300 leading-relaxed [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5">-</div>
                            </div>
                        </div>
                    </div>

                    <div id="info-gallery-section" class="mb-6 hidden">
                        <h3 id="label-info-gallery-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dokumentasi</h3>
                        <div class="rounded-2xl border border-gray-700/30 bg-gray-800/35 p-3">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <p id="info-gallery-counter" class="text-[11px] font-medium uppercase tracking-[0.14em] text-gray-500">-</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="info-gallery-prev" class="gallery-slider-button" aria-label="Previous gallery photo">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button type="button" id="info-gallery-next" class="gallery-slider-button" aria-label="Next gallery photo">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div id="info-gallery-viewport" class="gallery-slider-viewport overflow-hidden rounded-2xl">
                                <div id="info-gallery-grid" class="gallery-slider-track"></div>
                            </div>
                            <div id="info-gallery-dots" class="gallery-slider-dots mt-3"></div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 id="label-info-license-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Izin</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30 sm:col-span-2">
                                <p id="label-info-sk" class="text-xs text-gray-500 mb-1">Nomor SK</p>
                                <p id="info-sk" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-status" class="text-xs text-gray-500 mb-1">Status</p>
                                <p id="info-status" class="text-sm font-medium">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-luas-sk" class="text-xs text-gray-500 mb-1">Luas SK</p>
                                <p id="info-luas-sk" class="text-sm font-bold text-white">-<span
                                        class="text-xs font-normal text-gray-500 ml-1">Ha</span></p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-tanggal-berlaku" class="text-xs text-gray-500 mb-1">Tanggal Berlaku</p>
                                <p id="info-tanggal-berlaku" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-tanggal-berakhir" class="text-xs text-gray-500 mb-1">Tanggal Berakhir</p>
                                <p id="info-tanggal-berakhir" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-kegiatan" class="text-xs text-gray-500 mb-1">Kegiatan</p>
                                <p id="info-kegiatan" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-jenis-izin" class="text-xs text-gray-500 mb-1">Jenis Izin</p>
                                <p id="info-jenis-izin" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-nama-provinsi" class="text-xs text-gray-500 mb-1">Nama Provinsi</p>
                                <p id="info-nama-provinsi" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-nama-kabupaten" class="text-xs text-gray-500 mb-1">Nama Kabupaten</p>
                                <p id="info-nama-kabupaten" class="text-sm font-medium text-gray-200">-</p>
                            </div>
                            <div class="sm:col-span-2 bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                                <p id="label-info-lokasi" class="text-xs text-gray-500 mb-1">Lokasi</p>
                                <p id="info-lokasi" class="text-sm font-medium text-gray-200 leading-relaxed">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/20">
                            <p id="label-info-luas-overlap" class="text-xs text-red-400/70 mb-1">Luas Overlap</p>
                            <p id="info-luas-overlap" class="text-sm font-bold text-red-400">-<span
                                    class="text-xs font-normal text-red-400/60 ml-1">Ha</span></p>
                        </div>
                    </div>

                    <div id="info-overlap-section" class="mb-6 hidden">
                        <h3 id="label-info-overlap-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Rincian Overlap per Kawasan</h3>
                        <div class="bg-gray-800/30 rounded-2xl border border-gray-700/30 p-4 mb-4">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <div>
                                    <p id="label-info-overlap-composition" class="text-xs text-gray-500 uppercase tracking-[0.18em]">Komposisi Overlap</p>
                                    <p id="label-info-overlap-description" class="text-sm text-gray-300 mt-1">Proporsi total overlap berdasarkan klasifikasi kawasan hutan</p>
                                </div>
                                <div class="text-right">
                                    <p id="label-info-total-overlap" class="text-xs text-gray-500">Total Overlap</p>
                                    <p id="info-overlap-total" class="text-sm font-bold text-white">-</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-[180px,1fr] gap-4 items-center">
                                <div id="info-overlap-chart" class="flex items-center justify-center min-h-[180px]">
                                </div>
                                <div id="info-overlap-chart-legend" class="space-y-2">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="info-impact-section" class="hidden">
                        <h3 id="label-info-impact-section" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dampak</h3>
                        <div class="space-y-3">
                            <div id="dampak-lingkungan-card"
                                class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30 hidden">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                    <p id="label-impact-environmental" class="text-xs font-medium text-emerald-400">Lingkungan</p>
                                </div>
                                <div id="info-dampak-lingkungan"
                                    class="text-sm text-gray-300 leading-6 space-y-2 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1 [&_a]:text-emerald-400 [&_a]:underline [&_strong]:font-semibold [&_em]:italic">-</div>
                            </div>
                            <div id="dampak-sosial-card"
                                class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30 hidden">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                    <p id="label-impact-social" class="text-xs font-medium text-blue-400">Sosial</p>
                                </div>
                                <div id="info-dampak-sosial"
                                    class="text-sm text-gray-300 leading-6 space-y-2 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1 [&_a]:text-emerald-400 [&_a]:underline [&_strong]:font-semibold [&_em]:italic">-</div>
                            </div>
                            <div id="dampak-ekonomi-card"
                                class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30 hidden">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                    <p id="label-impact-economic" class="text-xs font-medium text-amber-400">Ekonomi</p>
                                </div>
                                <div id="info-dampak-ekonomi"
                                    class="text-sm text-gray-300 leading-6 space-y-2 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1 [&_a]:text-emerald-400 [&_a]:underline [&_strong]:font-semibold [&_em]:italic">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute right-4 top-24 z-20">
                <div
                    class="dashboard-basemap-switch bg-gray-900/80 backdrop-blur-xl border border-gray-700/50 rounded-xl overflow-hidden shadow-lg">
                    <button id="btn-basemap-dark"
                        class="basemap-btn active px-3 py-2 text-xs font-medium text-white bg-emerald-600/30 border-b border-gray-700/50 block w-full text-left hover:bg-gray-700/50 transition-all">
                        <span id="label-basemap-dark">🌙 Dark</span>
                    </button>
                    <button id="btn-basemap-satellite"
                        class="basemap-btn px-3 py-2 text-xs font-medium text-gray-400 block w-full text-left hover:bg-gray-700/50 hover:text-white transition-all">
                        <span id="label-basemap-satellite">🛰️ Satellite</span>
                    </button>
                    <button id="btn-basemap-streets"
                        class="basemap-btn px-3 py-2 text-xs font-medium text-gray-400 block w-full text-left hover:bg-gray-700/50 hover:text-white transition-all">
                        <span id="label-basemap-streets">🗺️ Streets</span>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <div id="gallery-modal"
        class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md px-4 py-6 sm:px-8">
        <div class="relative mx-auto flex h-full max-w-6xl flex-col justify-center">
            <button id="gallery-close"
                class="absolute right-0 top-0 z-10 flex h-10 w-10 items-center justify-center rounded-xl bg-gray-800/80 text-gray-300 transition-colors hover:bg-gray-700 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="relative flex h-full min-h-[420px] items-center justify-center overflow-hidden rounded-3xl border border-gray-800/70 bg-gray-900/60 pt-12 shadow-2xl">
                <div class="absolute left-6 top-6 z-10">
                    <p id="label-gallery-title" class="text-xs uppercase tracking-[0.18em] text-gray-500">Galeri Foto</p>
                    <p id="gallery-counter" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>

                <button id="gallery-prev"
                    class="absolute left-4 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-gray-950/70 text-gray-200 transition-colors hover:bg-gray-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <img id="gallery-image" src="" alt="Dokumentasi Tambang" class="max-h-[78vh] w-full object-contain">

                <button id="gallery-next"
                    class="absolute right-4 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-gray-950/70 text-gray-200 transition-colors hover:bg-gray-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/map.js') }}"></script>
    @endpush
</x-layout>
