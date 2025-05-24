@extends('layouts.warga')

@section('title', 'Event Saya - DengueCare')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Halo! Ini 
            <span class="text-blue-600">Event yang Kamu Ikuti</span>:
        </h1>
    </div>

    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Events Grid -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if(count($events) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="event-container">
                @foreach($events as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" id="event-{{ $event->id }}">
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
                            </div>
                            
                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                    Terdaftar
                                </button>
                                <button type="button" data-event-id="{{ $event->id }}" class="cancel-event-btn flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
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
<div x-data="popupNotification()" x-show="isOpen" @show-popup.window="show($event.detail)" 
     class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0" x-show="type === 'success'">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-shrink-0" x-show="type === 'error'">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p x-text="message" class="text-sm font-medium text-gray-900"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="close()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('popupNotification', () => ({
        isOpen: false,
        type: 'success',
        message: '',
        show(detail) {
            this.type = detail.type;
            this.message = detail.message;
            this.isOpen = true;
            
            // Auto close after 5 seconds
            setTimeout(() => {
                this.close();
            }, 5000);
        },
        close() {
            this.isOpen = false;
        }
    }));
});
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    document.querySelectorAll('.cancel-event-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            cancel(eventId, csrfToken);
        });
    });
});

function cancel(id_event, csrfToken) {
    if (!confirm('Apakah Anda yakin ingin membatalkan pendaftaran event ini?')) return;

    const card = document.getElementById('event-' + id_event);
    card.classList.add('opacity-50', 'pointer-events-none'); // Indikasi sedang diproses

    fetch(`/eventsaya/${id_event}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id_event })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            card.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
            setTimeout(() => {
                card.remove();
                checkEmptyContainer();
                // Trigger Alpine.js popup
                const event = new CustomEvent('show-popup', {
                    detail: {
                        type: 'success',
                        message: 'Event berhasil dibatalkan'
                    }
                });
                document.dispatchEvent(event);
            }, 300);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        card.classList.remove('opacity-50', 'pointer-events-none');
        // Trigger Alpine.js popup for error
        const event = new CustomEvent('show-popup', {
            detail: {
                type: 'error',
                message: 'Terjadi kesalahan saat membatalkan event'
            }
        });
        document.dispatchEvent(event);
    });
}

// Function to check empty container (if needed)
function checkEmptyContainer() {
    // Your implementation here
}
</script>
@endpush
@endsection