@extends('layouts.warga')

@section('title', 'Profil - DengueCare')

@section('content')
<div x-data="{ showLogoutModal: false }" class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
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

    <!-- Profile Header -->
    <div class="text-center mb-10 animate-fade-in">
        <h1 class="text-4xl font-bold text-gray-800 mb-3">Profil Warga</h1>
        <p class="text-gray-600 mb-4">Kelola informasi profil dan data rumah Anda</p>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-green-500 rounded-full mx-auto"></div>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100">
        <div class="md:flex">
            <!-- Left Section - Profile Picture with Blue Background -->
            <div class="md:w-1/3 bg-gradient-to-b from-blue-600 to-blue-700 p-8 flex flex-col items-center justify-center">
                <!-- Profile Picture Container -->
                <div class="relative mb-6">
                    <div class="w-44 h-44 rounded-full overflow-hidden border-4 border-white/30 shadow-2xl mx-auto relative">
                        @if($user->profile_pict)
                            <img src="{{ $profile_picture_url }}" 
                                alt="Foto Profil {{ $user->nama_lengkap }}"
                                class="w-full h-full object-cover"
                                id="profileImage"
                                onerror="this.style.display='none'; document.getElementById('fallbackContainer').style.display='flex'">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent flex items-center justify-center hidden" id="fallbackContainer">
                                <span class="text-4xl font-bold text-white">
                                    {{ substr($user->nama_lengkap ?? 'W', 0, 1) }}
                                </span>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500/80 to-blue-600/80 flex items-center justify-center">
                                <span class="text-4xl font-bold text-white">
                                    {{ substr($user->nama_lengkap ?? 'W', 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Info in Left Section -->
                <div class="text-center mt-6">
                    <h3 class="text-xl font-bold text-white mb-2" id="profileName">{{ $user->nama_lengkap }}</h3>
                    <p class="text-blue-100 mb-4">Warga Umum</p>
                    <div class="flex justify-center space-x-4">
                        <div class="text-center">
                            <div class="text-white font-bold text-2xl">{{ $savedEdukasiCount }}</div>
                            <div class="text-blue-100 text-sm">Informasi Disimpan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-white font-bold text-2xl">{{ $savedEventCount }}</div>
                            <div class="text-blue-100 text-sm">Event Disimpan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section - Profile Details -->
            <div class="md:w-2/3 p-10 bg-gray-50">
                <!-- Personal Information Card -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-id-card text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Informasi Pribadi</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap</p>
                            <p class="font-bold text-gray-800 text-lg" id="fullName">{{ $user->nama_lengkap }}</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor Telepon</p>
                            <p class="font-bold text-gray-800 text-lg" id="phoneNumber">
                                <i class="fas fa-phone text-green-500 mr-2"></i>{{ $user->telepon }}
                            </p>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">NIK</p>
                            <p class="font-bold text-gray-800 text-lg" id="nikNumber">
                                <i class="fas fa-id-card text-purple-500 mr-2"></i>{{ $user->nik }}
                            </p>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Alamat</p>
                            <p class="font-bold text-gray-800 text-lg" id="addressText">
                                <i class="fas fa-home text-orange-500 mr-2"></i>{{ $user->alamat_lengkap }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Home Condition Card -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-500 to-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-home text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Kondisi Rumah</h3>
                            @if($home_condition)
                            <p class="text-sm text-gray-500">
                                Terakhir diperiksa: {{ \Carbon\Carbon::parse($home_condition->tanggal)->format('d M Y') }}
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="inline-block mb-4">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-100 text-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-800">
                            {{ $home_condition ? ($status_display[$home_condition->kategori_masalah] ?? $home_condition->kategori_masalah) : 'Belum Dicek' }}
                        </span>
                    </div>
                    
                    <!-- Pesan Status -->
                    <div class="bg-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-50 p-4 rounded-lg border-l-4 border-{{ $home_condition && $home_condition->kategori_masalah == 'Aman' ? 'green' : ($home_condition && $home_condition->kategori_masalah == 'Tidak Aman' ? 'red' : 'gray') }}-500">
                        @if($home_condition)
                            @if($home_condition->kategori_masalah == 'Aman')
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                    <div>
                                        <p class="font-semibold text-green-800">Rumah Anda Aman dari DBD</p>
                                        <p class="text-sm text-green-700 mt-1">Tetap jaga kebersihan rumah dan lakukan PSN (Pemberantasan Sarang Nyamuk) secara rutin.</p>
                                    </div>
                                </div>
                            @elseif($home_condition->kategori_masalah == 'Tidak Aman')
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-2"></i>
                                    <div>
                                        <p class="font-semibold text-red-800">Perhatian: Potensi DBD Terdeteksi</p>
                                        <p class="text-sm text-red-700 mt-1">
                                            Ditemukan indikasi jentik nyamuk. Segera lakukan:
                                            <ul class="list-disc list-inside mt-1 ml-2">
                                                <li>Pembersihan tempat penampungan air</li>
                                                <li>Penutupan wadah yang bisa menampung air</li>
                                            </ul>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-700">
                                Belum ada data pemeriksaan rumah. Silakan minta pemeriksaan ke petugas jumantik.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('warga.profile.edit') }}" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-edit mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Edit Profil</span>
                    </a>
                    
                    <a href="{{ route('warga.informasi-saya') }}" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-video mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>informasi Saya</span>
                        <span class="ml-2 bg-white/30 px-2 py-1 rounded-full text-sm">{{ $savedEdukasiCount }}</span>
                    </a>
                    
                    <a href="{{ route('warga.dashboard') }}" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-home mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variable untuk tracking user ID
    const userId = document.getElementById('app')?.dataset.userId || null;
    
    function handleImageError(img) {
        // Hide the error image
        img.style.display = 'none';
        
        // Show fallback container
        const fallbackContainer = document.getElementById('fallbackContainer');
        if (fallbackContainer) {
            fallbackContainer.style.display = 'flex';
        }
        
        console.log('Error loading profile image, fallback to initials');
    }

    // Function to upload profile picture via AJAX
    function uploadProfilePicture(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('profile_picture', input.files[0]);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Show loading state
            const uploadButton = input.parentElement;
            const originalContent = uploadButton.innerHTML;
            uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengunggah...';
            uploadButton.classList.add('opacity-75');

            axios.post('{{ route("warga.profile.upload-picture") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                if (response.data.success) {
                    updateProfilePicture(response.data.image_url);
                    showToast('success', 'Foto profil berhasil diperbarui');
                } else {
                    showToast('error', response.data.message || 'Gagal mengunggah foto');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showToast('error', error.response?.data?.message || 'Terjadi kesalahan saat mengunggah');
            })
            .finally(() => {
                uploadButton.innerHTML = originalContent;
                uploadButton.classList.remove('opacity-75');
                input.value = '';
            });
        }
    }

    // Function to update profile picture in UI
    function updateProfilePicture(imageUrl) {
        const profileImage = document.getElementById('profileImage');
        const fallbackContainer = document.getElementById('fallbackContainer');
        
        if (profileImage) {
            profileImage.src = imageUrl + '?' + new Date().getTime();
            profileImage.style.display = 'block';
            if (fallbackContainer) {
                fallbackContainer.style.display = 'none';
            }
        } else {
            const img = document.createElement('img');
            img.src = imageUrl + '?' + new Date().getTime();
            img.alt = 'Foto Profil';
            img.className = 'w-full h-full object-cover';
            img.id = 'profileImage';
            img.onload = function() {
                if (fallbackContainer) fallbackContainer.style.display = 'none';
            };
            img.onerror = function() {
                this.style.display = 'none';
                if (fallbackContainer) fallbackContainer.style.display = 'flex';
            };
            
            const profileContainer = document.querySelector('.w-44.h-44');
            const existingDefault = profileContainer.querySelector('.bg-gradient-to-br');
            if (existingDefault) existingDefault.remove();
            
            profileContainer.insertBefore(img, profileContainer.firstChild);
        }
    }

    // Function to show toast notifications
    function showToast(type, message) {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('translate-x-0');
        }, 10);
        
        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Function to refresh profile data
    function refreshProfileData() {
        axios.get('{{ route("warga.profile.data") }}')
            .then(response => {
                if (response.data.success) {
                    const profile = response.data.profile;
                    
                    // Update profile name
                    if (profile.nama_lengkap) {
                        const profileName = document.getElementById('profileName');
                        const fullName = document.getElementById('fullName');
                        const initialsText = document.getElementById('initialsText');
                        
                        if (profileName) profileName.textContent = profile.nama_lengkap;
                        if (fullName) fullName.textContent = profile.nama_lengkap;
                        if (initialsText) initialsText.textContent = profile.nama_lengkap.charAt(0);
                    }
                    
                    // Update phone number
                    if (profile.telepon) {
                        const phoneNumber = document.getElementById('phoneNumber');
                        if (phoneNumber) {
                            phoneNumber.innerHTML = `<i class="fas fa-phone text-green-500 mr-2"></i>${profile.telepon}`;
                        }
                    }
                    
                    // Update address
                    if (profile.alamat_lengkap) {
                        const addressText = document.getElementById('addressText');
                        if (addressText) {
                            addressText.innerHTML = `<i class="fas fa-home text-orange-500 mr-2"></i>${profile.alamat_lengkap}`;
                        }
                    }
                    
                    // Update profile picture if changed
                    if (profile.profile_picture) {
                        updateProfilePicture(profile.profile_picture);
                    }
                    
                    // Update home condition if available
                    if (profile.home_condition) {
                        updateHomeCondition(profile.home_condition);
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing profile data:', error);
            });
    }

    // Function to update home condition
    function updateHomeCondition(homeCondition) {
        const homeStatus = document.getElementById('homeStatus');
        const lastCheckDate = document.getElementById('lastCheckDate');
        const homeDescription = document.getElementById('homeDescription');
        
        if (homeStatus) homeStatus.textContent = homeCondition.status;
        if (lastCheckDate) {
            lastCheckDate.innerHTML = `<i class="fas fa-calendar text-blue-500 mr-2"></i>${homeCondition.date}`;
        }
        if (homeDescription) homeDescription.textContent = homeCondition.description;
    }

    // Listen for profile updates from other tabs/windows
    window.addEventListener('storage', function(event) {
        if (event.key === 'profileUpdated') {
            try {
                const data = JSON.parse(event.newValue);
                if (data.userId === userId) {
                    // Update profile picture if changed
                    if (data.profilePicture) {
                        updateProfilePicture(data.profilePicture);
                    }
                    
                    // Update other profile data
                    if (data.fullName) {
                        const profileName = document.getElementById('profileName');
                        const fullName = document.getElementById('fullName');
                        const initialsText = document.getElementById('initialsText');
                        
                        if (profileName) profileName.textContent = data.fullName;
                        if (fullName) fullName.textContent = data.fullName;
                        if (initialsText) initialsText.textContent = data.fullName.charAt(0);
                    }
                    
                    if (data.phone) {
                        const phoneNumber = document.getElementById('phoneNumber');
                        if (phoneNumber) {
                            phoneNumber.innerHTML = `<i class="fas fa-phone text-green-500 mr-2"></i>${data.phone}`;
                        }
                    }
                    
                    if (data.address) {
                        const addressText = document.getElementById('addressText');
                        if (addressText) {
                            addressText.innerHTML = `<i class="fas fa-home text-orange-500 mr-2"></i>${data.address}`;
                        }
                    }
                    
                    showToast('success', 'Profil diperbarui dari tab lain');
                }
            } catch (error) {
                console.error('Error parsing storage event:', error);
            }
        }
    });

    // Auto refresh profile data every 30 seconds
    setInterval(refreshProfileData, 30000);

    // Refresh on page focus
    window.addEventListener('focus', function() {
        refreshProfileData();
    });

    // Cleanup old localStorage entries
    function cleanupLocalStorage() {
        const keys = Object.keys(localStorage);
        const now = Date.now();
        const maxAge = 24 * 60 * 60 * 1000; // 24 hours
        
        keys.forEach(key => {
            if (key.startsWith('profileUpdated')) {
                try {
                    const data = JSON.parse(localStorage.getItem(key));
                    if (data.timestamp && (now - data.timestamp) > maxAge) {
                        localStorage.removeItem(key);
                    }
                } catch (error) {
                    // Remove invalid entries
                    localStorage.removeItem(key);
                }
            }
        });
    }

    // Run cleanup on page load
    document.addEventListener('DOMContentLoaded', function() {
        cleanupLocalStorage();
    });

    // Enable real-time updates with Echo/Pusher if available
    if (typeof window.Echo !== 'undefined' && typeof userId !== 'undefined') {
    window.Echo.private(`profile-updates.${userId}`)
        .listen('ProfileUpdated', (data) => {
            // Handle real-time updates
            if (data.profile_picture) {
                updateProfilePicture(data.profile_picture);
            }
            
            if (data.nama_lengkap) {
                const profileName = document.getElementById('profileName');
                const fullName = document.getElementById('fullName');
                const initialsText = document.getElementById('initialsText');
                
                if (profileName) profileName.textContent = data.nama_lengkap;
                if (fullName) fullName.textContent = data.nama_lengkap;
                if (initialsText) initialsText.textContent = data.nama_lengkap.charAt(0);
            }
            
            if (data.telepon) {
                const phoneNumber = document.getElementById('phoneNumber');
                if (phoneNumber) {
                    phoneNumber.innerHTML = `<i class="fas fa-phone text-green-500 mr-2"></i>${data.telepon}`;
                }
            }
            
            if (data.alamat_lengkap) {
                const addressText = document.getElementById('addressText');
                if (addressText) {
                    addressText.innerHTML = `<i class="fas fa-home text-orange-500 mr-2"></i>${data.alamat_lengkap}`;
                }
            }
            
            showToast('success', 'Profil diperbarui secara real-time');
        });
    }
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

/* Hover effects */
.hover\:scale-110:hover {
    transform: scale(1.1);
}
</style>

@endsection