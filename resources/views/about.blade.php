<x-layout
    :title="$pageTitle ?? 'Tentang Indonesia Mining Watch'"
    :meta-description="$metaDescription ?? 'Informasi tentang Indonesia Mining Watch.'"
    :canonical-url="$canonicalUrl ?? route('about')"
    :og-title="$pageTitle ?? 'Tentang Indonesia Mining Watch'"
    :og-description="$metaDescription ?? 'Informasi tentang Indonesia Mining Watch.'">
    @push('styles')
        <style>
            #about-root {
                --about-bg: radial-gradient(circle at top, rgba(16, 185, 129, 0.12), transparent 38%), linear-gradient(180deg, #020617 0%, #020617 100%);
                --about-nav-bg: rgba(2, 6, 23, 0.84);
                --about-nav-border: rgba(255, 255, 255, 0.1);
                --about-surface: rgba(15, 23, 42, 0.78);
                --about-surface-border: rgba(255, 255, 255, 0.1);
                --about-text: #f8fafc;
                --about-muted: #94a3b8;
                --about-soft: #cbd5e1;
                --about-accent-bg: rgba(16, 185, 129, 0.16);
                --about-accent-text: #6ee7b7;
                --about-control-bg: rgba(15, 23, 42, 0.72);
                --about-control-border: rgba(51, 65, 85, 0.65);
            }

            #about-root.theme-light {
                --about-bg: radial-gradient(circle at top, rgba(16, 185, 129, 0.14), transparent 38%), linear-gradient(180deg, #ecfeff 0%, #f8fafc 100%);
                --about-nav-bg: rgba(255, 255, 255, 0.92);
                --about-nav-border: rgba(148, 163, 184, 0.28);
                --about-surface: rgba(255, 255, 255, 0.92);
                --about-surface-border: rgba(203, 213, 225, 0.9);
                --about-text: #0f172a;
                --about-muted: #475569;
                --about-soft: #334155;
                --about-accent-bg: rgba(16, 185, 129, 0.12);
                --about-accent-text: #047857;
                --about-control-bg: rgba(255, 255, 255, 0.96);
                --about-control-border: rgba(203, 213, 225, 0.95);
            }

            #about-root {
                min-height: 100vh;
                background: var(--about-bg);
                color: var(--about-text);
                transition: background 180ms ease, color 180ms ease;
            }

            .about-navbar {
                position: sticky;
                top: 0;
                z-index: 50;
                border-bottom: 1px solid var(--about-nav-border);
                background: var(--about-nav-bg);
                backdrop-filter: blur(20px);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
                overflow: visible;
            }

            .about-navbar-shell {
                margin: 0 auto;
                display: flex;
                max-width: 80rem;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.75rem 1rem;
            }

            .about-home-link,
            .about-lang-shell,
            .about-theme-trigger,
            .about-theme-menu {
                border: 1px solid var(--about-control-border);
                background: var(--about-control-bg);
                color: var(--about-soft);
            }

            .about-home-link,
            .about-theme-trigger,
            .about-lang-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 0.75rem;
                min-height: 2.75rem;
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 160ms ease;
            }

            .about-home-link:hover,
            .about-theme-trigger:hover,
            .about-lang-btn:hover {
                color: var(--about-text);
                border-color: rgba(16, 185, 129, 0.3);
            }

            .about-lang-shell {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                border-radius: 0.75rem;
                padding: 0.25rem;
            }

            .about-lang-btn.is-active {
                background: var(--about-accent-bg);
                color: var(--about-text);
            }

            .about-brand {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 0.75rem;
            }

            .about-brand-mark {
                display: flex;
                height: 2.75rem;
                width: 2.75rem;
                flex-shrink: 0;
                align-items: center;
                justify-content: center;
                border-radius: 0.75rem;
                background: linear-gradient(to bottom right, #10b981, #0d9488);
                box-shadow: 0 10px 24px rgba(16, 185, 129, 0.2);
            }

            .about-brand-title {
                background: linear-gradient(to right, #34d399, #5eead4);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                font-size: 1rem;
                font-weight: 700;
                line-height: 1.2;
            }

            .about-brand-subtitle {
                margin-top: 0.125rem;
                color: #6b7280;
                font-size: 0.75rem;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .about-theme-wrap {
                position: relative;
            }

            .about-theme-trigger {
                gap: 0.5rem;
                min-width: 5rem;
                justify-content: space-between;
            }

            .about-theme-menu {
                position: fixed;
                z-index: 40;
                right: 0;
                min-width: 12rem;
                border-radius: 1rem;
                padding: 0.5rem;
                box-shadow: 0 24px 48px rgba(15, 23, 42, 0.22);
                transform-origin: top right;
                transition: all 150ms ease-out;
            }

            .about-theme-menu[data-open="true"] {
                opacity: 1;
                transform: translateY(0) scale(1);
                visibility: visible;
                pointer-events: auto;
            }

            .about-theme-menu[data-open="false"] {
                opacity: 0;
                transform: translateY(0.35rem) scale(0.98);
                visibility: hidden;
                pointer-events: none;
            }

            .about-theme-option {
                display: flex;
                width: 100%;
                align-items: center;
                justify-content: space-between;
                border-radius: 0.8rem;
                padding: 0.75rem 0.85rem;
                font-size: 0.875rem;
                color: var(--about-soft);
                transition: background 160ms ease, color 160ms ease;
            }

            .about-theme-option:hover,
            .about-theme-option.is-active {
                background: var(--about-accent-bg);
                color: var(--about-text);
            }

            #about-root.theme-light .about-theme-option.is-active {
                color: #0f172a;
            }

            .about-content-shell {
                margin: 0 auto;
                max-width: 80rem;
                padding: 2rem 1.5rem 4rem;
            }

            .about-surface {
                overflow: hidden;
                border: 1px solid var(--about-nav-border);
                border-radius: 2rem;
                background: var(--about-surface);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
                backdrop-filter: blur(18px);
            }

            .about-hero {
                border-bottom: 1px solid var(--about-nav-border);
                background: linear-gradient(90deg, rgba(16, 185, 129, 0.12), rgba(20, 184, 166, 0.08), transparent);
                padding: 2.5rem 2rem;
            }

            .about-section {
                padding: 2.5rem 2rem;
            }

            .about-kicker {
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.28em;
                text-transform: uppercase;
                color: var(--about-accent-text);
            }

            .about-title {
                margin-top: 0.75rem;
                font-size: clamp(2rem, 4vw, 3rem);
                font-weight: 800;
                color: var(--about-text);
            }

            .about-prose {
                max-width: none;
                color: var(--about-soft);
            }

            .about-prose :where(h1, h2, h3, h4, h5, h6, strong) {
                color: var(--about-text);
            }

            .about-prose :where(p, li) {
                color: var(--about-soft);
            }

            .about-prose a {
                color: var(--about-accent-text);
            }

            @media (max-width: 640px) {
                .about-navbar-shell {
                    flex-wrap: wrap;
                    padding: 0.875rem 1rem;
                }

                .about-brand-copy {
                    max-width: calc(100vw - 9rem);
                }

                .about-content-shell {
                    padding: 1.5rem 1rem 3rem;
                }

                .about-hero,
                .about-section {
                    padding: 1.5rem;
                }
            }
        </style>
    @endpush

    <div id="about-root" class="theme-dark">
        <header class="about-navbar">
            <div class="about-navbar-shell">
                <div class="about-brand">
                    <a href="{{ route('dashboard') }}" class="about-brand-mark">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.6 9h16.8M3.6 15h16.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                        </svg>
                    </a>
                    <div class="about-brand-copy min-w-0">
                        <h1 class="about-brand-title truncate">Indonesia Mining Watch</h1>
                        <p class="about-brand-subtitle">{{ $activeLanguage === 'en' ? 'Mining Area Monitoring' : 'Pantau Wilayah Pertambangan' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}"
                        class="about-home-link gap-2 font-medium">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
                        </svg>
                        Dashboard
                    </a>

                    <div class="about-lang-shell">
                        <a href="{{ route('about', ['lang' => 'id']) }}"
                            data-about-lang="id"
                            class="about-lang-btn {{ $activeLanguage === 'id' ? 'is-active' : '' }}">
                            ID
                        </a>
                        <a href="{{ route('about', ['lang' => 'en']) }}"
                            data-about-lang="en"
                            class="about-lang-btn {{ $activeLanguage === 'en' ? 'is-active' : '' }}">
                            EN
                        </a>
                    </div>

                    <div class="about-theme-wrap">
                        <button type="button" id="about-theme-trigger" class="about-theme-trigger">
                            <span class="flex items-center gap-2">
                                <svg id="about-theme-current-dark-icon" class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.8A9 9 0 1111.2 3 7 7 0 0021 12.8z" />
                                </svg>
                                <svg id="about-theme-current-light-icon" class="hidden h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.5m0 13V21m9-9h-2.5M5.5 12H3m14.864 6.364-1.768-1.768M7.904 7.904 6.136 6.136m11.728 0-1.768 1.768M7.904 16.096l-1.768 1.768M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                </svg>
                                <span id="about-theme-label">Dark</span>
                            </span>
                            <svg id="about-theme-chevron" class="h-4 w-4 text-gray-500 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="about-theme-menu" data-open="false" class="about-theme-menu">
                            <button type="button" class="about-theme-option" data-theme-option="dark">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.8A9 9 0 1111.2 3 7 7 0 0021 12.8z" />
                                    </svg>
                                    <span>Dark</span>
                                </span>
                                <span id="about-theme-check-dark">•</span>
                            </button>
                            <button type="button" class="about-theme-option" data-theme-option="light">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.5m0 13V21m9-9h-2.5M5.5 12H3m14.864 6.364-1.768-1.768M7.904 7.904 6.136 6.136m11.728 0-1.768 1.768M7.904 16.096l-1.768 1.768M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                    </svg>
                                    <span>Light</span>
                                </span>
                                <span id="about-theme-check-light" class="hidden">•</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="about-content-shell">
            <section class="about-surface">
                <div class="about-hero">
                    <p class="about-kicker">About</p>
                    <h1 class="about-title">{{ $pageTitle }}</h1>
                </div>

                <div class="about-section">
                    <div class="about-prose prose max-w-none prose-headings:font-semibold prose-p:leading-7 prose-a:no-underline hover:prose-a:underline prose-li:leading-7">
                        {!! $pageContent !!}
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const activeLanguage = @json($activeLanguage);
                const aboutRoot = document.getElementById('about-root');
                const themeTrigger = document.getElementById('about-theme-trigger');
                const themeMenu = document.getElementById('about-theme-menu');
                const themeLabel = document.getElementById('about-theme-label');
                const themeChevron = document.getElementById('about-theme-chevron');
                const currentDarkIcon = document.getElementById('about-theme-current-dark-icon');
                const currentLightIcon = document.getElementById('about-theme-current-light-icon');
                const darkCheck = document.getElementById('about-theme-check-dark');
                const lightCheck = document.getElementById('about-theme-check-light');
                const themeOptions = Array.from(document.querySelectorAll('[data-theme-option]'));
                let currentTheme = localStorage.getItem('dashboardTheme') === 'light' ? 'light' : 'dark';
                let themeMenuOpen = false;

                localStorage.setItem('dashboardLocale', activeLanguage);
                document.cookie = `preferred_locale=${activeLanguage}; path=/; max-age=31536000; SameSite=Lax`;

                const positionThemeMenu = () => {
                    if (!themeTrigger || !themeMenu) {
                        return;
                    }

                    const rect = themeTrigger.getBoundingClientRect();
                    themeMenu.style.top = `${rect.bottom + 8}px`;
                    themeMenu.style.right = `${Math.max(window.innerWidth - rect.right, 12)}px`;
                    themeMenu.style.width = `${rect.width}px`;
                    themeMenu.style.minWidth = `${rect.width}px`;
                    themeMenu.style.left = 'auto';
                };

                const setThemeMenuOpen = (open) => {
                    themeMenuOpen = open;

                    if (!themeMenu) {
                        return;
                    }

                    if (open) {
                        positionThemeMenu();
                    }

                    themeMenu.dataset.open = open ? 'true' : 'false';
                    themeChevron?.classList.toggle('rotate-180', open);
                };

                const applyAboutTheme = () => {
                    aboutRoot.classList.toggle('theme-dark', currentTheme === 'dark');
                    aboutRoot.classList.toggle('theme-light', currentTheme === 'light');
                    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
                    themeLabel.textContent = currentTheme === 'light' ? 'Light' : 'Dark';
                    currentDarkIcon?.classList.toggle('hidden', currentTheme !== 'dark');
                    currentLightIcon?.classList.toggle('hidden', currentTheme !== 'light');
                    darkCheck.classList.toggle('hidden', currentTheme !== 'dark');
                    lightCheck.classList.toggle('hidden', currentTheme !== 'light');
                    themeOptions.forEach((button) => {
                        button.classList.toggle('is-active', button.dataset.themeOption === currentTheme);
                    });
                };

                applyAboutTheme();

                document.querySelectorAll('[data-about-lang]').forEach((link) => {
                    link.addEventListener('click', function () {
                        const nextLanguage = this.dataset.aboutLang === 'en' ? 'en' : 'id';
                        localStorage.setItem('dashboardLocale', nextLanguage);
                        document.cookie = `preferred_locale=${nextLanguage}; path=/; max-age=31536000; SameSite=Lax`;
                    });
                });

                themeTrigger?.addEventListener('click', function () {
                    setThemeMenuOpen(!themeMenuOpen);
                });

                themeOptions.forEach((button) => {
                    button.addEventListener('click', function () {
                        currentTheme = this.dataset.themeOption === 'light' ? 'light' : 'dark';
                        localStorage.setItem('dashboardTheme', currentTheme);
                        applyAboutTheme();
                        setThemeMenuOpen(false);
                    });
                });

                document.addEventListener('click', function (event) {
                    if (
                        themeMenuOpen &&
                        themeMenu &&
                        themeTrigger &&
                        !themeMenu.contains(event.target) &&
                        !themeTrigger.contains(event.target)
                    ) {
                        setThemeMenuOpen(false);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && themeMenuOpen) {
                        setThemeMenuOpen(false);
                    }
                });

                window.addEventListener('resize', function () {
                    if (themeMenuOpen) {
                        positionThemeMenu();
                    }
                });

                window.addEventListener('scroll', function () {
                    if (themeMenuOpen) {
                        positionThemeMenu();
                    }
                }, { passive: true });
            });
        </script>
    @endpush
</x-layout>
