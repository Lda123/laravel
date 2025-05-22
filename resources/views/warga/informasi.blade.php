@extends('layouts.warga')

@section('title', 'Informasi Edukasi')

@section('custom-css')
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-hover {
            transition: all 0.2s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
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
    </style>
@endsection

@section('content')
    @if (isset($content))
        {{-- Detail View --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $content->judul }}</h1>
                        <p class="text-gray-600">Edukasi {{ $content->tipe }} untuk Warga</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @if ($content->tipe === 'Video')
                        @php
                            $video_id = '';
                            if (Str::contains($content->tautan, 'youtube.com')) {
                                preg_match('/v=([^&]+)/', $content->tautan, $matches);
                                $video_id = $matches[1] ?? '';
                            } elseif (Str::contains($content->tautan, 'youtu.be')) {
                                preg_match('/youtu\.be\/([^?]+)/', $content->tautan, $matches);
                                $video_id = $matches[1] ?? '';
                            }
                        @endphp

                        <div class="video-container rounded-lg overflow-hidden">
                            @if ($video_id)
                                <iframe src="https://www.youtube.com/embed/{{ $video_id }}?rel=0" frameborder="0" allowfullscreen></iframe>
                            @else
                                <div class="bg-gray-200 h-full flex items-center justify-center">
                                    <p class="text-gray-500">Tautan video tidak valid</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-2">Tautan Artikel:</h3>
                            <a href="{{ $content->tautan }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                {{ $content->tautan }}
                            </a>
                        </div>
                    @endif

                    <div class="prose max-w-none">
                        <h3 class="font-semibold text-gray-800 mb-2">Deskripsi:</h3>
                        {!! nl2br(e($content->isi)) !!}
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('warga.informasi.index') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover flex items-center w-max">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Daftar Edukasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- List View --}}
        <div class="mb-8 animate-fade-in">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Edukasi DBD</h1>
            <p class="text-lg text-gray-600">Temukan informasi dan video edukasi tentang pencegahan DBD</p>
        </div>

        {{-- Search Filter --}}
        <form method="GET" action="{{ route('warga.informasi.index') }}" class="bg-white rounded-xl shadow-md p-6 mb-8 animate-slide-in space-y-4 md:space-y-0 md:flex md:space-x-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau kata kunci..."
                   class="flex-grow w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />

            <select name="tipe" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all" {{ request('tipe') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                <option value="Video" {{ request('tipe') == 'Video' ? 'selected' : '' }}>Video</option>
                <option value="Artikel" {{ request('tipe') == 'Artikel' ? 'selected' : '' }}>Artikel</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari
            </button>
        </form>

        {{-- Content Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($edukasiList as $e)
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
                    <div class="p-6 h-full flex flex-col">
                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $e->tipe === 'Video' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $e->tipe }}
                            </span>
                            <h3 class="text-xl font-semibold text-gray-800 mt-2 mb-2">{{ $e->judul }}</h3>
                            <p class="text-gray-600 line-clamp-3">{{ $e->isi }}</p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('warga.informasi.show', $e->id) }}"
                               class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover flex items-center justify-center">
                                Lihat {{ $e->tipe === 'Video' ? 'Video' : 'Artikel' }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 mt-4">Tidak ada hasil ditemukan</h3>
                    <p class="text-gray-500 mt-1">Coba gunakan kata kunci lain atau filter yang berbeda</p>
                </div>
            @endforelse
        </div>
    @endif
@endsection
