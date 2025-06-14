@extends('layouts.admin')

@section('title', 'Manajemen Edukasi')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Manajemen Edukasi</h2>
        <a href="{{ route('admin.edukasi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Baru
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.edukasi') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $search }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari judul edukasi...">
                </div>
                
                <!-- Tipe Filter -->
                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select name="tipe" id="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        <option value="Video" {{ $tipe === 'Video' ? 'selected' : '' }}>Video</option>
                        <option value="Artikel" {{ $tipe === 'Artikel' ? 'selected' : '' }}>Artikel</option>
                    </select>
                </div>
                
                <!-- Kategori Pengguna Filter -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Untuk</label>
                    <select name="kategori" id="kategori" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        <option value="Warga" {{ $kategori === 'Warga' ? 'selected' : '' }}>Warga</option>
                        <option value="Kader" {{ $kategori === 'Kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.edukasi') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Edukasi Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($edukasi->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Untuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($edukasi as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($item->thumbnail_url)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-md object-cover" src="{{ $item->thumbnail_url }}" alt="">
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->judul }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->isi, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item->tipe === 'Video' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $item->tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->kategori_pengguna }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->kategori ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.edukasi.edit', $item->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.edukasi.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus edukasi ini?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $edukasi->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fas fa-book-open text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600">Tidak ada edukasi ditemukan</h3>
                <p class="text-gray-500 mt-2">Coba ubah filter pencarian Anda atau tambahkan edukasi baru</p>
                <a href="{{ route('admin.edukasi.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Tambah Edukasi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection