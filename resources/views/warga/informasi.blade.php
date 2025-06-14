@extends('layouts.warga')

@section('title', 'Edukasi DBD - DengueCare')

@section('content')
@if(isset($detailItem) && $detailItem)
    {{-- Hanya menangani artikel saja --}}
    @if($detailItem->tipe == 'Artikel')
        {{-- Detail View - Improved Article Section --}}
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-newspaper text-blue-600 text-xl"></i>
                        </div>
                        <div class="flex-grow">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $detailItem->judul }}</h1>
                            <p class="text-gray-600">Artikel Edukasi DBD</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $detailItem->kategori }}
                                </span>
                                <span class="text-gray-500 text-sm flex items-center">
                                    <i class="fas fa-eye mr-1"></i> {{ $detailItem->views }}x dilihat
                                </span>
                                <span class="text-gray-500 text-sm flex items-center">
                                    <i class="fas fa-calendar mr-1"></i> {{ $detailItem->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        @auth('warga')
                        <button class="save-btn p-3 rounded-full {{ in_array($detailItem->id, $savedItems) ? 'text-blue-600 bg-blue-100' : 'text-gray-400 hover:bg-gray-100' }}"
                                data-edukasi-id="{{ $detailItem->id }}"
                                data-action="{{ in_array($detailItem->id, $savedItems) ? 'unsave' : 'save' }}">
                            <i class="fas fa-bookmark text-lg"></i>
                        </button>
                        @endauth
                    </div>

                    <div class="space-y-6">
                        {{-- Article Source Link (improved) --}}
                        @if($detailItem->tautan)
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-1">
                                    <i class="fas fa-external-link-alt text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Sumber Artikel:</strong>
                                        <a href="{{ $detailItem->tautan }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="text-blue-600 hover:text-blue-800 underline ml-2 font-medium break-all">
                                            {{ Str::limit($detailItem->tautan, 50) }}
                                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Link akan terbuka di tab baru
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Article Content with improved link handling --}}
                        <div class="article-content bg-white border rounded-lg p-8 shadow-sm">
                            <div class="prose prose-lg max-w-none">
                                @php
                                    // Process content to make links clickable and open in new tab
                                    $processedContent = preg_replace_callback(
                                        '/<a\s[^>]*href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/i',
                                        function($matches) {
                                            $url = $matches[1];
                                            $text = $matches[2];
                                            // Only modify if it's an external link
                                            if (filter_var($url, FILTER_VALIDATE_URL) && 
                                                !str_contains($url, request()->getHttpHost())) {
                                                return '<a href="'.e($url).'" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">'.e($text).' <i class="fas fa-external-link-alt text-xs ml-1"></i></a>';
                                            }
                                            return $matches[0];
                                        },
                                        $detailItem->isi
                                    );
                                @endphp
                                {!! $processedContent !!}
                            </div>
                        </div>

                        {{-- Improved Related Links Section --}}
                        @if($detailItem->tautan)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                <i class="fas fa-link mr-2 text-gray-600"></i>
                                Tautan Terkait:
                            </h4>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                <a href="{{ $detailItem->tautan }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Kunjungi Sumber Asli
                                </a>
                                <span class="text-xs text-gray-500 sm:ml-2">
                                    <i class="fas fa-info-circle mr-1"></i> Link akan terbuka di tab baru
                                </span>
                            </div>
                        </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 pt-6 border-t">
                            <a href="{{ route('warga.informasi') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar Edukasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    {{-- List View --}}
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header with integrated search -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-8 mb-8 shadow-lg animate-fade-in">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-3xl font-bold text-white mb-2">Edukasi DBD</h1>
                <p class="text-xl text-blue-100 mb-6">Tingkatkan pengetahuan Anda tentang pencegahan dan penanganan DBD</p>
                
                <!-- Search Box inside header -->
                <form method="GET" action="{{ route('warga.informasi') }}" class="bg-white flex items-center px-4 py-2 rounded-full max-w-md mx-auto shadow-sm">
                    <div class="flex-grow flex items-center">
                        <i class="fas fa-search text-blue-500 mr-2"></i>
                        <input type="text" name="search" placeholder="Cari edukasi..." 
                               class="flex-grow px-3 py-2 bg-transparent outline-none text-gray-800" 
                               value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-8">
            <form method="GET" action="{{ route('warga.informasi') }}" class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <!-- Type Filter -->
                <div class="flex-grow md:w-auto">
                    <select name="type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="Video" {{ request('type') == 'Video' ? 'selected' : '' }}>Video</option>
                        <option value="Artikel" {{ request('type') == 'Artikel' ? 'selected' : '' }}>Artikel</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div class="flex-grow md:w-auto">
                    <select name="category" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('warga.informasi') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- All Edukasi Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    @if(request('type') == 'Video')
                        Video Edukasi
                    @elseif(request('type') == 'Artikel')
                        Artikel Edukasi
                    @else
                        Semua Edukasi
                    @endif
                </h2>
                @if($edukasiItems->count() > 0)
                    <div class="text-sm text-gray-500">
                        Menampilkan {{ $edukasiItems->firstItem() }} - {{ $edukasiItems->lastItem() }} dari {{ $edukasiItems->total() }} item
                    </div>
                @endif
            </div>
            
            @if($edukasiItems->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($edukasiItems as $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                    <!-- Thumbnail/Header -->
                    <div class="relative">
                        @if($item->tipe == 'Video')
                            <!-- Video Thumbnail -->
                            <div class="video-thumbnail">
                                <img src="{{ $item->thumbnail_url ?? 'https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia' }}" 
                                    alt="{{ $item->judul }}"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia'"
                                    class="w-full h-48 object-cover">
                                <div class="play-icon">
                                    <i class="fas fa-play text-white text-xl"></i>
                                </div>
                            </div>
                        @else
                            <!-- Article Thumbnail -->
                            <div class="h-48 relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-blue-400 text-6xl opacity-30"></i>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
                                <div class="absolute bottom-4 left-4">
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                                        Artikel
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Info -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex space-x-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $item->kategori }}
                                </span>
                                @if($item->tipe == 'Video')
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                        Video
                                    </span>
                                @endif
                            </div>
                            <span class="text-gray-500 text-xs flex items-center">
                                <i class="fas fa-eye mr-1"></i> {{ $item->views }}x
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-2">{{ $item->judul }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit(strip_tags($item->isi), 100) }}</p>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('warga.informasi.view', $item->id) }}"
                            class="view-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
                            data-edukasi-id="{{ $item->id }}">
                                <i class="fas {{ $item->tipe == 'Video' ? 'fa-play' : 'fa-book-open' }} mr-2"></i> 
                                {{ $item->tipe == 'Video' ? 'Tonton' : 'Baca' }}
                            </a>
                            @auth('warga')
                            <button class="save-btn p-2 rounded-full {{ in_array($item->id, $savedItems) ? 'text-blue-600 bg-blue-100' : 'text-gray-400 hover:bg-gray-100' }}"
                                    data-edukasi-id="{{ $item->id }}"
                                    data-action="{{ in_array($item->id, $savedItems) ? 'unsave' : 'save' }}">
                                <i class="fas fa-bookmark"></i>
                            </button>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $edukasiItems->links() }}
            </div>
            @else
            <div class="col-span-full text-center py-12">
                <div class="inline-block bg-blue-100 p-4 rounded-full mb-4">
                    <i class="fas {{ request('type') == 'Artikel' ? 'fa-newspaper' : 'fa-video' }} text-blue-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">
                    @if(request('search'))
                        Tidak ada hasil untuk "{{ request('search') }}"
                    @else
                        Belum Ada {{ request('type') == 'Video' ? 'Video' : (request('type') == 'Artikel' ? 'Artikel' : 'Edukasi') }}
                    @endif
                </h3>
                <p class="text-blue-600">
                    @if(request('search'))
                        Coba gunakan kata kunci yang berbeda atau hapus filter pencarian.
                    @else
                        Konten edukasi akan segera tersedia.
                    @endif
                </p>
                
                @if(request('search') || request('category') != 'all' || request('type') != 'all')
                    <a href="{{ route('warga.informasi') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Lihat Semua
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
@endif

@push('styles')
<style>
    .video-thumbnail {
        position: relative;
        height: 12rem; /* Fixed height for consistency */
        overflow: hidden;
    }
    
    .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .card-hover:hover .video-thumbnail img {
        transform: scale(1.05);
    }
    
    .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(37, 99, 235, 0.8); /* blue-600 with opacity */
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .card-hover:hover .play-icon {
        background: rgba(29, 78, 216, 0.9); /* blue-700 with opacity */
        width: 3.2rem;
        height: 3.2rem;
    }
    
    .duration-badge {
        position: absolute;
        bottom: 0.75rem;
        right: 0.75rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
    }
    
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e5e7eb; /* gray-200 */
    }
    
    .card-hover:hover {
        transform: translateY(-0.25rem);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Article specific styles */
    .article-thumbnail {
        position: relative;
        height: 12rem; /* Match video thumbnail height */
        overflow: hidden;
    }
    
    .article-thumbnail i {
        transition: transform 0.3s ease;
    }
    
    .card-hover:hover .article-thumbnail i {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle view button clicks with AJAX view increment
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const edukasiId = this.dataset.edukasiId;
                const url = this.href;
                
                // Increment views via AJAX
                fetch(`/warga/informasi/${edukasiId}/increment-views`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Navigate to detail page
                        window.location.href = url;
                    } else {
                        // If increment fails, still navigate
                        window.location.href = url;
                    }
                })
                .catch(error => {
                    console.error('Error incrementing views:', error);
                    // Still navigate even if view increment fails
                    window.location.href = url;
                });
            });
        });

        // Save/Unsave edukasi functionality
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const edukasiId = this.dataset.edukasiId;
                const action = this.dataset.action;
                const btn = this;
                
                fetch('{{ route("warga.informasi.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        edukasi_id: edukasiId,
                        action: action
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Update button state
                        btn.dataset.action = data.action;
                        
                        if (data.action === 'unsave') {
                            btn.classList.remove('text-gray-400', 'hover:bg-gray-100');
                            btn.classList.add('text-blue-600', 'bg-blue-100');
                        } else {
                            btn.classList.remove('text-blue-600', 'bg-blue-100');
                            btn.classList.add('text-gray-400', 'hover:bg-gray-100');
                        }
                        
                        // Show toast notification
                        showToast(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menyimpan edukasi', 'error');
                });
            });
        });
    });

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush
@endsection