@extends('layouts.admin')

@section('title', 'Tambah Edukasi Baru')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Tambah Edukasi Baru</h2>
        
        <form action="{{ route('admin.edukasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul*</label>
                    <input type="text" name="judul" id="judul" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Judul edukasi">
                </div>
                
                <!-- Tipe -->
                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe*</label>
                    <select name="tipe" id="tipe" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Tipe</option>
                        <option value="Video">Video</option>
                        <option value="Artikel">Artikel</option>
                    </select>
                </div>
                
                <!-- Kategori Pengguna -->
                <div>
                    <label for="kategori_pengguna" class="block text-sm font-medium text-gray-700 mb-1">Untuk*</label>
                    <select name="kategori_pengguna" id="kategori_pengguna" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Warga">Warga</option>
                        <option value="Kader">Kader</option>
                    </select>
                </div>
                
                <!-- Thumbnail -->
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Format: JPEG, PNG, JPG (Max: 2MB)</p>
                </div>
                
                <!-- Tautan (for Video) -->
                <div id="tautan-container">
                    <label for="tautan" class="block text-sm font-medium text-gray-700 mb-1">Tautan Video</label>
                    <input type="url" name="tautan" id="tautan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://example.com/video">
                    <p class="mt-1 text-sm text-gray-500">Masukkan URL video (YouTube, Vimeo, dll)</p>
                </div>
                
                <!-- Durasi -->
                <div>
                    <label for="durasi" class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                    <input type="text" name="durasi" id="durasi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: 15 menit">
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" name="kategori" id="kategori"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Kesehatan, Gizi, dll">
                </div>
                
                <!-- Isi -->
                <div class="md:col-span-2">
                    <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi*</label>
                    <textarea name="isi" id="isi" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Konten edukasi"></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.edukasi') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition mr-3">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Simpan Edukasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipe');
    const tautanContainer = document.getElementById('tautan-container');
    
    // Hide/show tautan field based on tipe selection
    tipeSelect.addEventListener('change', function() {
        if (this.value === 'Video') {
            tautanContainer.style.display = 'block';
            document.getElementById('tautan').setAttribute('required', 'required');
        } else {
            tautanContainer.style.display = 'none';
            document.getElementById('tautan').removeAttribute('required');
        }
    });
    
    // Initialize based on current value
    if (tipeSelect.value !== 'Video') {
        tautanContainer.style.display = 'none';
        document.getElementById('tautan').removeAttribute('required');
    }
});
</script>
@endsection