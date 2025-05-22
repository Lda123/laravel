@extends('layouts.warga')

@section('title', 'Profil - DengueCare')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-6 animate-fade-in-up">
    <div class="mb-8">
        @if ($user)
            <div class="flex items-center gap-4 mb-6">
                <div class="relative w-24 h-24 rounded-full overflow-hidden group cursor-pointer transition-all duration-300 hover:shadow-lg">
                    @php
                           $profilePicUrl = asset('images/default-profile.jpg'); // default
                        
                        if ($user->profile_pict) {
                            // Gunakan logika yang sama seperti di edit.blade.php
                            if (file_exists(public_path('uploads')) && is_link(public_path('storage'))) {
                                // Jika storage link ada dan merupakan symlink
                                $profilePicUrl = Storage::url($user->profile_pict);
                            } else {
                                // Jika tidak ada symlink, gunakan asset dengan path langsung
                                $profilePicUrl = asset($user->profile_pict);
                            }
                        }
                    @endphp
                    
                    <img src="{{ $profilePicUrl }}" 
                         alt="Foto Profil" 
                         class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-50">
                    <a href="{{ route('warga.profile.edit') }}" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" 
                            class="w-8 h-8 text-white hover:text-blue-300 transition-colors duration-200 cursor-pointer">
                            <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                            <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                        </svg>
                    </a>
                </div>
                <div class="flex flex-col justify-center">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $user->nama_lengkap }}</h2>
                    <p class="text-gray-600">NIK: {{ $user->nik }}</p>
                </div>
            </div>
            <p class="text-gray-700 mb-2">Nomor Telepon: {{ $user->telepon }}</p>
            <p class="text-gray-700">Alamat: {!! nl2br(e($user->alamat_lengkap)) !!}</p>
        @else
            <p class="text-red-500">Data pengguna tidak ditemukan.</p>
        @endif
    </div>

    <!-- Home Condition Status -->
    <div class="my-6 rounded-lg overflow-hidden shadow-md animate-fade-in-up">
        <div class="p-6 @if($home_condition && $home_condition->kategori_masalah == 'Aman') bg-green-50 border-l-4 border-green-500
                        @elseif($home_condition && $home_condition->kategori_masalah == 'Tidak Aman') bg-red-50 border-l-4 border-red-500
                        @else bg-gray-50 border-l-4 border-gray-500 @endif">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if($home_condition && $home_condition->kategori_masalah == 'Aman')
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    @elseif($home_condition && $home_condition->kategori_masalah == 'Tidak Aman')
                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    @else
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold @if($home_condition && $home_condition->kategori_masalah == 'Aman') text-green-800
                                              @elseif($home_condition && $home_condition->kategori_masalah == 'Tidak Aman') text-red-800
                                              @else text-gray-800 @endif">
                        KONDISI RUMAH: {{ $home_condition ? ($status_display[$home_condition->kategori_masalah] ?? $home_condition->kategori_masalah) : 'Belum Dicek' }}
                    </h3>
                    <p class="text-sm @if($home_condition && $home_condition->kategori_masalah == 'Aman') text-green-700
                                   @elseif($home_condition && $home_condition->kategori_masalah == 'Tidak Aman') text-red-700
                                   @else text-gray-700 @endif mt-1">
                        @if(!$home_condition || $home_condition->kategori_masalah == 'Belum Dicek')
                            Rumah Anda belum diperiksa oleh petugas
                        @else
                            Terakhir diperiksa: {{ $home_condition->tanggal_pantau->format('d M Y') }}
                        @endif
                    </p>
                    
                    <!-- Additional status information -->
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Status Kesehatan:</p>
                            <p class="text-sm font-semibold @if($home_condition && $home_condition->status_kesehatan == 'Sehat') text-green-600
                                                         @elseif($home_condition && $home_condition->status_kesehatan == 'Gejala Ringan') text-yellow-600
                                                         @else text-red-600 @endif">
                                {{ $home_condition ? $home_condition->status_kesehatan : '-' }}
                            </p>
                        </div>
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Status Lingkungan:</p>
                            <p class="text-sm font-semibold @if($home_condition && $home_condition->status_lingkungan == 'Bersih') text-green-600
                                                         @elseif($home_condition && $home_condition->status_lingkungan == 'Kurang Bersih') text-yellow-600
                                                         @else text-red-600 @endif">
                                {{ $home_condition ? $home_condition->status_lingkungan : '-' }}
                            </p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-3">{{ $home_condition ? ($home_condition->deskripsi ?? 'Tidak ada deskripsi tambahan') : 'Belum ada data pemeriksaan rumah' }}</p>
                </div>
            </div>
            
            @if(!$home_condition || $home_condition->kategori_masalah == 'Belum Dicek')
                <div class="mt-4">
                    <a href="{{ route('warga.pelaporan') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Minta Pemeriksaan Rumah
                    </a>
                </div>
            @else
                <div class="mt-4">
                    <a href="{{ route('warga.riwayat') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        Lihat Riwayat Pemeriksaan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-8">
        <!-- Edit Profile Button -->
        <a href="{{ route('warga.profile.edit') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer transition-all duration-300 hover:bg-gray-50 hover:shadow-md hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 mr-3 text-blue-600">
                <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
            </svg>
            <span class="font-bold text-gray-800">Edit Profil</span>
        </a>

        <!-- Logout Button -->
        <a href="{{ route('warga.logout') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer transition-all duration-300 hover:bg-gray-50 hover:shadow-md hover:-translate-y-1" onclick="return confirm('Yakin ingin keluar?');">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 mr-3 text-red-600">
                <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold text-gray-800">Keluar</span>
        </a>
    </div>
</div>

@push('scripts')
<script>
    // Add additional animations on scroll
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('.animate-fade-in-up');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        elements.forEach(element => {
            observer.observe(element);
        });
    });
</script>
@endpush
@endsection