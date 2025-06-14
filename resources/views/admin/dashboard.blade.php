@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Greeting Section -->
    <div class="mb-8 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $greeting }}, 
            <span class="text-blue-600">{{ $admin->nama_lengkap }}</span>!
        </h1>
        <p class="text-lg text-gray-600">Selamat Bekerja!</p>
    </div>

    <!-- Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- User Management -->
            <a href="#" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="font-medium">Manajemen Pengguna</span>
            </a>

            <!-- Forum Management -->
            <a href="#" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <span class="font-medium">Manajemen Forum</span>
            </a>

            <!-- Complaints -->
            <a href="#" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <span class="font-medium">Aduan Warga</span>
            </a>

            <!-- Education Materials -->
            <a href="#" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="font-medium">Edukasi</span>
            </a>
        </div>

    <!-- Latest Education Materials -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Materi Edukasi Terbaru</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Semua</a>
        </div>

        <!-- Filter Options -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('admin.dashboard', ['type' => 'all']) }}" class="px-3 py-1 {{ request('type') == 'all' || !request('type') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm">Semua</a>
            <a href="{{ route('admin.dashboard', ['type' => 'video']) }}" class="px-3 py-1 {{ request('type') == 'video' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm">Video</a>
            <a href="{{ route('admin.dashboard', ['type' => 'article']) }}" class="px-3 py-1 {{ request('type') == 'article' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm">Artikel</a>
            <a href="{{ route('admin.dashboard', ['audience' => 'warga']) }}" class="px-3 py-1 {{ request('audience') == 'warga' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm">Untuk Warga</a>
            <a href="{{ route('admin.dashboard', ['audience' => 'kader']) }}" class="px-3 py-1 {{ request('audience') == 'kader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm">Untuk Kader</a>
        </div>

        <!-- Education Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($educations as $education)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Thumbnail Section -->
                <div class="relative">
                    <div class="video-thumbnail h-40 w-full bg-gray-200 flex items-center justify-center">
                        @if($education->thumbnail_url)
                            <img src="{{ $education->thumbnail_url }}" 
                                 alt="{{ $education->judul }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia'">
                        @else
                            <img src="https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia" 
                                 alt="Thumbnail Default"
                                 class="w-full h-full object-cover">
                        @endif
                        
                        @if($education->tipe === 'Video')
                        <div class="play-icon absolute inset-0 flex items-center justify-center">
                            <div class="bg-white bg-opacity-75 rounded-full p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                </svg>
                            </div>
                        </div>
                        @endif
                        
                        @if($education->durasi)
                        <span class="duration-badge absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                            {{ $education->durasi }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800">{{ $education->judul }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full {{ $education->kategori_pengguna === 'Kader' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $education->kategori_pengguna }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($education->isi, 100) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ $education->created_at->diffForHumans() }}</span>
                        <div class="flex space-x-2">
                            <a href="#" class="text-blue-500 hover:text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </a>
                            <form action="#" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">Belum ada materi edukasi tersedia.</p>
                <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Tambah Materi Baru
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection