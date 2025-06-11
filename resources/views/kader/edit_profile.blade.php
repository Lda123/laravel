@extends('layouts.kader')

@section('title', 'Edit Profil - DengueCare')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Edit Profil Kader</h1>
        <p class="text-gray-600">Perbarui informasi profil Anda</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fade-in-up">
        <form action="{{ route('kader.update-profile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <!-- Profile Picture Upload -->
                <div class="flex flex-col items-center mb-10">
                    <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-blue-200 shadow-lg mb-4">
                        <img src="{{ $kader->profil_pict ? asset('storage/' . $kader->profil_pict) : asset('images/default-profile.jpg') }}" 
                             alt="Current Profile Picture"
                             class="w-full h-full object-cover"
                             id="profilePreview">
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-sm font-medium">Ubah Foto</span>
                        </div>
                    </div>
                    <label class="cursor-pointer">
                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-camera mr-2"></i>Pilih Foto
                        </span>
                        <input type="file" name="profil_pict" id="profileInput" class="hidden" accept="image/*">
                    </label>
                    @error('profil_pict')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Personal Information -->
                <div class="space-y-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" 
                               value="{{ old('nama_lengkap', $kader->nama_lengkap) }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('nama_lengkap')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="telepon" id="telepon" 
                               value="{{ old('telepon', $kader->telepon) }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('telepon')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Password with Toggle Visibility -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini (untuk verifikasi)</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" 
                                   value="{{ old('current_password') }}"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600 hover:text-gray-800 focus:outline-none" 
                                    onclick="togglePasswordVisibility('current_password', 'current_password_icon')">
                                <i id="current_password_icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="new_password" id="new_password" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-10">
                    <a href="{{ route('kader.profile') }}" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileInput = document.getElementById('profileInput');
    const profilePreview = document.getElementById('profilePreview');
    
    profileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(event) {
                profilePreview.src = event.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
});

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection