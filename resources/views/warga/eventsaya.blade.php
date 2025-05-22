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
                alert('Event berhasil dibatalkan');
            }, 300);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        card.classList.remove('opacity-50', 'pointer-events-none');
        alert('Terjadi kesalahan saat membatalkan event');
    });
}

function checkEmptyContainer() {
    const container = document.getElementById('event-container');
    // Check if there are any event cards left
    const remaining = container.querySelectorAll('[id^="event-"]');
    
    if (remaining.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg text-gray-600">Belum ada event yang kamu ikuti.</p>
                <a href="{{ route('warga.dashboard') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lihat Event Tersedia
                </a>
            </div>
        `;
    }
}
</script>
@endpush
@endsection