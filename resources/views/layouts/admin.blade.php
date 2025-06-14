<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DengueCare</title>
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

        .profile-picture-container {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid white;
            transition: all 0.2s ease;
            position: relative;
        }

        .profile-picture-container:hover {
            border-color: #93c5fd;
            transform: scale(1.05);
        }

        .default-profile-icon {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #bfdbfe; /* blue-200 */
            color: #1d4ed8; /* blue-700 */
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* Profile Image Styles */
        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 50%;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        /* Container untuk profile image dengan fallback */
        .profile-container {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-fallback {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1d4ed8;
        }

        .profile-img-loaded {
            position: relative;
            z-index: 1;
        }

        /* Loading state untuk profile image */
        .profile-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header/Navbar -->
    <header x-data="{ mobileMenuOpen: false }">
        <nav class="virus-primary text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="#" class="flex items-center">
                            <img src="{{ asset('images/Logoputihkecil.png') }}" alt="DengueCare Logo" class="h-10 mr-2">
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <div class="flex space-x-6">
                            <a href="#" class="py-2 px-1 font-medium hover:text-blue-200 transition active-nav">
                                <i class="fas fa-home mr-1"></i> Beranda
                            </a>
                            <a href="#" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-file-alt mr-1"></i> Manajemen Pelaporan
                            </a>
                            <a href="#" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-calendar-alt mr-1"></i> Manajemen Event
                            </a>
                            <a href="#" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-chart-line mr-1"></i> Kasus DBD
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
                                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white relative">
                                    <div class="profile-fallback">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                </div>
                                
                                <span class="font-medium">{{ $admin->nama_lengkap }}</span>
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
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 relative">
                                            <div class="profile-fallback">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        
                                        <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $admin->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">Administrator</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <a 
                                    href="#" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-user-circle mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Profil Akun</span>
                                </a>
                                <a 
                                    href="#" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                >
                                    <i class="fas fa-cog mr-3 text-blue-500 w-5 text-center"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                                <form method="POST" action="#">
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

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-show="mobileMenuOpen" x-transition class="md:hidden mt-4 pb-4 border-t border-blue-500">
                    <div class="flex flex-col space-y-2 mt-4">
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition bg-blue-700">
                            <i class="fas fa-home mr-2"></i> Beranda
                        </a>
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-file-alt mr-2"></i> Manajemen Pelaporan
                        </a>
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Manajemen Event
                        </a>
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-chart-line mr-2"></i> Kasus DBD
                        </a>
                        <hr class="border-blue-500 my-2">
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-user-circle mr-2"></i> Profil Akun
                        </a>
                        <a href="#" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-cog mr-2"></i> Pengaturan Akun
                        </a>
                        <form method="POST" action="#">
                            @csrf
                            <button type="submit" class="w-full text-left py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </button>
                        </form>
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
                    <p class="text-blue-200">Email: admin@denguecare.id</p>
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
                <p>&copy; 2023 DengueCare Admin. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</body>
</html>