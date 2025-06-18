<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DengueCare')</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] { display: none !important; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
       
        @keyframes slideInRight {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
       
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
       
        .animate-slide-in {
            animation: slideInRight 0.5s ease-out forwards;
        }
       
        .step { display: none; }
        .step.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }
       
        .card-hover {
            transition: all 0.3s ease;
        }
       
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
       
        .btn-hover {
            transition: all 0.2s ease;
        }
       
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .active-nav {
            position: relative;
        }

        .active-nav::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: white;
            border-radius: 3px;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        @yield('custom-css')
    </style>
    @yield('header-scripts')
</head>
@stack('scripts')
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="{ showLogoutModal: false }">
    <!-- Header/Navbar -->
    <header x-data="{ mobileMenuOpen: false }">
        <nav class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="{{ route('warga.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('/images/Logoputihkecil.png') }}" alt="DengueCare Logo" class="h-10 mr-2">
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <div class="flex space-x-6">
                            <a href="{{ route('warga.dashboard') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition {{ request()->routeIs('warga.dashboard') ? 'active-nav' : '' }}">
                                <i class="fas fa-home mr-1"></i> Beranda
                            </a>
                            <a href="{{ route('warga.informasi') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition {{ request()->routeIs('warga.informasi') ? 'active-nav' : '' }}">
                                <i class="fas fa-info-circle mr-1"></i> Informasi
                            </a>
                            <a href="{{ route('warga.forum') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition {{ request()->routeIs('warga.forum.index') ? 'active-nav' : '' }}">
                                <i class="fas fa-comments mr-1"></i> Forum
                            </a>
                        </div>

                        <!-- Profile Dropdown - Improved Version -->
                        <div class="relative" x-data="{ open: false, stayOpen: false }">
                            <button 
                                @click="open = !open; stayOpen = false" 
                                @mouseenter="open = true"
                                class="flex items-center space-x-2 focus:outline-none"
                            >
                                @php
                                    $user = auth()->user();
                                    $profilePicUrl = asset('images/default-profile.jpg');
                                    
                                    if ($user && $user->profile_pict) {
                                        if (file_exists(public_path('uploads')) && is_link(public_path('storage'))) {
                                            $profilePicUrl = Storage::url($user->profile_pict);
                                        } else {
                                            $profilePicUrl = asset($user->profile_pict);
                                        }
                                    }
                                @endphp
                                
                                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-blue-200">
                                    @if($user && $user->profile_pict)
                                        <img src="{{ $profilePicUrl }}" 
                                            alt="Foto Profil {{ $user->nama_lengkap }}" 
                                            class="w-full h-full object-cover"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-blue-200 flex items-center justify-center" style="display: none;">
                                            <i class="fas fa-user text-blue-700"></i>
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-blue-200 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-700"></i>
                                        </div>
                                    @endif
                                </div>
                                <span class="font-medium">{{ auth()->user()->nama_lengkap }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>

                            <!-- Dropdown menu - Improved -->
                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-100"
                                @mouseenter="open = true; stayOpen = true"
                                @mouseleave="open = false; stayOpen = false"
                                @click.away="open = false; stayOpen = false"
                            >
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-blue-200">
                                            @if($user && $user->profile_pict)
                                                <img src="{{ $profilePicUrl }}" 
                                                    alt="Foto Profil {{ $user->nama_lengkap }}" 
                                                    class="w-full h-full object-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full bg-blue-200 flex items-center justify-center" style="display: none;">
                                                    <i class="fas fa-user text-blue-700"></i>
                                                </div>
                                            @else
                                                <div class="w-full h-full bg-blue-200 flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-700"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $user->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">warga</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <a 
                                    href="{{ route('warga.profile') }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-user-circle mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <a 
                                    href="{{ route('warga.profile.edit') }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-cog mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <button 
                                    @click="showLogoutModal = true; open = false" 
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center border-t border-gray-100"
                                >
                                    <i class="fas fa-sign-out-alt mr-3 text-red-500 w-5 text-center"></i>
                                    <span>Keluar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-show="mobileMenuOpen" x-transition class="md:hidden mt-4 pb-4 border-t border-blue-500">
                    <div class="flex flex-col space-y-2 mt-4">
                        <!-- Profile section for mobile -->
                        <div class="flex items-center space-x-3 py-2 px-4 bg-blue-700 rounded mb-2">
                            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-blue-200">
                                @if($user && $user->profile_pict)
                                    <img src="{{ $profilePicUrl }}" 
                                        alt="Foto Profil {{ $user->nama_lengkap }}" 
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full bg-blue-200 flex items-center justify-center" style="display: none;">
                                        <i class="fas fa-user text-blue-700"></i>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-blue-200 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-700"></i>
                                    </div>
                                @endif
                            </div>
                            <span class="font-medium">{{ $user->nama_lengkap }}</span>
                        </div>

                        <a href="{{ route('warga.dashboard') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition {{ request()->routeIs('warga.dashboard') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-home mr-2"></i> Beranda
                        </a>
                        <a href="{{ route('warga.informasi') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-info-circle mr-2"></i> Informasi
                        </a>
                        <a href="{{ route('warga.forum') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-comments mr-2"></i> Forum
                        </a>
                        <hr class="border-blue-500 my-2">
                        <a href="{{ route('warga.profile') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-user-circle mr-2"></i> Profil Saya
                        </a>
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-cog mr-2"></i> Pengaturan
                        </a>
                        <button @click="showLogoutModal = true; mobileMenuOpen = false" class="w-full text-left py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-6">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">DengueCare</h3>
                    <p class="text-blue-200">Solusi terpadu untuk pencegahan dan penanganan demam berdarah di lingkungan Anda.</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Kontak</h3>
                    <p class="text-blue-200">Email: info@denguecare.id</p>
                    <p class="text-blue-200">Telepon: (021) 123-4567</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Ikuti Kami</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-blue-200 hover:text-white transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-blue-200 hover:text-white transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-blue-200 hover:text-white transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-blue-700 pt-6 text-center text-blue-200">
                <p>&copy; {{ date('Y') }} DengueCare. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Logout Confirmation Modal -->
    <div x-cloak x-show="showLogoutModal"
        style="display: none;"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl" @click.away="showLogoutModal = false">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-sign-out-alt text-red-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Konfirmasi Keluar</h3>
            </div>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari akun Anda?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showLogoutModal = false"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200 font-medium">
                    Batal
                </button>
                <!-- Ganti dengan form -->
                <form action="{{ route('warga.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 font-medium flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    <script>
        // Base JS functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add any global JS functionality here
        });
    </script>
    @yield('footer-scripts')
</body>
</html>