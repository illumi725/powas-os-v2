@php
    $userinfo = optional(Auth::user()->userinfo);
    if ($userinfo && $userinfo->user_id != null) {
        $userfullname = Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname;
    }
@endphp

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                @if (Auth::user()->isActive())
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>

                    @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|board'))
                        @if (Auth::user()->hasRole('admin'))
                            <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                                <x-nav-link href="{{ route('powas') }}" :active="request()->routeIs('powas')">
                                    {{ __('POWAS Coop') }}
                                </x-nav-link>
                            </div>
                        @else
                            <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                                <x-nav-link href="{{ route('powas.records', ['powasID' => Auth::user()->powas_id]) }}"
                                    :active="request()->routeIs('powas.records', [
                                        'powasID' => Auth::user()->powas_id,
                                    ])">
                                    {{ __('Records') }}
                                </x-nav-link>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                                <x-nav-link
                                    href="{{ route('view-transactions', ['powasID' => Auth::user()->powas_id]) }}"
                                    :active="request()->routeIs('view-transactions', [
                                        'powasID' => Auth::user()->powas_id,
                                    ])">
                                    {{ __('Transactions') }}
                                </x-nav-link>
                            </div>
                        @endif
                    @endif

                    @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|collector-reader|board'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                            <x-nav-link href="{{ route('members') }}" :active="request()->routeIs('members')">
                                {{ __('Members') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if (Auth::user()->hasRole('admin'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                            <x-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                                {{ __('Users') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                            <x-nav-link href="{{ route('poca-brain') }}" :active="request()->routeIs('poca-brain')">
                                {{ __('POCA') }}
                            </x-nav-link>
                        </div>
                    @else
                        @can('edit powas preferences')
                            <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                                <x-nav-link href="{{ route('powas.show', ['powas_id' => Auth::user()->powas_id]) }}"
                                    :active="request()->routeIs('powas.show', ['powas_id' => Auth::user()->powas_id])">
                                    {{ __('POWAS Preferences') }}
                                </x-nav-link>
                            </div>
                        @endcan
                    @endif

                    @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|board'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
                            <x-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                                {{ __('Settings') }}
                            </x-nav-link>
                        </div>
                    @endif
                @endif

            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->username }}

                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        src="{{ Auth::user()->profile_photo_url ?? URL::asset('assets/user.png') }}"
                                        alt="{{ $userfullname ?? Auth::user()->username }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ $userfullname ?? Auth::user()->username }}

                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            @if (Auth::user()->isActive())
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                        {{ __('API Tokens') }}
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @if (Auth::user()->isActive())
            <div class="space-y-1">
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|board'))
                <div class="space-y-1">
                    @if (Auth::user()->hasRole('admin'))
                        <x-responsive-nav-link href="{{ route('powas') }}" :active="request()->routeIs('powas')">
                            {{ __('POWAS Coop') }}
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link
                            href="{{ route('powas.records', ['powasID' => Auth::user()->powas_id]) }}"
                            :active="request()->routeIs('powas.records', ['powasID' => Auth::user()->powas_id])">
                            {{ __('Records') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link
                            href="{{ route('view-transactions', ['powasID' => Auth::user()->powas_id]) }}"
                            :active="request()->routeIs('view-transactions', ['powasID' => Auth::user()->powas_id])">
                            {{ __('Transactions') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endif

            @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|board'))
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('members') }}" :active="request()->routeIs('members')">
                        {{ __('Members') }}
                    </x-responsive-nav-link>
                </div>
            @endif

            @if (Auth::user()->hasRole('admin'))
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                </div>
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('poca-brain') }}" :active="request()->routeIs('poca-brain')">
                        {{ __('POCA') }}
                    </x-responsive-nav-link>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('powas.show', ['powas_id' => Auth::user()->powas_id]) }}"
                        :active="request()->routeIs('powas.show', ['powas_id' => Auth::user()->powas_id])">
                        {{ __('POWAS Preferences') }}
                    </x-responsive-nav-link>
                </div>
            @endif

            @if (Auth::user()->hasRole('admin|president|vice-president|secretary|treasurer|auditor|board'))
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                </div>
            @endif
        @endif

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ $userfullname ?? Auth::user()->username }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                        {{ $userfullname ?? Auth::user()->username }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @if (Auth::user()->isActive())
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                            {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Team Management -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Team') }}
                        </div>

                        <!-- Team Settings -->
                        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                            :active="request()->routeIs('teams.show')">
                            {{ __('Team Settings') }}
                        </x-responsive-nav-link>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                                {{ __('Create New Team') }}
                            </x-responsive-nav-link>
                        @endcan

                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Switch Teams') }}
                            </div>

                            @foreach (Auth::user()->allTeams() as $team)
                                <x-switchable-team :team="$team" component="responsive-nav-link" />
                            @endforeach
                        @endif
                    @endif
                @endif


                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
