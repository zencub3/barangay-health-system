
<nav x-data="{ open: false }"
     class="bg-white border-b border-[#dce9df] shadow-sm">

    <!-- Primary Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Barangay Health Logo and Name -->
            <div class="flex items-center">

                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 no-underline">

                    <!-- Healthcare Logo -->
                    <div class="flex items-center justify-center
                                w-10 h-10 rounded-xl
                                bg-[#216e4f] text-white
                                text-2xl font-bold">
                        +
                    </div>

                    <!-- System Name -->
                    <div class="flex flex-col">
                        <span class="text-[#184e37] font-bold text-base leading-tight">
                            Barangay Health
                        </span>

                        <span class="text-[#758b7b] text-xs leading-tight mt-1">
                            Management System
                        </span>
                    </div>

                </a>
            </div>

            <!-- Desktop Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2
                                   px-4 py-2 rounded-xl
                                   text-sm font-medium
                                   text-[#216e4f]
                                   bg-[#f2f7f3]
                                   border border-[#dce9df]
                                   hover:bg-[#e8f5ec]
                                   focus:outline-none
                                   transition duration-150">

                            <!-- User Name -->
                            <span>
                                {{ Auth::user()->name }}
                            </span>

                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 fill-current"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd" />
                            </svg>

                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <!-- Profile -->
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="-me-2 flex items-center sm:hidden">

                <button
                    type="button"
                    @click="open = ! open"
                    aria-label="Toggle navigation menu"
                    :aria-expanded="open"
                    class="inline-flex items-center justify-center
                           p-2 rounded-lg
                           text-[#216e4f]
                           hover:bg-[#e8f5ec]
                           focus:outline-none
                           transition duration-150">

                    <svg class="h-6 w-6"
                         stroke="currentColor"
                         fill="none"
                         viewBox="0 0 24 24">

                        <!-- Menu Icon -->
                        <path
                            :class="{'hidden': open, 'inline-flex': !open}"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <!-- Close Icon -->
                        <path
                            :class="{'hidden': !open, 'inline-flex': open}"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />

                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}"
         class="hidden sm:hidden border-t border-[#dce9df]">

        <!-- User Information -->
        <div class="px-4 pt-4 pb-3">

            <div class="font-semibold text-[#184e37] text-base">
                {{ Auth::user()->name }}
            </div>

            <div class="text-sm text-[#758b7b] mt-1">
                {{ Auth::user()->email }}
            </div>

        </div>

        <!-- Mobile Profile and Logout -->
        <div class="pt-2 pb-3 border-t border-[#dce9df] space-y-1">

            <!-- Profile -->
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-responsive-nav-link
                    :href="route('logout')"
                    onclick="event.preventDefault();
                             this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>

        </div>
    </div>

</nav>
