@extends('layouts.warga')

@section('title', 'Edit Profil - DengueCare')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Edit Profil Warga</h1>
        <p class="text-gray-600">Perbarui informasi profil Anda</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fade-in-up">
        <form action="{{ route('warga.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <!-- Profile Picture Upload -->
                <div class="flex flex-col items-center mb-10">
                    <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-blue-200 shadow-lg mb-4">
                        <img src="{{ $warga->profile_pict ? Storage::url($warga->profile_pict) : asset('images/default-profile.jpg') }}" 
                             alt="Foto profil {{ $warga->nama_lengkap }}"
                             class="w-full h-full object-cover"
                             id="profilePreview">
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-sm font-medium">Ubah Foto</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center space-y-2">
                        <label class="cursor-pointer">
                            <span class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-camera mr-2"></i>Pilih Foto
                            </span>
                            <input type="file" name="profile_pict" id="profileInput" class="hidden" accept="image/*">
                        </label>
                        @if($warga->profile_pict)
                        <button type="button" onclick="confirmDeletePhoto()" class="text-red-500 text-sm hover:text-red-700">
                            <i class="fas fa-trash mr-1"></i>Hapus Foto
                        </button>
                        @endif
                    </div>
                    @error('profile_pict')
                        <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Personal Information -->
                <div class="space-y-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" 
                               value="{{ old('nama_lengkap', $warga->nama_lengkap) }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('nama_lengkap')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="telepon" id="telepon" 
                               value="{{ old('telepon', $warga->telepon) }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('telepon')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3"
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('alamat_lengkap', $warga->alamat_lengkap) }}</textarea>
                        @error('alamat_lengkap')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini (untuk verifikasi)</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-12">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" 
                                    onclick="togglePasswordVisibility('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password" 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" 
                                    onclick="togglePasswordVisibility('new_password')">
                                <i class="fas fa-eye" id="new_password_icon"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" 
                                    onclick="togglePasswordVisibility('new_password_confirmation')">
                                <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                            </button>
                        </div>
                        @error('new_password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-10">
                    <a href="{{ route('warga.profile') }}" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
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

<!-- Delete Photo Confirmation Modal -->
<div id="deletePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Hapus Foto Profil?</h3>
        <p class="text-gray-600 mb-6">Anda yakin ingin menghapus foto profil? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeletePhotoModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Batal
            </button>
            <form action="{{ route('warga.profile.delete-photo') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Hapus
                </button>
            </form>
        </div>
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

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (!input || !icon) return;
    
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

function confirmDeletePhoto() {
    document.getElementById('deletePhotoModal').classList.remove('hidden');
}

function closeDeletePhotoModal() {
    document.getElementById('deletePhotoModal').classList.add('hidden');
}
</script>
@endsection