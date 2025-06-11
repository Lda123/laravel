@extends('layouts.warga')

@section('title', 'Video Edukasi DBD - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-video text-green-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $video->judul }}</h1>
                    <p class="text-gray-600">Video Edukasi untuk Warga</p>
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
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $video->kategori }}
                        </span>
                        <span class="text-gray-500 text-sm flex items-center">
                            <i class="fas fa-eye mr-1"></i> {{ $video->views }}x ditonton
                        </span>
                    </div>
                    
                    @auth
                    <button class="save-btn p-2 rounded-full {{ $isSaved ? 'text-green-600 bg-green-100' : 'text-gray-400 hover:bg-gray-100' }}"
                            data-video-id="{{ $video->id }}"
                            data-action="{{ $isSaved ? 'unsave' : 'save' }}">
                        <i class="fas fa-bookmark"></i>
                    </button>
                    @endauth
                </div>

                <div class="prose max-w-none">
                    <h3 class="font-semibold text-gray-800 mb-2">Deskripsi:</h3>
                    {!! nl2br(e($video->isi)) !!}
                </div>

                <div class="pt-4">
                    <a href="{{ route('warga.video-edukasi') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover flex items-center w-max">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Video
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    // Function to increment video views when page loads
    document.addEventListener('DOMContentLoaded', function() {
        fetch(`/warga/video-edukasi/{{ $video->id }}/increment-views`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
    });

    // Save/Unsave video functionality
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.querySelector('.save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                const videoId = this.dataset.videoId;
                const action = this.dataset.action;
                const btn = this;
                
                fetch('{{ route("warga.video-edukasi.save") }}', {
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
                            btn.classList.add('text-green-600', 'bg-green-100');
                        } else {
                            btn.classList.remove('text-green-600', 'bg-green-100');
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
        }
    });

    function showToast(message, type = 'success') {
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