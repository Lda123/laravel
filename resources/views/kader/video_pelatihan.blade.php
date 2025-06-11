@extends('layouts.kader')

@section('title', 'Video Pelatihan Kader - DengueCare')

@section('content')
@if(isset($video))
    {{-- Detail View --}}
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-video text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $video->judul }}</h1>
                        <p class="text-gray-600">Video Pelatihan untuk Kader</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @php
                        $video_id = '';
                        if (Str::contains($video->tautan, 'youtube.com')) {
                            preg_match('/v=([^&]+)/', $video->tautan, $matches);
                            $video_id = $matches[1] ?? '';
                        } elseif (Str::contains($video->tautan, 'youtu.be')) {
                            preg_match('/youtu\.be\/([^?]+)/', $video->tautan, $matches);
                            $video_id = $matches[1] ?? '';
                        }
                    @endphp

                    <div class="video-container rounded-lg overflow-hidden">
                        @if ($video_id)
                            <iframe src="https://www.youtube.com/embed/{{ $video_id }}?rel=0" frameborder="0" allowfullscreen class="w-full h-96"></iframe>
                        @else
                            <div class="bg-gray-200 h-96 flex items-center justify-center">
                                <p class="text-gray-500">Tautan video tidak valid</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $video->kategori }}
                            </span>
                            <span class="text-gray-500 text-sm flex items-center">
                                <i class="fas fa-eye mr-1"></i> {{ $video->views }}x ditonton
                            </span>
                        </div>
                        
                        <button class="save-btn p-2 rounded-full {{ $isSaved ? 'text-blue-600 bg-blue-100' : 'text-gray-400 hover:bg-gray-100' }}"
                                data-video-id="{{ $video->id }}"
                                data-action="{{ $isSaved ? 'unsave' : 'save' }}">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>

                    <div class="prose max-w-none">
                        <h3 class="font-semibold text-gray-800 mb-2">Deskripsi:</h3>
                        {!! nl2br(e($video->isi)) !!}
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('kader.video-pelatihan') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover flex items-center w-max">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar Video
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- List View --}}
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header with integrated search -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-8 mb-8 shadow-lg animate-fade-in">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-3xl font-bold text-white mb-2">Video Pelatihan Kader</h1>
                <p class="text-xl text-blue-100 mb-6">Tingkatkan kompetensi Anda dengan koleksi video pelatihan pencegahan DBD</p>
                
                <!-- Search Box inside header -->
                <form method="GET" action="{{ route('kader.video-pelatihan') }}" class="bg-white flex items-center px-4 py-2 rounded-full max-w-md mx-auto shadow-sm">
                    <div class="flex-grow flex items-center">
                        <i class="fas fa-search text-blue-500 mr-2"></i>
                        <input type="text" name="search" placeholder="Cari video pelatihan..." 
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
            <form method="GET" action="{{ route('kader.video-pelatihan') }}" class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
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
                    <a href="{{ route('kader.video-pelatihan') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- All Videos Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Semua Video Pelatihan</h2>
                @if($videos->count() > 0)
                    <div class="text-sm text-gray-500">
                        Menampilkan {{ $videos->firstItem() }} - {{ $videos->lastItem() }} dari {{ $videos->total() }} video
                    </div>
                @endif
            </div>
            
            @if($videos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($videos as $video)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                        <!-- Video Thumbnail -->
                        <div class="relative">
                            <div class="video-thumbnail">
                                <img src="{{ $video->thumbnail_url ?? 'https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia' }}" 
                                    alt="{{ $video->judul }}"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia'">
                                <div class="play-icon">
                                    <i class="fas fa-play text-blue-600"></i>
                                </div>
                                @if($video->durasi)
                                    <span class="duration-badge">{{ $video->durasi }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Video Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $video->kategori }}
                                </span>
                                <span class="text-gray-500 text-xs flex items-center">
                                    <i class="fas fa-eye mr-1"></i> {{ $video->views }}x
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-2">{{ $video->judul }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $video->isi }}</p>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center">
                                <a href="{{ route('kader.video_detail', $video->id) }}"
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
                                   onclick="incrementViews('{{ $video->id }}')">
                                    <i class="fas fa-play mr-2"></i> Tonton
                                </a>
                                
                                <button class="save-btn p-2 rounded-full {{ in_array($video->id, $savedVideos) ? 'text-blue-600 bg-blue-100' : 'text-gray-400 hover:bg-gray-100' }}"
                                        data-video-id="{{ $video->id }}"
                                        data-action="{{ in_array($video->id, $savedVideos) ? 'unsave' : 'save' }}">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
            @else
            <div class="col-span-full text-center py-12">
                <div class="inline-block bg-blue-100 p-4 rounded-full mb-4">
                    <i class="fas fa-video text-blue-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">
                    @if(request('search'))
                        Tidak ada hasil untuk "{{ request('search') }}"
                    @else
                        Belum Ada Video Pelatihan
                    @endif
                </h3>
                <p class="text-blue-600">
                    @if(request('search'))
                        Coba gunakan kata kunci yang berbeda atau hapus filter pencarian.
                    @else
                        Video pelatihan akan segera tersedia.
                    @endif
                </p>
                
                @if(request('search') || request('category') != 'all')
                    <a href="{{ route('kader.video-pelatihan') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Lihat Semua Video
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
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
    }
    
    .video-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.8);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .duration-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@push('scripts')
<script>
    // Function to increment video views
    function incrementViews(videoId) {
        fetch(`/kader/video-pelatihan/${videoId}/increment-views`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
    }

    // Save/Unsave video functionality
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.dataset.videoId;
            const action = this.dataset.action;
            
            fetch('{{ route("kader.video-pelatihan.save") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    video_id: videoId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Toggle button state
                    const icon = this.querySelector('i');
                    if (action === 'save') {
                        this.dataset.action = 'unsave';
                        this.classList.remove('text-gray-400', 'hover:bg-gray-100');
                        this.classList.add('text-blue-600', 'bg-blue-100');
                    } else {
                        this.dataset.action = 'save';
                        this.classList.remove('text-blue-600', 'bg-blue-100');
                        this.classList.add('text-gray-400', 'hover:bg-gray-100');
                    }
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const videoId = this.dataset.videoId;
                const action = this.dataset.action;
                const btn = this;
                
                fetch('{{ route("kader.video-pelatihan.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        video_id: videoId,
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
                    showToast('Terjadi kesalahan saat menyimpan video', 'error');
                });
            });
        });
    });

    function showToast(message, type = 'success') {
        // Implementasi toast notification sederhana
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg ${
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