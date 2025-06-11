@extends('layouts.kader')

@section('title', 'Buku Panduan Kader - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header with integrated search -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-8 mb-8 shadow-lg animate-fade-in">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Buku Panduan Kader</h1>
            <p class="text-xl text-blue-100 mb-6">Kumpulan panduan lengkap untuk pengembangan kader kesehatan dalam pencegahan DBD</p>
            
            <!-- Search Box inside header -->
            <form method="GET" action="{{ route('buku-panduan.search') }}" class="bg-white flex items-center px-4 py-2 rounded-full max-w-md mx-auto shadow-sm">
                <div class="flex-grow flex items-center">
                    <i class="fas fa-search text-blue-500 mr-2"></i>
                    <input type="text" name="search" placeholder="Cari buku panduan..." 
                           class="flex-grow px-3 py-2 bg-transparent outline-none text-gray-800" 
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Rest of the content -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(isset($bukuPanduan) && $bukuPanduan->count() > 0)
            @foreach($bukuPanduan as $buku)
            <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover" data-book-id="{{ $buku->id }}">
                <!-- Cover Image Section -->
                <div class="h-48 bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center bg-cover bg-center book-cover relative overflow-hidden">
                    @if($buku->cover_image)
                    <img src="{{ route('buku-panduan.cover', ['filename' => basename($buku->cover_image)]) }}" 
                        alt="Cover {{ $buku->judul }}" 
                        class="absolute inset-0 w-full h-full object-cover"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    @endif
                    
                    <!-- Fallback title display -->
                    <div class="text-white text-center px-4 {{ $buku->cover_image ? 'hidden' : 'flex' }} flex-col items-center justify-center h-full">
                        <i class="fas fa-book text-4xl mb-2 opacity-50"></i>
                        <h3 class="text-xl font-bold">
                            {{ strtoupper($buku->judul ?? 'BUKU PANDUAN') }}
                        </h3>
                    </div>
                </div>
                
                <!-- Book Info Section -->
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-blue-800 mb-2 line-clamp-2">{{ $buku->judul ?? 'Judul Tidak Tersedia' }}</h4>
                    
                    <!-- Book Details -->
                    <div class="space-y-1 mb-4">
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-user-pen text-blue-500 mr-2 w-4"></i>{{ $buku->penulis ?? 'Penulis tidak diketahui' }}
                        </p>
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-calendar text-blue-500 mr-2 w-4"></i>Tahun: {{ $buku->tahun_terbit ?? '-' }}
                        </p>
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-graduation-cap text-blue-500 mr-2 w-4"></i>Level: {{ $buku->kelas ?? 'Dasar' }}
                        </p>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $buku->deskripsi ?? 'Deskripsi tidak tersedia' }}</p>
                    
                    <!-- File Info -->
                    <div class="flex justify-between text-sm text-gray-500 mb-4">
                        <span><i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF</span>
                        <span><i class="fas fa-book text-blue-500 mr-1"></i> {{ $buku->halaman ?? 0 }} halaman</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        @if($buku->file_pdf)
                            @php
                                $fileExists = \Storage::disk('public')->exists('bukupanduan/' . $buku->file_pdf);
                            @endphp
                            
                            @if($fileExists)
                                <div class="flex gap-2">
                                    <!-- Download Button -->
                                    <a href="{{ route('buku-panduan.download', $buku->id) }}" 
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition btn-hover download-btn">
                                        <i class="fas fa-download mr-2"></i> Download
                                    </a>
                                    
                                    <!-- Preview Button -->
                                    <a href="{{ route('buku-panduan.stream', $buku->id) }}" 
                                       target="_blank"
                                       class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover">
                                        <i class="fas fa-eye mr-2"></i> Preview
                                    </a>
                                </div>
                            @else
                                <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-400 text-white rounded-lg cursor-not-allowed" disabled>
                                    <i class="fas fa-exclamation-circle mr-2"></i> File Tidak Ditemukan
                                </button>
                            @endif
                        @else
                            <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                <i class="fas fa-exclamation-circle mr-2"></i> File Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-12">
                <div class="inline-block bg-blue-100 p-4 rounded-full mb-4">
                    <i class="fas fa-book-open text-blue-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">
                    @if(isset($searchTerm) && $searchTerm)
                        Tidak ada hasil untuk "{{ $searchTerm }}"
                    @else
                        Belum Ada Buku Panduan
                    @endif
                </h3>
                <p class="text-blue-600">
                    @if(isset($searchTerm) && $searchTerm)
                        Coba gunakan kata kunci yang berbeda atau hapus filter pencarian.
                    @else
                        Buku panduan akan segera tersedia.
                    @endif
                </p>
                
                @if(isset($searchTerm) && $searchTerm)
                    <a href="{{ route('buku-panduan.index') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Lihat Semua Buku
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat file...</p>
    </div>
</div>

<script>
    // Global Functions
    function setSearch(term) {
        const searchInput = document.querySelector('input[name="search"]');
        const form = document.querySelector('form');
        
        if (searchInput && form) {
            searchInput.value = term;
            
            // Trigger submit form dengan method GET
            form.submit();
        }
    }
    // Show loading modal
    function showLoading() {
        document.getElementById('loadingModal').classList.remove('hidden');
        document.getElementById('loadingModal').classList.add('flex');
    }
    
    // Hide loading modal
    function hideLoading() {
        document.getElementById('loadingModal').classList.add('hidden');
        document.getElementById('loadingModal').classList.remove('flex');
    }
    
    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        // Live search functionality
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // Pastikan form disubmit dengan GET
                    this.form.submit();
                }
            });
        }
        
        // Enhanced download button functionality
        const downloadBtns = document.querySelectorAll('.download-btn');
        downloadBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                const originalContent = this.innerHTML;
                
                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengunduh...';
                this.classList.add('bg-blue-500', 'cursor-wait');
                this.disabled = true;
                
                // Reset button after download starts
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.classList.remove('bg-blue-500', 'cursor-wait');
                    this.disabled = false;
                }, 3000);
            });
        });
        
        // Handle cover image errors
        const coverImages = document.querySelectorAll('.book-cover img');
        coverImages.forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const fallback = this.nextElementSibling;
                if (fallback) {
                    fallback.style.display = 'flex';
                    fallback.classList.remove('hidden');
                }
            });
        });
        
        // Add hover effects
        const bookCards = document.querySelectorAll('.card-hover');
        bookCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        });
    });
    
    // Utility function to check file availability
    async function checkFileAvailability(bookId) {
        try {
            const response = await fetch(`/kader/buku-panduan/${bookId}/info`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error checking file availability:', error);
            return null;
        }
    }
</script>
@endsection