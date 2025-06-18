@extends('layouts.warga')

@section('title', 'Forum Diskusi - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-8 mb-8 shadow-lg animate-fade-in">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Forum Diskusi DBD</h1>
            <p class="text-xl text-blue-100 mb-6">Berbagi informasi, pengalaman, dan tips tentang pencegahan & penanganan Demam Berdarah</p>
            <!-- Search Box -->
            <form method="GET" class="bg-white flex items-center px-4 py-2 rounded-full max-w-md mx-auto shadow-sm">
                <input type="text" name="search" placeholder="Cari diskusi..." 
                       class="flex-grow px-3 py-2 bg-transparent outline-none text-gray-800" 
                       value="{{ request('search') }}">
                <button type="submit" class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="flex-grow">
            <!-- New Post Card (Hidden by default) -->
            <div id="newPostCard" class="bg-white rounded-xl shadow-md mb-6 hidden">
                <form method="POST" action="{{ route('warga.forum.post.store') }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-800 font-semibold">
                            {{ substr(auth('warga')->user()->nama_lengkap ?? 'User', 0, 2) }}
                        </div>
                        <div class="flex-grow">
                            <input type="text" name="topik" required placeholder="Judul diskusi" 
                                   class="w-full px-4 py-3 border-b border-gray-200 focus:outline-none focus:border-blue-500 text-lg font-semibold mb-3">
                            <textarea name="pesan" required placeholder="Apa yang ingin Anda diskusikan?" 
                                      class="w-full px-0 py-2 border-0 focus:ring-0 focus:outline-none text-gray-800 whitespace-pre-wrap resize-none"
                                      rows="3"></textarea>
                            
                            <div id="imagePreview" class="mt-4 hidden relative">
                                <img id="previewImage" src="#" alt="Preview Gambar" class="rounded-lg w-full max-h-96 object-contain">
                                <button type="button" onclick="removeImage()" class="absolute top-2 right-2 bg-white p-2 rounded-full shadow-md text-red-500 hover:bg-red-50">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <label for="gambarInput" class="cursor-pointer text-gray-500 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 transition">
                                        <i class="far fa-image fa-lg"></i>
                                        <input type="file" name="gambar" id="gambarInput" accept="image/*" class="hidden">
                                    </label>
                                </div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 text-white rounded-full font-medium transition">
                                    Posting Diskusi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Discussion List -->
            <div class="space-y-6" id="postsContainer">
                @forelse($posts as $post)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover animate-fade-in" data-animation-delay="{{ $loop->index * 0.1 }}">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-full bg-blue-200 flex-shrink-0 flex items-center justify-center text-blue-800 font-semibold">
                                {{ $post->author_initials }}
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $post->author_name }}</h3>
                                        <span class="text-gray-500 text-sm">
                                            {{ $post->dibuat_pada ? $post->dibuat_pada->translatedFormat('d M Y H:i') : 'Tanggal tidak tersedia' }}
                                        </span>
                                    </div>
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs">Diskusi</span>
                                </div>
                                
                                <h4 class="font-semibold text-xl mb-3 text-gray-800">{{ $post->topik }}</h4>
                                <p class="text-gray-700 whitespace-pre-wrap mb-4">{{ $post->pesan }}</p>
                                
                                @if($post->gambar)
                                <div class="mt-3">
                                    <img src="{{ Storage::url($post->gambar) }}" 
                                         alt="Gambar postingan" 
                                         class="rounded-lg w-full max-h-96 object-contain cursor-zoom-in"
                                         onclick="showImageModal('{{ Storage::url($post->gambar) }}')">
                                </div>
                                @endif
                                
                                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                    <button class="comment-btn flex items-center space-x-2 text-gray-500 hover:text-blue-600 transition"
                                            data-post-id="{{ $post->id }}">
                                        <i class="far fa-comment"></i>
                                        <span>{{ $post->comments->count() }} Komentar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comment Section -->
                    <div id="commentsSection-{{ $post->id }}" class="bg-gray-50 border-t border-gray-200 rounded-b-xl p-4 hidden">
                        <!-- Comment Form -->
                        <form method="POST" action="{{ route('warga.forum.comment.store') }}" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $post->id }}">
                            <div class="flex space-x-3">
                                <div class="w-10 h-10 rounded-full bg-blue-200 flex-shrink-0 flex items-center justify-center text-blue-800 font-semibold">
                                    {{ substr(auth('warga')->user()->nama_lengkap ?? 'User', 0, 2) }}
                                </div>
                                <div class="flex-grow">
                                    <textarea name="pesan" required placeholder="Tulis komentar Anda..." 
                                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 resize-none"
                                              rows="2"></textarea>
                                    
                                    <div id="commentImagePreview-{{ $post->id }}" class="mt-3 hidden relative">
                                        <img id="commentPreviewImage-{{ $post->id }}" src="#" alt="Preview Gambar" class="rounded-lg w-full max-h-60 object-contain">
                                        <button type="button" class="absolute top-2 right-2 bg-white p-2 rounded-full shadow-md text-red-500 hover:bg-red-50" data-post-id="{{ $post->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center justify-between pt-3 mt-3">
                                        <label for="commentGambarInput-{{ $post->id }}" class="cursor-pointer text-gray-500 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 transition">
                                            <i class="far fa-image fa-lg"></i>
                                            <input type="file" name="gambar" id="commentGambarInput-{{ $post->id }}" accept="image/*" class="hidden">
                                        </label>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 text-white rounded-full font-medium transition">
                                            Kirim Komentar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Comments List -->
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            @forelse($post->comments as $comment)
                            <div class="bg-white p-3 rounded-lg border-l-4 border-blue-600">
                                <div class="flex items-start space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-200 flex-shrink-0 flex items-center justify-center text-blue-800 text-sm font-semibold">
                                        {{ $comment->author_initials }}
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-gray-800">{{ $comment->author_name }}</span>
                                            <span class="text-gray-500 text-xs">
                                                {{ $comment->dibuat_pada ? $comment->dibuat_pada->translatedFormat('d M Y H:i') : 'Tanggal tidak tersedia' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $comment->pesan }}</p>
                                        @if($comment->gambar)
                                        <img src="{{ Storage::url($comment->gambar) }}" 
                                             alt="Gambar komentar" 
                                             class="mt-2 rounded-lg max-w-full max-h-60 cursor-zoom-in"
                                             onclick="showImageModal('{{ Storage::url($comment->gambar) }}')">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Belum ada komentar</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm p-12 text-center animate-fade-in">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Belum ada diskusi</h3>
                    <p class="text-gray-500 mb-6">Mulailah diskusi pertama tentang pencegahan DBD</p>
                    <button onclick="showNewPostForm()" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 text-white rounded-full font-medium inline-flex items-center transition">
                        <i class="fas fa-plus mr-2"></i> Buat Diskusi
                    </button>
                </div>
                @endforelse

                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button onclick="showNewPostForm()" class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center text-2xl z-40 transition hover:bg-blue-700 hover:shadow-xl hover:scale-105">
    <i class="fas fa-plus"></i>
</button>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-75">
    <div class="relative max-w-4xl w-full">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white text-3xl hover:text-gray-300 transition">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Gambar besar" class="max-w-full max-h-[90vh] mx-auto rounded-lg shadow-xl">
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for new post
    const gambarInput = document.getElementById('gambarInput');
        if (gambarInput) {
            gambarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewContainer = document.getElementById('imagePreview');
                const previewImage = document.getElementById('previewImage');
                
                if (file) {
                    previewContainer.classList.remove('hidden');
                    
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

    // Apply animation delays
    document.querySelectorAll('[data-animation-delay]').forEach(function(element) {
        const delay = element.getAttribute('data-animation-delay');
        element.style.animationDelay = delay + 's';
    });

    // Setup comment toggle buttons
    document.querySelectorAll('.comment-btn[data-post-id]').forEach(function(button) {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            toggleComments(postId);
        });
    });

    // Setup comment image preview event listeners
    document.querySelectorAll('[id^="commentGambarInput-"]').forEach(function(input) {
        const postId = input.id.replace('commentGambarInput-', '');
        setupCommentImagePreview(postId);
    });

    // Setup remove comment image button event listeners
    document.querySelectorAll('button[data-post-id]').forEach(function(button) {
        if (button.querySelector('.fa-times')) {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                removeCommentImage(postId);
            });
        }
    });
});

// Show/hide new post form
function showNewPostForm() {
    const form = document.getElementById('newPostCard');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        form.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('gambarInput').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
    } else {
        form.classList.add('hidden');
    }
}

function removeImage() {
    const gambarInput = document.getElementById('gambarInput');
    const previewContainer = document.getElementById('imagePreview');
    
    if (gambarInput) gambarInput.value = '';
    if (previewContainer) previewContainer.classList.add('hidden');
}

// Image preview for comment
function setupCommentImagePreview(postId) {
    const input = document.getElementById('commentGambarInput-' + postId);
    if (!input) return;
    
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('commentImagePreview-' + postId);
        const previewImage = document.getElementById('commentPreviewImage-' + postId);
        
        if (file) {
            previewContainer.classList.remove('hidden');
            
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

function removeCommentImage(postId) {
    const input = document.getElementById('commentGambarInput-' + postId);
    const preview = document.getElementById('commentImagePreview-' + postId);
    if (input) input.value = '';
    if (preview) preview.classList.add('hidden');
}

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

// Toggle comments visibility
function toggleComments(postId) {
    const commentsSection = document.getElementById('commentsSection-' + postId);
    commentsSection.classList.toggle('hidden');
    
    // Setup image preview for this comment form
    setupCommentImagePreview(postId);
    
    // Scroll to comments if showing
    if (!commentsSection.classList.contains('hidden')) {
        setTimeout(() => {
            commentsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

// Auto-resize textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush