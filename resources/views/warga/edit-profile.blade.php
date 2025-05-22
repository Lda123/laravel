@extends('layouts.warga')

@section('title', 'Edit Profil - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 animate-fade-in-up">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('warga.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Picture -->
<div class="flex flex-col items-center">
    <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-blue-100 mb-4 profile-pic-container cursor-pointer">
        @php
            $defaultProfile = asset('images/default-profile.jpg');
            $profilePicUrl = $defaultProfile;
            
            if ($warga->profile_pict) {
                if (Storage::disk('public')->exists($warga->profile_pict)) {
                    $profilePicUrl = Storage::disk('public')->url($warga->profile_pict);
                } elseif (file_exists(public_path($warga->profile_pict))) {
                    $profilePicUrl = asset($warga->profile_pict);
                }
            }
        @endphp
        
        <img src="{{ $profilePicUrl }}" 
             alt="Foto Profil" 
             id="profilePicPreview"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center edit-icon opacity-0 hover:opacity-100 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <input type="file" id="profile_pict" name="profile_pict" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
    </div>
    <p class="text-sm text-gray-500">Klik gambar untuk mengubah foto profil</p>
</div>

            <!-- Name -->
            <div>
                <label for="nama_lengkap" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" 
                       value="{{ old('nama_lengkap', $warga->nama_lengkap) }}" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Phone -->
            <div>
                <label for="telepon" class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                <input type="tel" id="telepon" name="telepon" 
                       value="{{ old('telepon', $warga->telepon) }}" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Address -->
            <div>
                <label for="alamat_lengkap" class="block text-gray-700 font-medium mb-2">Alamat Lengkap</label>
                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" 
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('alamat_lengkap', $warga->alamat_lengkap) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition transform hover:-translate-y-0.5 shadow hover:shadow-md">
                    Simpan Perubahan
                </button>
                <a href="{{ route('warga.profile') }}" class="ml-4 px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Profile picture preview
    function previewProfilePic(event) {
        const preview = document.getElementById('profilePicPreview');
        const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('ring', 'ring-blue-300', 'animate-pulse');
                    setTimeout(() => {
                        preview.classList.remove('animate-pulse');
                    }, 600);
                }
                reader.readAsDataURL(file);
            }
}

    // Initialize file input change event
    document.getElementById('profile_pict').addEventListener('change', function(event) {
    previewProfilePic(event);
    document.getElementById('previewNote').classList.remove('hidden');
});

</script>
@endpush
@endsection