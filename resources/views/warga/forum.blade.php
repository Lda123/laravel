@extends('layouts.warga')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header Section -->
    <div class="mb-8 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Forum Diskusi DBD</h1>
        <p class="text-lg text-gray-600">Berbagi informasi dan pengalaman tentang pencegahan DBD</p>
    </div>
    
    <!-- Search and New Post -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 animate-slide-in">
        <form method="GET" action="{{ route('warga.forum.index') }}" class="mb-6">
            <div class="flex">
                <input type="text" name="search" placeholder="Cari topik diskusi..." value="{{ $search }}" 
                       class="flex-grow px-4 py-2 border rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition btn-hover flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </div>
        </form>
        
        <button id="togglePostForm" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Postingan Baru
        </button>
        
        <!-- New Post Form (Hidden by default) -->
        <form id="postForm" method="POST" action="{{ route('warga.forum.store') }}" enctype="multipart/form-data" class="mt-6 hidden">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Topik <span class="text-red-500">*</span></label>
                <input type="text" name="topik" required placeholder="Masukkan judul topik" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Pesan <span class="text-red-500">*</span></label>
                <textarea name="pesan" required placeholder="Tulis isi postingan Anda..." 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Gambar (Opsional)</label>
                <input type="file" name="gambar" id="gambarInput" accept="image/*" class="hidden">
                <div class="flex items-center space-x-4">
                    <label for="gambarInput" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-300 transition btn-hover">
                        Pilih Gambar
                    </label>
                    <span id="fileName" class="text-gray-500 text-sm">Tidak ada file dipilih</span>
                </div>
                <div id="imagePreview" class="mt-2 hidden">
                    <img id="previewImage" src="#" alt="Preview Gambar" class="image-preview rounded-lg border">
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelPost" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition btn-hover">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    Posting
                </button>
            </div>
        </form>
    </div>
    
    <!-- Posts List -->
    <div class="space-y-6" id="postsContainer">
        @forelse ($posts as $post)
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover animate-fade-in">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $post->topik }}</h3>
                        <p class="text-gray-500 text-sm">
    Oleh: {{ $post->warga_nama ?? 'Anonim' }}   
    {{ \Carbon\Carbon::parse($post->dibuat_pada)->format('d M Y H:i') }}
</p>

                    </div>
                </div>
                
                <div class="prose max-w-none mb-6">
                    {!! nl2br(e($post->pesan)) !!}
                </div>
                
                @if ($post->gambar)
                <div class="mb-6">
                <img src="{{ asset('storage/' . $post->gambar) }}" 
     alt="Gambar postingan" 
     class="max-w-full h-auto rounded-lg border image-preview cursor-pointer"
     @onclick="showImageModal('{{ asset('storage/' . $post->gambar) }}')">
                </div>
                @endif
                
                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <div class="post-actions flex space-x-4">
                        <button class="flex items-center text-gray-500 hover:text-blue-500 toggle-comments" data-post-id="{{ $post->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Komentar ({{ $post->comments_count }})</span>
                        </button>
                    </div>
                </div>
                
                <!-- Comments Section -->
                <div class="mt-4 pt-4 border-t border-gray-100 hidden comments-section" data-post-id="{{ $post->id }}">
                    <div class="space-y-4" id="comments-{{ $post->id }}">
                    @foreach ($comments[$post->id] ?? [] as $comment)
                    <div class="comment-indent">
    <div class="flex">
        <div class="flex-shrink-0 mr-3">
            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-gray-600 text-sm">{{ substr($comment->warga_nama ?? 'Anonim', 0, 2) }}</span>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 flex-grow">
            <div class="flex justify-between items-start">
                <h4 class="font-medium text-gray-800">{{ $comment->warga_nama ?? 'Anonim' }}</h4>
                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($comment->dibuat_pada)->format('d M Y H:i') }}</span>
            </div>
            <p class="text-gray-600 mt-1">{!! nl2br(e($comment->pesan)) !!}</p>
            @if ($comment->gambar)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $comment->gambar) }}" 
                     alt="Gambar komentar" 
                     class="max-w-xs h-auto rounded-lg border cursor-pointer"
                     @onclick="showImageModal('{{ asset('storage/' . $comment->gambar) }}')">
            </div>
            @endif
        </div>
    </div>
</div>
                    @endforeach
                        @empty
                        <p class="text-gray-500 text-center py-4">Belum ada komentar</p>
                        @endforelse
                        
                        <!-- Comment Form -->
                        <div class="flex mt-6">
                            <div class="flex-shrink-0 mr-3">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 text-sm">{{ substr(Auth::user()->nama_lengkap, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <form method="POST" action="{{ route('warga.forum.store') }}" class="flex">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                    <input type="text" name="pesan" required placeholder="Tulis komentar..." 
                                        class="flex-grow px-4 py-2 border rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-800 mt-4">Belum ada diskusi</h3>
            <p class="text-gray-500 mt-1">Jadilah yang pertama memulai diskusi</p>
            <button id="showPostForm" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover">
                Buat Postingan
            </button>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-screen p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-3xl">&times;</button>
        <img id="modalImage" src="" alt="Gambar besar" class="max-w-full max-h-screen">
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Toggle new post form
        document.getElementById('togglePostForm').addEventListener('click', function() {
            const form = document.getElementById('postForm');
            form.classList.toggle('hidden');
            this.classList.toggle('hidden');
            
            if (!form.classList.contains('hidden')) {
                form.scrollIntoView({ behavior: 'smooth' });
            }
        });
        
        document.getElementById('cancelPost').addEventListener('click', function() {
            document.getElementById('postForm').classList.add('hidden');
            document.getElementById('togglePostForm').classList.remove('hidden');
        });
        
        document.getElementById('showPostForm')?.addEventListener('click', function() {
            document.getElementById('postForm').classList.remove('hidden');
            document.getElementById('togglePostForm').classList.add('hidden');
            document.getElementById('postForm').scrollIntoView({ behavior: 'smooth' });
        });
        
        // Toggle comments
        document.querySelectorAll('.toggle-comments').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                const commentsSection = document.querySelector(`.comments-section[data-post-id="${postId}"]`);
                commentsSection.classList.toggle('hidden');
                
                if (!commentsSection.classList.contains('hidden')) {
                    commentsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
        
        // Image preview for new post
        document.getElementById('gambarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileName = document.getElementById('fileName');
            const previewContainer = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            
            if (file) {
                fileName.textContent = file.name;
                previewContainer.classList.remove('hidden');
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = 'Tidak ada file dipilih';
                previewContainer.classList.add('hidden');
            }
        });
        
        // Image modal functions
        function showImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>

@endpush

@endsection