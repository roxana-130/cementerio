<nav x-data="{ open: false }" class="bg-emerald-900 border-b border-emerald-800 text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-6">
                <!-- Logo / Titulo Cementerio -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 font-bold text-lg text-emerald-100 hover:text-white">
                        <span>Cementerio General de Sacaba</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:flex">
                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-emerald-100 hover:text-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Difuntos (Placeholder) --}}
                    <x-nav-link href="#" class="text-emerald-200 hover:text-white">
                        {{ __('Difuntos') }}
                    </x-nav-link>

                    {{-- Agenda (Placeholder) --}}
                    <x-nav-link href="#" class="text-emerald-200 hover:text-white">
                        {{ __('Agenda') }}
                    </x-nav-link>

                    {{-- Mapa (Placeholder) --}}
                    <x-nav-link href="#" class="text-emerald-200 hover:text-white">
                        {{ __('Mapa') }}
                    </x-nav-link>

                    {{-- Ítems visibles solo para el rol Administrador --}}
                    @if (Auth::user() && Auth::user()->rol === 'administrador')
                        {{-- Historial --}}
                        <x-nav-link href="#" class="text-emerald-200 hover:text-white">
                            {{ __('Historial') }}
                        </x-nav-link>

                        {{-- Panteoneros --}}
                        <x-nav-link href="#" class="text-emerald-200 hover:text-white">
                            {{ __('Panteoneros') }}
                        </x-nav-link>

                        {{-- Usuarios --}}
                        <x-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" class="text-emerald-100 hover:text-white">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-emerald-700 text-sm leading-4 font-medium rounded-md text-emerald-100 bg-emerald-800 hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} <span class="text-xs text-emerald-300">({{ ucfirst(Auth::user()->rol) }})</span></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-emerald-200 hover:text-white hover:bg-emerald-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-emerald-950">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="#" class="text-emerald-200">
                {{ __('Difuntos') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="#" class="text-emerald-200">
                {{ __('Agenda') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="#" class="text-emerald-200">
                {{ __('Mapa') }}
            </x-responsive-nav-link>

            @if (Auth::user() && Auth::user()->rol === 'administrador')
                <x-responsive-nav-link href="#" class="text-emerald-200">
                    {{ __('Historial') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="#" class="text-emerald-200">
                    {{ __('Panteoneros') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" class="text-white">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-emerald-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-emerald-300">{{ Auth::user()->email }} ({{ ucfirst(Auth::user()->rol) }})</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-emerald-200">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-emerald-200">
                        {{ __('Cerrar sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
