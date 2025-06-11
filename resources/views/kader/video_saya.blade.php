@extends('layouts.kader')

@section('title', 'Video Saya - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="videoHandler()">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Video Saya</h2>
            <p class="text-gray-600">Koleksi video pelatihan yang telah Anda simpan</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium">
                Total Disimpan: {{ $totalSaved }}
            </span>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Videos Container -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if($savedVideos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($savedVideos as $saved)
                    @php $video = $saved->video; @endphp
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-gray-100">
                        <!-- Video Thumbnail -->
                        <div class="relative">
                            <div class="video-thumbnail">
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
                                
                                @if ($video_id)
                                    <img src="https://img.youtube.com/vi/{{ $video_id }}/mqdefault.jpg" 
                                        alt="{{ $video->judul }}"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia'">
                                @else
                                    <img src="https://via.placeholder.com/800x450?text=Thumbnail+Tidak+Tersedia" 
                                        alt="{{ $video->judul }}">
                                @endif
                                <div class="play-icon">
                                    <i class="fas fa-play text-blue-600"></i>
                                </div>
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
                            
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="far fa-calendar-alt mr-2"></i>
                                Disimpan pada: {{ $saved->saved_at->format('d M Y H:i') }}
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center">
                                <a href="{{ route('kader.video_detail', $video->id) }}"
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                                    <i class="fas fa-play mr-2"></i> Tonton
                                </a>
                                
                                <button type="button" 
                                        @click="openDeleteModal('{{ $saved->id }}')"
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-full transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                
                                <!-- Hidden form for each video -->
                                <form id="delete-form-{{ $saved->id }}" 
                                      action="{{ route('kader.video-saya.destroy', $saved->id) }}" 
                                      method="POST" 
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $savedVideos->links() }}
            </div>
        @else
            <div class="col-span-full text-center py-12">
                <div class="inline-block bg-blue-100 p-4 rounded-full mb-4">
                    <i class="fas fa-video text-blue-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">
                    Belum Ada Video Disimpan
                </h3>
                <p class="text-blue-600 mb-4">
                    Anda belum menyimpan video pelatihan apapun.
                </p>
                <a href="{{ route('kader.video-pelatihan') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-arrow-right mr-2"></i> Jelajahi Video Pelatihan
                </a>
            </div>
        @endif
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl" 
             @click.away="showDeleteModal = false">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Penghapusan</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus video ini dari koleksi Anda?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showDeleteModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button @click="deleteVideo()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

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
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('videoHandler', () => ({
        showDeleteModal: false,
        videoIdToDelete: null,
        
        openDeleteModal(videoId) {
            this.videoIdToDelete = videoId;
            this.showDeleteModal = true;
        },
        
        deleteVideo() {
            if (this.videoIdToDelete) {
                const form = document.getElementById(`delete-form-${this.videoIdToDelete}`);
                if (form) {
                    form.submit();
                }
            }
            this.showDeleteModal = false;
        }
    }));
});
</script>
@endpush
@endsection