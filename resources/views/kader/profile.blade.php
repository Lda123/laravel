@extends('layouts.kader')

@section('title', 'Profil Kader - DengueCare')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Profile Header -->
    <div class="text-center mb-10 animate-fade-in">
        <h1 class="text-4xl font-bold text-gray-800 mb-3">Profil Kader Jumantik</h1>
        <p class="text-gray-600 mb-4">Kelola informasi profil dan data daerah bertugas Anda</p>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-green-500 rounded-full mx-auto"></div>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100">
        <div class="md:flex">
            <!-- Bagian Kiri - Foto Profil dengan Background Biru -->
            <div class="md:w-1/3 bg-gradient-to-b from-blue-600 to-blue-700 p-8 flex flex-col items-center justify-center">
                <!-- Container Foto Profil -->
                <div class="relative mb-6">
                    <!-- Lingkaran Foto Profil -->
                    <div class="w-44 h-44 rounded-full overflow-hidden border-4 border-white/30 shadow-2xl mx-auto relative">
                        <!-- Fallback Background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent flex items-center justify-center" id="fallbackContainer">
                            <!-- Initials Fallback -->
                            <span class="text-4xl font-bold text-white" id="initialsText">
                                {{ substr($kader->nama_lengkap ?? 'K', 0, 1) }}
                            </span>
                        </div>
                        
                                            <!-- Foto Profil Aktual -->
                    @php
                        $profilePicturePath = null;
                        
                        // Cek apakah ada foto profil di database
                        if ($kader->profil_pict) {
                            // Jika path sudah lengkap dengan profile_pictures/, gunakan langsung
                            if (str_contains($kader->profil_pict, 'profile_pictures/')) {
                                $profilePicturePath = asset('storage/' . $kader->profil_pict);
                            } else {
                                // Jika belum, tambahkan prefix profile_pictures/
                                $profilePicturePath = asset('storage/profile_pictures/' . $kader->profil_pict);
                            }
                        } elseif ($kader->foto_profil) {
                            // Fallback ke foto_profil jika ada
                            if (str_contains($kader->foto_profil, 'profile_pictures/')) {
                                $profilePicturePath = asset('storage/' . $kader->foto_profil);
                            } else {
                                $profilePicturePath = asset('storage/profile_pictures/' . $kader->foto_profil);
                            }
                        }
                    @endphp
                    
                    @if($profilePicturePath)
                        <img src="{{ $profilePicturePath }}" 
                            alt="Foto Profil {{ $kader->nama_lengkap }}"
                            class="w-full h-full object-cover"
                            id="profileImage"
                            onload="document.getElementById('fallbackContainer').style.display='none'"
                            onerror="handleImageError(this)">
                    @else
                        <!-- Jika tidak ada foto profil, tampilkan default atau inisial -->
                        <div class="w-full h-full bg-gradient-to-br from-blue-500/80 to-blue-600/80 flex items-center justify-center">
                            <span class="text-4xl font-bold text-white">
                                {{ substr($kader->nama_lengkap ?? 'K', 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                </div>

                <!-- Informasi Tambahan di Bagian Kiri -->
                <div class="text-center mt-6">
                    <h3 class="text-xl font-bold text-white mb-2">{{ $kader->nama_lengkap }}</h3>
                    <p class="text-blue-100 mb-4">Kader Jumantik</p>
                    <div class="flex justify-center space-x-4">
                        <div class="text-center">
                            <div class="text-white font-bold text-2xl">{{ $savedCount }}</div>
                            <div class="text-blue-100 text-sm">Video</div>
                        </div>
                        <div class="text-center">
                            <div class="text-white font-bold text-2xl">{{ $eventCount ?? 0 }}</div>
                            <div class="text-blue-100 text-sm">Event</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan - Detail Profil -->
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
                            <p class="font-bold text-gray-800 text-lg">{{ $kader->nama_lengkap }}</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor Telepon</p>
                            <p class="font-bold text-gray-800 text-lg">
                                <i class="fas fa-phone text-green-500 mr-2"></i>{{ $kader->telepon }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Location Information Card -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Lokasi Bertugas</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="bg-orange-100 text-orange-800 px-3 py-2 rounded-lg text-sm font-bold">
                                RT {{ $kader->rt->nomor_rt ?? '00' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">RT</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-purple-100 text-purple-800 px-3 py-2 rounded-lg text-sm font-bold">
                                {{ $kader->rt->rw->nomor_rw ?? '00' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">RW</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-bold">
                                {{ $kader->rt->kelurahan->nama_kelurahan ?? 'N/A' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Kelurahan</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-indigo-100 text-indigo-800 px-3 py-2 rounded-lg text-sm font-bold">
                                {{ $kader->rt->kelurahan->kecamatan->nama_kecamatan ?? 'N/A' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Kecamatan</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 border-l-4 border-purple-500 pl-4">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Bergabung Sejak</p>
                        <p class="font-bold text-gray-800 text-lg">
                            <i class="fas fa-calendar text-purple-500 mr-2"></i>
                            {{ $kader->dibuat_pada->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('kader.settings') }}" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-edit mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Edit Profil</span>
                    </a>
                    
                    <a href="{{ route('kader.video-saya') }}?saved=1" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-video mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Video Saya</span>
                        <span class="ml-2 bg-white/30 px-2 py-1 rounded-full text-sm">{{ $savedCount }}</span>
                    </a>
                    
                    <a href="{{ route('kader.dashboard') }}" 
                    class="group flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-home mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
        <!-- Help Card -->
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-2xl border border-yellow-200 shadow-md">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-question-circle text-white"></i>
                </div>
                <h4 class="font-bold text-gray-800">Bantuan</h4>
            </div>
            <p class="text-gray-600 text-sm">
                Jika Anda memiliki pertanyaan atau butuh bantuan, silakan hubungi admin atau koordinator daerah Anda.
            </p>
        </div>

        <!-- Contact Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200 shadow-md">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-phone text-white"></i>
                </div>
                <h4 class="font-bold text-gray-800">Kontak Darurat</h4>
            </div>
            <p class="text-gray-600 text-sm">
                Dalam situasi darurat terkait DBD, segera hubungi Puskesmas setempat atau layanan kesehatan terdekat.
            </p>
        </div>
    </div>
</div>

<script>
    function handleImageError(img) {
    // Sembunyikan gambar yang error
    img.style.display = 'none';
    
    // Tampilkan fallback container
    const fallbackContainer = document.getElementById('fallbackContainer');
    if (fallbackContainer) {
        fallbackContainer.style.display = 'flex';
    }
    
    // Update badge menjadi warning
    const badge = img.parentElement.parentElement.querySelector('.absolute.-bottom-2.-right-2');
    if (badge) {
        badge.className = 'absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center border-3 border-white shadow-lg';
        badge.innerHTML = '<i class="fas fa-exclamation text-white text-sm"></i>';
    }
    
    console.log('Error loading profile image, fallback to initials');
}

// Fungsi untuk preview foto profil baru (opsional, untuk form edit)
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const profileImg = document.getElementById('profileImage');
            const fallbackContainer = document.getElementById('fallbackContainer');
            
            if (profileImg) {
                profileImg.src = e.target.result;
                profileImg.style.display = 'block';
                if (fallbackContainer) {
                    fallbackContainer.style.display = 'none';
                }
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
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

/* Custom scrollbar untuk mobile */
@media (max-width: 768px) {
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
}
</style>

@endsection