<x-layout title="Login Admin - Indonesia Mining Watch">
    <div class="min-h-dvh grid place-items-center bg-gray-950 p-6 relative overflow-hidden">

        {{-- Map Background Glow Effect --}}
        <div class="absolute inset-0 z-0">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-1/2 max-w-md mx-auto relative z-10">
            <div class="bg-gray-900/80 backdrop-blur-xl border border-gray-800/50 rounded-2xl shadow-2xl p-8">
                {{-- Header --}}
                <div class="text-center mb-8">
                    <div
                        class="w-14 h-14 mx-auto rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.6 9h16.8M3.6 15h16.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                        </svg>
                    </div>
                    <h1
                        class="text-2xl font-bold bg-linear-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">
                        Indonesia Mining Watch</h1>
                    <p class="text-sm text-gray-400 mt-2">Masuk untuk mengelola data wilayah pertambangan</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf

                    {{-- Error Summary --}}
                    @if($errors->any())
                        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                            <ul class="list-disc list-inside text-sm text-red-400">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                {{-- <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg> --}}
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40 transition-all placeholder-gray-500"
                                placeholder="admin@minwatch.com">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                {{-- <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg> --}}
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40 transition-all placeholder-gray-500"
                                placeholder="••••••••">
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between mt-8">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 bg-gray-800 border-gray-700 rounded text-emerald-500 focus:ring-emerald-500/40 focus:ring-offset-gray-900 transition-colors">
                            <span class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Ingat
                                Saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full py-3 px-4 mt-4 bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:ring-offset-2 focus:ring-offset-gray-900">
                        Masuk
                    </button>

                    <div class="text-center mt-8">
                        <a href="/"
                            class="text-sm text-gray-500 hover:text-emerald-400 transition-colors flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Peta Publik
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
