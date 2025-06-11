@extends('layouts.kader')

@section('title', 'Event Saya')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="eventHandler()">
    <!-- Confirmation Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.away="showModal = false">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Pembatalan</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin membatalkan pendaftaran pelatihan ini?</p>
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
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Pelatihan Saya</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Events Container -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if($pelatihan->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="event-container">
                @foreach($pelatihan as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-gray-100">
                        <div class="p-6">
                            <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->nama_event }}</h4>
                            <div class="space-y-2 text-gray-600 mb-4">
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">Tanggal:</span>
                                    <span class="ml-1">{{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}</span>
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="font-medium">Lokasi:</span>
                                    <span class="ml-1">{{ $event->lokasi }}</span>
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">Waktu:</span>
                                    <span class="ml-1">
                                        @if(strpos($event->waktu, '-') !== false)
                                            {{ $event->waktu }} WIB
                                        @else
                                            {{ \Carbon\Carbon::parse($event->waktu)->format('H:i') }} WIB
                                        @endif
                                    </span>
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">Biaya:</span>
                                    <span class="ml-1">{{ $event->biaya }}</span>
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-medium">Terdaftar:</span>
                                    <span class="ml-1">{{ \Carbon\Carbon::parse($event->tanggal_daftar)->format('d M Y H:i') }}</span>
                                </p>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mb-4">
                                @php
                                    $eventDate = \Carbon\Carbon::parse($event->tanggal);
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                
                                @if($eventDate->isPast())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif($eventDate->isToday())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Hari Ini
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Akan Datang
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg cursor-not-allowed opacity-75" disabled>
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Terdaftar
                                </button>
                                @if(!$eventDate->isPast())
                                    <button 
                                        type="button" 
                                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200"
                                        @click="showConfirmation({{ $event->id }})"
                                    >
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Batalkan
                                    </button>
                                @else
                                    <button class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Terkunci
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-span-full text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Pelatihan</h3>
                <p class="text-lg text-gray-600 mb-4">Anda belum mendaftar pada pelatihan apapun.</p>
                <a href="{{ route('kader.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat Pelatihan Tersedia
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
                    this.showModal = false;
                    // Show success message
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal membatalkan pelatihan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membatalkan pelatihan');
            } finally {
                this.showModal = false;
                this.selectedEventId = null;
            }
        }
    }));
});
</script>
@endpush
@endsection