@extends('layouts.kader')

@section('title', 'Dashboard Kader Kesehatan')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="dashboardHandler()">
    <!-- Confirmation Modal -->
    <div x-show="showCancelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.away="showCancelModal = false">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Pembatalan</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin membatalkan pendaftaran pelatihan ini?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showCancelModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button @click="cancelRegistration()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.away="showSuccessModal = false">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-3" x-text="successMessage"></h3>
                <div class="mt-5">
                    <button @click="showSuccessModal = false" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="mb-8 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $greeting }}, 
            <span class="text-blue-600">{{ $kader->nama_lengkap }}</span>!
        </h1>
        <p class="text-lg text-gray-600">Selamat Bekerja!</p>
    </div>

    <!-- Notifikasi Session -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded animate-fade-in">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded animate-fade-in">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Notifikasi Penting -->
    @if(isset($notifikasi))
    <div class="mb-8 bg-white rounded-lg shadow p-6 hover:shadow-md transition">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Ada Notifikasi Penting yang Masuk!</h2>
        <ul class="list-disc pl-5">
            <li class="mb-2">
                <strong>{{ $notifikasi['title'] }}</strong>
                <p class="text-gray-600">{{ $notifikasi['message'] }}</p>
            </li>
        </ul>
    </div>
    @endif

    <!-- Quick Links -->
    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Mungkin Ada yang Perlu Kamu Lihat</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Pelatihan Saya -->
            <a href="{{ route('pelatihan.saya') }}" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="font-medium"> Event Pelatihan Saya</span>
            </a>
            
            <!-- Laporan Harian -->
            <a href="{{ route('laporan.harian') }}" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="font-medium">Tracking Harian</span>
            </a>
            
            <!-- Laporan Bulanan -->
            <a href="{{ route('laporan.index') }}" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="font-medium">Laporan Bulanan</span>
            </a>
            
            <!-- Data Warga -->
            <a href="{{ route('data-warga') }}" class="bg-white rounded-lg p-6 text-center shadow hover:shadow-md transition hover:bg-blue-50">
                <div class="text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="font-medium">Data Warga</span>
            </a>
        </div>
    </section>

    <!-- Pelatihan Section -->
    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Pelatihan Tersedia (Khusus Kader)</h3>
        
        @if($events->count() > 0)
            <input type="text" id="search-bar" placeholder="Cari Pelatihan" 
                   class="w-full px-4 py-2 mb-6 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            
            <!-- Pelatihan Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="pelatihan-grid">
                @foreach($events as $event)
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition hover:shadow-lg" 
                     data-name="{{ strtolower($event->nama_event) }}">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->nama_event }}</h4>
                        
                        <div class="space-y-2 text-gray-600 mb-4">
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $event->tanggal }}
                            </p>
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->lokasi }}
                            </p>
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $event->waktu }}
                            </p>
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $event->biaya }}
                            </p>
                        </div>
                        
                        @if(in_array($event->id, $registeredEvents))
                            <div class="flex gap-2">
                                <button class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Terdaftar
                                </button>
                                <button 
                                    type="button" 
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center"
                                    @click="showCancelConfirmation({{ $event->id }})"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batalkan
                                </button>
                            </div>
                        @else
                            <button 
                                type="button" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center"
                                @click="registerEvent({{ $event->id }})"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Daftar
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-600">Belum ada pelatihan tersedia</h3>
                <p class="text-gray-500">Pelatihan khusus kader akan muncul di sini</p>
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardHandler', () => ({
        showCancelModal: false,
        showSuccessModal: false,
        selectedEventId: null,
        successMessage: '',
        
        showCancelConfirmation(eventId) {
            this.selectedEventId = eventId;
            this.showCancelModal = true;
        },
        
        async registerEvent(eventId) {
            try {
                const response = await fetch('{{ route("daftar-pelatihan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ event_id: eventId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.successMessage = 'Berhasil mendaftar pelatihan!';
                    this.showSuccessModal = true;
                    // Refresh page after 1.5 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal mendaftar pelatihan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mendaftar pelatihan');
            }
        },
        
        async cancelRegistration() {
            if (!this.selectedEventId) return;
            
            try {
                const response = await fetch('{{ route("batalkan-pelatihan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ cancel: this.selectedEventId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showCancelModal = false;
                    this.successMessage = 'Berhasil membatalkan pendaftaran!';
                    this.showSuccessModal = true;
                    // Refresh page after 1.5 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal membatalkan pendaftaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membatalkan pendaftaran');
            } finally {
                this.showCancelModal = false;
            }
        }
    }));
});

// Fitur pencarian pelatihan
const searchBar = document.getElementById('search-bar');
if (searchBar) {
    searchBar.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const pelatihanCards = document.querySelectorAll('#pelatihan-grid > div');
        
        pelatihanCards.forEach(card => {
            const eventName = card.getAttribute('data-name');
            if (eventName.includes(searchValue)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}
</script>
@endpush
@endsection