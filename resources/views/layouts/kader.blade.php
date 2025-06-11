<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - DengueCare</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
       
        @keyframes slideInRight {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        #file-preview-container {
            animation: fadeIn 0.3s ease-out;
        }

        /* Efek hover untuk tombol hapus */
        #remove-file-btn:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
       
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
       
        .animate-slide-in {
            animation: slideInRight 0.5s ease-out forwards;
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

        .virus-primary {
            background-color: #2563eb; /* blue-600 */
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
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header/Navbar -->
    <header x-data="{ mobileMenuOpen: false }">
        <nav class="virus-primary text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="{{ route('kader.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('/images/Logoputihkecil.png') }}" alt="DengueCare Logo" class="h-10 mr-2">
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <div class="flex space-x-6">
                            <a href="{{ route('kader.dashboard') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition {{ request()->routeIs('kader.dashboard') ? 'active-nav' : '' }}">
                                <i class="fas fa-home mr-1"></i> Beranda
                            </a>
                            <a href="{{ route('kader.forum') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-comments mr-1"></i> Forum
                            </a>
                            <a href="{{ route('kader.buku-panduan') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition ">
                                <i class="fas fa-book mr-1"></i> Panduan
                            </a>
                            <a href="{{ route('kader.video-pelatihan') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-graduation-cap mr-1"></i> Pelatihan
                            </a>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false, stayOpen: false }">
                            <button 
                                @click="open = !open; stayOpen = !stayOpen" 
                                @mouseenter="if(!stayOpen) open = true" 
                                @mouseleave="if(!stayOpen) open = false"
                                class="flex items-center space-x-2 focus:outline-none transition-colors duration-200 rounded-full p-1 hover:bg-blue-50"
                            >
                                <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-700"></i>
                                </div>
                                <span class="font-medium">{{ auth()->guard('kader')->user()->nama_lengkap }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-100"
                                @mouseenter="open = true"
                                @mouseleave="if(!stayOpen) open = false"
                                @click.away="open = false; stayOpen = false"
                            >
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-800">{{ auth()->guard('kader')->user()->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500 truncate">Kader Jumantik</p>
                                </div>
                                
                                <a 
                                    href="{{ route('kader.profile') }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-user-circle mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <a 
                                    href="{{ route('kader.settings') }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-cog mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <form method="POST" action="{{ route('kader.logout') }}">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center border-t border-gray-100"
                                    >
                                        <i class="fas fa-sign-out-alt mr-3 text-red-500 w-5 text-center"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">DengueCare</h3>
                    <p class="text-blue-200">Sistem pendukung kader kesehatan dalam pencegahan dan penanganan DBD.</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Kontak</h3>
                    <p class="text-blue-200">Email: kader@denguecare.id</p>
                    <p class="text-blue-200">Telepon: (021) 123-4567</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Panduan</h3>
                    <ul class="space-y-2 text-blue-200">
                        <li><a href="#" class="hover:text-white transition">Panduan Penggunaan</a></li>
                        <li><a href="#" class="hover:text-white transition">Protokol DBD</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-blue-700 pt-6 text-center text-blue-200">
                <p>&copy; {{ date('Y') }} DengueCare Kader. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @stack('scripts')
</body>
</html>