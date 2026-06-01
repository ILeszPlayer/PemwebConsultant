<nav x-data="{ open: false, darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{'bg-gray-900 border-gray-700': darkMode, 'bg-white border-[#E5E7EB]': !darkMode}" class="border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div x-bind:class="darkMode ? 'text-white' : 'text-gray-800'">
                            <x-application-logo class="block h-9 w-auto fill-current" />
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(auth()->user()->role === 'user')
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.index')">
                            {{ __('Layanan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('sessions.index')" :active="request()->routeIs('sessions.index')">
                            {{ __('Riwayat Sesi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">
                            {{ __('Jurnal') }}
                        </x-nav-link>
                        <x-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                            {{ __('Artikel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('sos.index')" :active="request()->routeIs('sos.*')">
                            🆘 {{ __('SOS') }}
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->role === 'doctor')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            🩺 {{ __('Monitoring') }}
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            ⚙️ {{ __('Admin') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')">
                            {{ __('Artikel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                            {{ __('Layanan') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <a href="{{ route('sos.index') }}" class="p-2 text-rose-400 hover:text-rose-600 transition" title="Butuh Bantuan?">
                    🆘
                </a>
                {{-- Dark Mode Toggle --}}
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode);" class="p-2 rounded-xl transition" :class="darkMode ? 'text-yellow-400 hover:bg-gray-700' : 'text-gray-400 hover:bg-gray-100'">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition gap-2" :class="darkMode ? 'text-gray-300 bg-gray-800 hover:text-white' : 'text-gray-500 bg-white hover:text-gray-700'"">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-6 h-6 rounded-full object-cover">
                            @else
                                <span class="text-base">👤</span>
                            @endif
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')">
                                {{ __('Panel Admin') }}
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md" :class="darkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-400 hover:text-gray-500 hover:bg-gray-100'">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->role === 'user')
                        <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.index')">
                            {{ __('Layanan') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('sessions.index')" :active="request()->routeIs('sessions.index')">
                            {{ __('Riwayat Sesi') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">
                            {{ __('Jurnal') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                            {{ __('Artikel') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('sos.index')" :active="request()->routeIs('sos.*')">
                            🆘 {{ __('SOS Darurat') }}
                        </x-responsive-nav-link>
                    @endif
                    @if(auth()->user()->role === 'doctor')
                        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            🩺 {{ __('Monitoring') }}
                        </x-responsive-nav-link>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            ⚙️ {{ __('Admin') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Pengguna') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')">
                            {{ __('Artikel') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                            {{ __('Layanan') }}
                        </x-responsive-nav-link>
                    @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3">
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-10 h-10 rounded-full object-cover">
                @else
                    <span class="text-2xl">👤</span>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
