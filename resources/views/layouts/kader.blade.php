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
            background-color: #2563eb;
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

        /* Profile Picture Styles - Updated */
        .profile-picture-container {
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid white;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #bfdbfe;
        }

        .profile-picture-container:hover {
            border-color: #93c5fd;
            transform: scale(1.05);
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .profile-fallback {
            width: 100%;
            height: 100%;
            background-color: #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1d4ed8;
            font-size: 0.875rem;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* Loading animation */
        .profile-loading {
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .profile-loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header/Navbar -->
    <header x-data="{ mobileMenuOpen: false, showLogoutModal: false }">
        <!-- Modal Logout Confirmation -->
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
                    <form method="POST" action="{{ route('kader.logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 font-medium flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <nav class="virus-primary text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="{{ route('kader.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('images/Logoputihkecil.png') }}" alt="DengueCare Logo" class="h-10 mr-2">
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
                            <a href="{{ route('kader.buku-panduan') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-book mr-1"></i> Panduan
                            </a>
                            <a href="{{ route('kader.video-pelatihan') }}" class="py-2 px-1 font-medium hover:text-blue-200 transition">
                                <i class="fas fa-graduation-cap mr-1"></i> Pelatihan
                            </a>
                        </div>

                        <!-- Profile Dropdown - Simplified -->
                        @php
                            $kader = auth()->guard('kader')->user();
                        @endphp

                        <div class="relative" x-data="{ open: false, stayOpen: false }">
                            <button 
                                @click="open = !open; stayOpen = !stayOpen" 
                                @mouseenter="if(!stayOpen) open = true" 
                                @mouseleave="if(!stayOpen) open = false"
                                class="flex items-center space-x-2 focus:outline-none transition-colors duration-200 rounded-full p-1 hover:bg-blue-700 hover:bg-opacity-30"
                            >
                                <!-- Profile Picture - Small -->
                                <div class="profile-picture-container w-8 h-8">
                                    @if($kader && $kader->profile_picture_url)
                                        <img 
                                            src="{{ $kader->profile_picture_url }}" 
                                            alt="Profile Picture"
                                            class="profile-img"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="profile-fallback" style="display: none;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @else
                                        <div class="profile-fallback">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <span class="font-medium">{{ $kader->nama_lengkap ?? 'Kader' }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
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
                                        <!-- Profile Picture - Large in dropdown -->
                                        <div class="profile-picture-container w-10 h-10 border-gray-200">
                                            @if($kader && $kader->profile_picture_url)
                                                <img 
                                                    src="{{ $kader->profile_picture_url }}" 
                                                    alt="Profile Picture"
                                                    class="profile-img"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                >
                                                <div class="profile-fallback" style="display: none;">
                                                    <i class="fas fa-user text-base"></i>
                                                </div>
                                            @else
                                                <div class="profile-fallback">
                                                    <i class="fas fa-user text-base"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $kader->nama_lengkap ?? 'Kader' }}</p>
                                            <p class="text-xs text-gray-500">Kader Jumantik</p>
                                        </div>
                                    </div>
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
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-show="mobileMenuOpen" x-transition class="md:hidden mt-4 pb-4 border-t border-blue-500">
                    <div class="flex flex-col space-y-2 mt-4">
                        <!-- Profile section for mobile -->
                        <div class="flex items-center space-x-3 py-2 px-4 bg-blue-700 rounded mb-2">
                            <div class="profile-picture-container w-8 h-8">
                                @if($kader && $kader->profile_picture_url)
                                    <img 
                                        src="{{ $kader->profile_picture_url }}" 
                                        alt="Profile Picture"
                                        class="profile-img"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="profile-fallback" style="display: none;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @else
                                    <div class="profile-fallback">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <span class="font-medium">{{ $kader->nama_lengkap ?? 'Kader' }}</span>
                        </div>

                        <a href="{{ route('kader.dashboard') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition {{ request()->routeIs('kader.dashboard') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-home mr-2"></i> Beranda
                        </a>
                        <a href="{{ route('kader.forum') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-comments mr-2"></i> Forum
                        </a>
                        <a href="{{ route('kader.buku-panduan') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-book mr-2"></i> Panduan
                        </a>
                        <a href="{{ route('kader.video-pelatihan') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-graduation-cap mr-2"></i> Pelatihan
                        </a>
                        <hr class="border-blue-500 my-2">
                        <a href="{{ route('kader.profile') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-user-circle mr-2"></i> Profil Saya
                        </a>
                        <a href="{{ route('kader.settings') }}" class="py-2 px-4 font-medium hover:bg-blue-700 rounded transition">
                            <i class="fas fa-cog mr-2"></i> Edit Profile
                        </a>
                        <button 
                            @click="showLogoutModal = true; mobileMenuOpen = false" 
                            class="w-full text-left py-2 px-4 font-medium hover:bg-blue-700 rounded transition"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                        </button>
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

        // Profile Image Preview Function
        function previewProfileImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update semua profile image di halaman
                    const profileImages = document.querySelectorAll('.profile-img');
                    const fallbackContainers = document.querySelectorAll('.profile-fallback');
                    
                    profileImages.forEach(img => {
                        img.src = e.target.result;
                        img.style.display = 'block';
                        img.onerror = function() {
                            this.style.display = 'none';
                            const fallback = this.nextElementSibling;
                            if (fallback && fallback.classList.contains('profile-fallback')) {
                                fallback.style.display = 'flex';
                            }
                        };
                    });
                    
                    fallbackContainers.forEach(container => {
                        container.style.display = 'none';
                    });

                    // Jika ada preview khusus (untuk form edit profile)
                    const profilePreview = document.getElementById('profileImage');
                    if (profilePreview) {
                        profilePreview.src = e.target.result;
                        profilePreview.style.display = 'block';
                        
                        const fallbackPreview = document.getElementById('fallbackContainer');
                        if (fallbackPreview) {
                            fallbackPreview.style.display = 'none';
                        }
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to refresh profile images after upload
        function refreshProfileImages(newImageUrl) {
            const profileImages = document.querySelectorAll('.profile-img');
            const fallbackContainers = document.querySelectorAll('.profile-fallback');
            
            profileImages.forEach(img => {
                img.src = newImageUrl + '?t=' + new Date().getTime(); // Add timestamp to prevent caching
                img.style.display = 'block';
            });
            
            fallbackContainers.forEach(container => {
                container.style.display = 'none';
            });
        }
    </script>
    @stack('scripts')
</body>
</html>