<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin - Indonesia Mining Watch' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen overflow-hidden">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside
            class="w-64 bg-gray-900/90 backdrop-blur-xl border-r border-gray-800/50 flex-shrink-0 flex flex-col z-20 hidden md:flex">
            <div class="p-6 border-b border-gray-800/50">
                <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.6 9h16.8M3.6 15h16.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                        </svg>
                    </div>
                    <span
                        class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Indonesia Mining Watch</span>
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                {{-- Sidebar Links --}}
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-3">Main Menu</div>

                <a href="{{ route('admin.wilayah-tambang.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.wilayah-tambang.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload GeoJSON
                </a>

                <a href="{{ route('detail-tambang.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('detail-tambang.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.6 9h16.8M3.6 15h16.8" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                    </svg>
                    Perusahaan
                </a>

                <a href="{{ route('admin.jenis-tambang.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.jenis-tambang.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h10M7 12h6m-6 5h10M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                    Jenis Tambang
                </a>

                <a href="{{ route('admin.about.edit') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.about.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                    </svg>
                    Halaman About
                </a>

                @if(auth()->user()?->isSuperadmin())
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5V9H2v11h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7m4-11a3 3 0 100-6 3 3 0 000 6zm6 2a2 2 0 012 2v1m-2-3a2 2 0 00-2 2v1M7 11a2 2 0 00-2 2v1m2-3a2 2 0 012 2v1" />
                        </svg>
                        User Admin
                    </a>
                @endif

                <details class="group">
                    <summary
                        class="list-none cursor-pointer flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.profile.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Profil Admin
                        </span>
                        <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="mt-2 ml-4 space-y-2">
                        <a href="{{ route('admin.profile.show') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L18.414 8A2 2 0 0119 9.414V19a2 2 0 01-2 2z" />
                            </svg>
                            Ganti Password
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </details>
            </nav>

            <div class="p-4 border-t border-gray-800/50 space-y-2">
                <a href="/"
                    class="flex items-center justify-center gap-2 px-4 py-2 text-sm text-gray-400 hover:text-white bg-gray-800/50 hover:bg-gray-700/50 rounded-lg transition-all w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Peta
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header -->
            <header
                class="md:hidden bg-gray-900 border-b border-gray-800/50 p-4 flex justify-between items-center z-20">
                <span
                    class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Indonesia Mining Watch
                    Admin</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.wilayah-tambang.index') }}" class="text-sm text-gray-400">GeoJSON</a>
                    <a href="{{ route('detail-tambang.index') }}" class="text-sm text-gray-400">Perusahaan</a>
                    <a href="{{ route('admin.jenis-tambang.index') }}" class="text-sm text-gray-400">Komoditas</a>
                    <a href="{{ route('admin.about.edit') }}" class="text-sm text-gray-400">About</a>
                    @if(auth()->user()?->isSuperadmin())
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400">Users</a>
                    @endif
                    <details class="group relative">
                        <summary class="list-none cursor-pointer text-sm text-gray-400">Profil</summary>
                        <div class="absolute right-0 mt-2 w-44 rounded-lg border border-gray-700/50 bg-gray-900 p-2 shadow-xl">
                            <a href="{{ route('admin.profile.show') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:bg-gray-800/70">
                                Ganti Password
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-md text-sm text-red-400 hover:bg-red-500/10">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto px-4 md:px-8 py-8 relative">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div
                        class="mb-6 mx-auto max-w-7xl p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm text-emerald-400 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="max-w-8xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')

    <!-- Quill.js Rich Text Editor (Free/No API Key) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        /* Quill Dark Mode Overrides to match Indonesia Mining Watch theme */
        .ql-toolbar.ql-snow {
            border: 1px solid rgba(51, 65, 85, 0.5);
            background-color: #0f172a;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .ql-container.ql-snow {
            border: 1px solid rgba(51, 65, 85, 0.5);
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            background-color: #1e293b;
            color: #cbd5e1;
            font-family: inherit;
            font-size: 0.875rem;
            max-width: 100%;
            overflow: hidden;
        }

        .ql-editor {
            min-height: 110px;
            max-height: 220px;
            overflow-y: auto;
            word-break: break-word;
        }

        .ql-toolbar.ql-snow {
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            box-sizing: border-box;
        }

        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 6px;
        }

        .rich-editor-shell {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .rich-editor-shell .ql-toolbar.ql-snow,
        .rich-editor-shell .ql-container.ql-snow {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        @media (min-width: 1280px) {
            .rich-editor-shell .ql-toolbar.ql-snow {
                white-space: nowrap;
            }
        }

        /* Toolbar Icon Colors */
        .ql-snow .ql-stroke {
            stroke: #94a3b8;
        }

        .ql-snow .ql-fill {
            fill: #94a3b8;
        }

        .ql-snow .ql-picker {
            color: #94a3b8;
        }

        .ql-snow .ql-picker.ql-expanded .ql-picker-label {
            color: #cbd5e1;
        }

        .ql-snow .ql-picker-options {
            background-color: #1e293b;
            border-color: rgba(51, 65, 85, 0.5);
        }

        button:hover .ql-stroke {
            stroke: #cbd5e1 !important;
        }

        button:hover .ql-fill {
            fill: #cbd5e1 !important;
        }

        .ql-snow .ql-picker-item:hover {
            color: #cbd5e1 !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Find all textareas with the rich-editor class
            const editors = document.querySelectorAll('textarea.rich-editor');

            editors.forEach(textarea => {
                // Hide the original textarea
                textarea.style.display = 'none';

                // Create a div container for Quill right after the textarea
                const quillContainer = document.createElement('div');
                quillContainer.className = 'rich-editor-shell';
                if (textarea.id) {
                    quillContainer.id = `${textarea.id}-editor`;
                    quillContainer.dataset.editorFor = textarea.id;
                }
                textarea.parentNode.insertBefore(quillContainer, textarea.nextSibling);

                // Set the initial content
                quillContainer.innerHTML = textarea.value;

                // Initialize Quill
                const quill = new Quill(quillContainer, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                            ['blockquote', 'code-block'],
                            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],          // outdent/indent
                            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                            [{ 'align': [] }],
                            ['clean']                                         // remove formatting button
                        ]
                    }
                });

                const rows = Number(textarea.getAttribute('rows') || 4);
                const editorMinHeight = Math.max(96, rows * 28);
                quill.root.style.minHeight = `${editorMinHeight}px`;

                // Sync Quill content back to the hidden textarea on form submit
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        // Only save if it's not empty, otherwise save empty string
                        textarea.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
                    });
                }
            });
        });
    </script>
</body>

</html>
