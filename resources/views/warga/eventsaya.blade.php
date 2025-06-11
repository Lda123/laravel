@extends('layouts.warga')

@section('title', 'Event Saya')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="eventHandler()">
    <!-- Confirmation Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.away="showModal = false">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Pembatalan</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin membatalkan pendaftaran event ini?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button @click="cancelEvent()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>

    <!-- Page Title -->
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Event Saya</h2>

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Events Container -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="event-container">
                @foreach($events as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->nama_event }}</h4>
                            <div class="space-y-2 text-gray-600 mb-4">
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}
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
                                    @if(strpos($event->waktu, '-') !== false)
                                        {{ $event->waktu }} WIB
                                    @else
                                        {{ \Carbon\Carbon::parse($event->waktu)->format('H:i') }} WIB
                                    @endif
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $event->biaya }}
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($event->tanggal_daftar)->format('d M Y H:i') }}
                                </p>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                    Terdaftar
                                </button>
                                <button 
                                    type="button" 
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                    @click="showConfirmation({{ $event->id }})"
                                >
                                    Batalkan
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-span-full text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg text-gray-600">Belum ada event yang kamu ikuti.</p>
                <a href="{{ route('warga.dashboard') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lihat Event Tersedia
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('eventHandler', () => ({
        showModal: false,
        selectedEventId: null,
        
        showConfirmation(eventId) {
            this.selectedEventId = eventId;
            this.showModal = true;
        },
        
        async cancelEvent() {
            if (!this.selectedEventId) return;
            
            try {
                const response = await fetch('{{ route("warga.cancel-event") }}', {
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
                    this.showModal = false;
                    // Otomatis reload halaman setelah sukses
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Gagal membatalkan event');
                }
            } catch (error) {
                console.error('Error:', error);
                // Tidak ada alert di sini, tapi bisa log error
            } finally {
                this.showModal = false;
            }
        }
    }));
});
</script>
@endpush
@endsection