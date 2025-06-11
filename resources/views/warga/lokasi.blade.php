@extends('layouts.warga')

@section('title', 'Lokasi - DengueCare')

@section('custom-css')
<style>
    .wilayah-icon {
        z-index: 1000;
    }
    #map {
        height: 500px;
        width: 100%;
        border-radius: 0.5rem;
        z-index: 0;
    }
    .location-card {
        transition: all 0.3s ease;
    }
    .location-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .risk-high { background-color: #fecaca; }
    .risk-medium { background-color: #fed7aa; }
    .risk-low { background-color: #bbf7d0; }
    select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
    }
    .input-focus-effect:focus {
        box-shadow: 0 0 0 3px rgba(34, 107, 210, 0.3);
        border-color: #226BD2;
    }
    .leaflet-popup-content {
        width: 250px !important;
    }
    .leaflet-popup-content h3 {
        font-weight: bold;
        margin-bottom: 5px;
        color: #1e40af;
    }
    .leaflet-popup-content .status-aman {
        color: #16a34a;
        font-weight: bold;
    }
    .leaflet-popup-content .status-tidak-aman {
        color: #dc2626;
        font-weight: bold;
    }
    .leaflet-popup-content .status-dbd {
        color: #b91c1c;
        font-weight: bold;
    }
    .dropdown-disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('header-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Peta Lokasi dan Pemantauan DBD</h1>
    
    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 animate-fade-in">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Cari Wilayah</h2>
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-gray-700 mb-2 font-medium">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatan_options as $kecamatan)
                        <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2 font-medium">Kelurahan</label>
                <select id="kelurahan" name="kelurahan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                </select>
                <div id="kelurahan-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat kelurahan...</span>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 mb-2 font-medium">RW</label>
                <select id="rw" name="rw" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih Kelurahan terlebih dahulu</option>
                </select>
                <div id="rw-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat RW...</span>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 mb-2 font-medium">RT</label>
                <select id="rt" name="rt" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih RW terlebih dahulu</option>
                </select>
                <div id="rt-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat RT...</span>
                </div>
            </div>
            <div class="flex items-end">
                <button type="button" id="btnCari" class="input-focus-effect w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                    Cari Lokasi
                </button>
            </div>
        </form>
        <div id="search-result" class="mt-4 hidden">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <span id="search-message"></span>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 animate-fade-in">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Peta Pemantauan DBD Surabaya</h2>
            <div class="flex gap-2">
                <button id="btnResetMap" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300">
                    Reset Peta
                </button>
                <button id="btnUserLocation" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300">
                    Lokasi Saya
                </button>
            </div>
        </div>
        <div id="map"></div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-600 rounded-full mr-2 border-2 border-white shadow"></div>
                <span>Lokasi Anda</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-600 rounded-full mr-2 border-2 border-white shadow"></div>
                <span>Rumah Aman</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-600 rounded-full mr-2 border-2 border-white shadow"></div>
                <span>Rumah Tidak Aman</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 border-2 border-orange-500 rounded-full mr-2"></div>
                <span>Area Rawan DBD</span>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Status Rumah -->
        <div class="bg-white rounded-xl shadow-md p-6 card-hover animate-slide-in">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Status Rumah</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-green-100 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-green-800">{{ $stats->aman ?? 0 }}</p>
                    <p class="text-gray-600">Aman</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-red-800">{{ $stats->tidak_aman ?? 0 }}</p>
                    <p class="text-gray-600">Tidak Aman</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats->belum_dicek ?? 0 }}</p>
                    <p class="text-gray-600">Belum Dicek</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-600">* Data berdasarkan hasil pemantauan terakhir</p>
            </div>
        </div>

        <!-- Grafik Kasus -->
        <div class="bg-white rounded-xl shadow-md p-6 card-hover animate-slide-in">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Grafik Kasus DBD</h2>
                <select id="period-select" class="input-focus-effect px-4 pr-8 py-1.5 border rounded-lg transition-all duration-300 focus:outline-none">
                    <option value="harian" {{ $period == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $period == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $period == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <canvas id="caseChart" height="200"></canvas>
        </div>
    </div>

    <!-- Daerah Rawan Section -->
    <div class="bg-white rounded-xl shadow-md p-6 animate-fade-in">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Wilayah dengan Potensi DBD Tinggi</h2>
        @if(count($rawan_areas) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($rawan_areas as $area)
                <div class="location-card p-4 rounded-lg border cursor-pointer
                    {{ $area->rumah_tidak_aman > 5 ? 'risk-high' : 
                       ($area->rumah_tidak_aman > 2 ? 'risk-medium' : 'risk-low') }}" 
                     onclick="focusAreaOnMap('{{ $area->koordinat_lat }}', '{{ $area->koordinat_lng }}', '{{ $area->wilayah }}')">
                    <h3 class="font-bold text-lg mb-2">{{ $area->wilayah }}</h3>
                    <p class="text-sm mb-2">Status: 
                        <span class="
                            {{ $area->rumah_tidak_aman > 5 ? 'text-red-600 font-bold' : 
                               ($area->rumah_tidak_aman > 2 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold') }}">
                            {{ $area->rumah_tidak_aman > 5 ? 'Rawan Tinggi' : 
                               ($area->rumah_tidak_aman > 2 ? 'Rawan Sedang' : 'Aman') }}
                        </span>
                    </p>
                    <p class="text-sm">Rumah Tidak Aman: <strong>{{ $area->rumah_tidak_aman }}</strong></p>
                    <p class="text-sm mt-1">Total Rumah: {{ $area->total_rumah }}</p>
                    <p class="text-xs text-gray-500 mt-2">Klik untuk lihat di peta</p>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Belum ada data wilayah rawan DBD</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Global variables
window.appData = {
        userLocation: {
            lat: <?php echo isset($user_location['lat']) ? $user_location['lat'] : -7.2575; ?>,
            lng: <?php echo isset($user_location['lng']) ? $user_location['lng'] : 112.7521; ?>,
            title: <?php echo json_encode(isset($user_location['title']) ? $user_location['title'] : 'Lokasi Anda'); ?>,
            kecamatan: <?php echo json_encode(isset($user_location['kecamatan']) ? $user_location['kecamatan'] : ''); ?>,
            kelurahan: <?php echo json_encode(isset($user_location['kelurahan']) ? $user_location['kelurahan'] : ''); ?>
        },
        trackingData: <?php echo json_encode(isset($tracking_data) ? $tracking_data : []); ?>,
        caseData: <?php echo json_encode(isset($case_data) ? $case_data : []); ?>
    };
// Get data from window.appData
const trackingData = window.appData.trackingData;
const rawanAreas = window.appData.rawanAreas;
const userLocation = window.appData.userLocation;
const csrfToken = window.appData.csrfToken;
const allRtCoordinates = window.appData.allRtCoordinates;

// Initialize Chart
const ctx = document.getElementById('caseChart').getContext('2d');
let caseChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: window.appData.caseData.labels,
        datasets: [{
            label: 'Kasus DBD',
            data: window.appData.caseData.values,
            backgroundColor: '#3B82F6',
            borderColor: '#1D4ED8',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Kasus' }
            },
            x: {
                title: { display: true, text: 'Periode' }
            }
        }
    }
});

// Initialize Map with Leaflet
let map;
let wilayahMarker = null;
let userMarker = null;
let trackingMarkers = [];
let areaCircles = [];

function initializeMap() {
    // Initialize map centered on Surabaya
    map = L.map('map').setView([
        window.appData.defaultCoordinates.lat, 
        window.appData.defaultCoordinates.lng
    ], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Set map bounds based on all RT coordinates if available
    if (allRtCoordinates && allRtCoordinates.length > 0) {
        const bounds = L.latLngBounds(allRtCoordinates);
        map.fitBounds(bounds, { padding: [20, 20] });
    }

    // Add markers
    addUserMarker();
    addTrackingMarkers();
    addRawanAreaCircles();
}

function addUserMarker() {
    if (userLocation.lat && userLocation.lng) {
        const userIcon = L.divIcon({
            className: 'user-icon',
            html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
            iconSize: [26, 26]
        });

        userMarker = L.marker([userLocation.lat, userLocation.lng], {
            icon: userIcon,
            title: userLocation.title
        }).addTo(map).bindPopup(`
            <div style="width: 250px;">
                <h3 style="color: #3B82F6; margin-bottom: 8px;">${userLocation.title}</h3>
                <p><strong>Wilayah Anda:</strong></p>
                <p>RT ${userLocation.rt}/RW ${userLocation.rw}</p>
                <p>${userLocation.kelurahan}, ${userLocation.kecamatan}</p>
            </div>
        `);
    }
}

function addTrackingMarkers() {
    // Clear existing markers
    trackingMarkers.forEach(marker => map.removeLayer(marker));
    trackingMarkers = [];

    const safeIcon = L.divIcon({
        className: 'safe-icon',
        html: '<div style="background-color: #16a34a; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 3px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20]
    });

    const dangerIcon = L.divIcon({
        className: 'danger-icon',
        html: '<div style="background-color: #dc2626; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 3px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20]
    });

    if (trackingData && trackingData.length > 0) {
        trackingData.forEach(data => {
            let icon;
            let statusText = '';
            let additionalInfo = '';
            
            if (data.kategori_masalah === 'Tidak Aman') {
                icon = dangerIcon;
                statusText = '<span class="status-tidak-aman">TIDAK AMAN</span>';
                additionalInfo = `<p><strong>Masalah:</strong> ${data.deskripsi || 'Lingkungan kotor'}</p>`;
            } else {
                icon = safeIcon;
                statusText = '<span class="status-aman">AMAN</span>';
                additionalInfo = '<p>Tidak ada masalah yang dilaporkan</p>';
            }
            
            const marker = L.marker([data.lat, data.lng], {
                icon: icon
            }).addTo(map).bindPopup(`
                <div style="width: 250px;">
                    <h3>${data.nama_warga}</h3>
                    <p><strong>Status:</strong> ${statusText}</p>
                    <p><strong>Wilayah:</strong> RT ${data.rt}/RW ${data.rw}</p>
                    <p><strong>Kelurahan:</strong> ${data.kelurahan}</p>
                    <p><strong>Kecamatan:</strong> ${data.kecamatan}</p>
                    ${additionalInfo}
                    <p><strong>Terakhir Dipantau:</strong> ${new Date(data.tanggal).toLocaleDateString('id-ID')}</p>
                </div>
            `);
            
            trackingMarkers.push(marker);
        });
    }
}

function addRawanAreaCircles() {
    // Clear existing circles
    areaCircles.forEach(circle => map.removeLayer(circle));
    areaCircles = [];

    if (rawanAreas && rawanAreas.length > 0) {
        rawanAreas.forEach(area => {
            if (area.koordinat_lat && area.koordinat_lng) {
                let color, fillColor, radius;
                
                if (area.rumah_tidak_aman > 5) {
                    color = '#DC2626';
                    fillColor = '#FEE2E2';
                    radius = 300;
                } else if (area.rumah_tidak_aman > 2) {
                    color = '#F59E0B';
                    fillColor = '#FEF3C7';
                    radius = 200;
                } else {
                    color = '#16A34A';
                    fillColor = '#DCFCE7';
                    radius = 150;
                }
                
                const circle = L.circle([area.koordinat_lat, area.koordinat_lng], {
                    color: color,
                    fillColor: fillColor,
                    fillOpacity: 0.4,
                    radius: radius,
                    weight: 2
                }).addTo(map).bindPopup(`
                    <div style="width: 250px;">
                        <h3>${area.wilayah}</h3>
                        <p><strong>Status:</strong> 
                            <span style="color: ${color}; font-weight: bold;">
                                ${area.rumah_tidak_aman > 5 ? 'Rawan Tinggi' : 
                                 area.rumah_tidak_aman > 2 ? 'Rawan Sedang' : 'Aman'}
                            </span>
                        </p>
                        <p><strong>Rumah Tidak Aman:</strong> ${area.rumah_tidak_aman}</p>
                        <p><strong>Total Rumah:</strong> ${area.total_rumah}</p>
                        <p style="font-size: 12px; color: #666; margin-top: 8px;">
                            Persentase: ${((area.rumah_tidak_aman / area.total_rumah) * 100).toFixed(1)}%
                        </p>
                    </div>
                `);
                
                areaCircles.push(circle);
            }
        });
    }
}

function focusAreaOnMap(lat, lng, nama) {
    if (map) {
        map.setView([lat, lng], 16);
        // Find and open popup for this area
        areaCircles.forEach(circle => {
            const circleLatLng = circle.getLatLng();
            if (Math.abs(circleLatLng.lat - lat) < 0.001 && Math.abs(circleLatLng.lng - lng) < 0.001) {
                circle.openPopup();
            }
        });
    }
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    
    // Map control buttons
    document.getElementById('btnResetMap').addEventListener('click', function() {
        if (allRtCoordinates && allRtCoordinates.length > 0) {
            const bounds = L.latLngBounds(allRtCoordinates);
            map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            map.setView([window.appData.defaultCoordinates.lat, window.appData.defaultCoordinates.lng], 12);
        }
    });
    
    document.getElementById('btnUserLocation').addEventListener('click', function() {
        if (userLocation.lat && userLocation.lng) {
            map.setView([userLocation.lat, userLocation.lng], 16);
            if (userMarker) {
                userMarker.openPopup();
            }
        }
    });
});

// Period select change
document.getElementById('period-select').addEventListener('change', function() {
    const period = this.value;
    
    // Update chart data via AJAX
    fetch('{{ route("warga.lokasi.update-period") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ period: period })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            caseChart.data.labels = data.data.map(item => item.label);
            caseChart.data.datasets[0].data = data.data.map(item => item.value);
            caseChart.update();
        }
    })
    .catch(error => {
        console.error('Error updating chart:', error);
    });
});

$(document).ready(function() {
    // CSRF Token
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Helper function to enable/disable dropdown
    function toggleDropdown(elementId, enabled, loadingId = null) {
        const element = $('#' + elementId);
        const loading = loadingId ? $('#' + loadingId) : null;

        if (enabled) {
            element.prop('disabled', false).removeClass('dropdown-disabled');
            if (loading) loading.addClass('hidden');
        } else {
            element.prop('disabled', true).addClass('dropdown-disabled');
            if (loading) loading.removeClass('hidden');
        }
    }

    // Update button state
    function updateSearchButton() {
        const hasKecamatan = $('#kecamatan').val();
        $('#btnCari').prop('disabled', !hasKecamatan);
    }

    // Reset dependent dropdowns
    function resetDependentDropdowns(startFrom) {
        if (startFrom === 'kecamatan') {
            $('#kelurahan').html('<option value="">Pilih Kelurahan</option>');
            $('#rw').html('<option value="">Pilih RW</option>');
            $('#rt').html('<option value="">Pilih RT</option>');
            toggleDropdown('kelurahan', false);
            toggleDropdown('rw', false);
            toggleDropdown('rt', false);
        } else if (startFrom === 'kelurahan') {
            $('#rw').html('<option value="">Pilih RW</option>');
            $('#rt').html('<option value="">Pilih RT</option>');
            toggleDropdown('rw', false);
            toggleDropdown('rt', false);
        } else if (startFrom === 'rw') {
            $('#rt').html('<option value="">Pilih RT</option>');
            toggleDropdown('rt', false);
        }
        updateSearchButton();
    }

    // Kecamatan change
    $('#kecamatan').change(function() {
        const kecamatan_id = $(this).val();
        resetDependentDropdowns('kecamatan');

        if (kecamatan_id) {
            toggleDropdown('kelurahan', false, 'kelurahan-loading');

            $.ajax({
                url: '{{ route("warga.lokasi.kelurahan") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    kecamatan_id: kecamatan_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#kelurahan').html(data.options);
                        toggleDropdown('kelurahan', true, 'kelurahan-loading');
                    } else {
                        $('#kelurahan').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('kelurahan', false, 'kelurahan-loading');
                    }
                    updateSearchButton();
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#kelurahan').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('kelurahan', false, 'kelurahan-loading');
                    updateSearchButton();
                }
            });
        }
    });

    // Kelurahan change
    $('#kelurahan').change(function() {
        const kelurahan_id = $(this).val();
        resetDependentDropdowns('kelurahan');

        if (kelurahan_id) {
            toggleDropdown('rw', false, 'rw-loading');

            $.ajax({
                url: '{{ route("warga.lokasi.rw") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    kelurahan_id: kelurahan_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#rw').html(data.options);
                        toggleDropdown('rw', true, 'rw-loading');
                    } else {
                        $('#rw').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('rw', false, 'rw-loading');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rw').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('rw', false, 'rw-loading');
                }
            });
        }
    });

    // RW change
    $('#rw').change(function() {
        const rw_id = $(this).val();
        resetDependentDropdowns('rw');

        if (rw_id) {
            toggleDropdown('rt', false, 'rt-loading');

            $.ajax({
                url: '{{ route("warga.lokasi.rt") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    rw_id: rw_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#rt').html(data.options);
                        toggleDropdown('rt', true, 'rt-loading');
                    } else {
                        $('#rt').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('rt', false, 'rt-loading');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rt').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('rt', false, 'rt-loading');
                }
            });
        }
    });

    // Button Cari click handler
    $('#btnCari').click(function() {
        const kecamatan_id = $('#kecamatan').val();
        const kelurahan_id = $('#kelurahan').val();
        const rw_id = $('#rw').val();
        const rt_id = $('#rt').val();

        $.ajax({
            url: '{{ route("warga.lokasi.wilayah-koordinat") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                kecamatan_id: kecamatan_id,
                kelurahan_id: kelurahan_id,
                rw_id: rw_id,
                rt_id: rt_id
            },
            success: function(data) {
                if (data.success) {
                    // Remove existing wilayah marker
                    if (window.wilayahMarker) {
                        window.map.removeLayer(window.wilayahMarker);
                    }

                    // Add new marker
                    const markerIcon = L.divIcon({
                        className: 'wilayah-icon',
                        html: '<div style="background-color: #8B5CF6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
                        iconSize: [26, 26]
                    });

                    window.wilayahMarker = L.marker([data.lat, data.lng], {
                        icon: markerIcon
                    }).addTo(window.map)
                    .bindPopup(`<b>${data.nama_wilayah}</b>`)
                    .openPopup();

                    // Zoom to the marker
                    window.map.setView([data.lat, data.lng], 16);
                } else {
                    alert(data.message || 'Gagal menemukan koordinat wilayah');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Terjadi kesalahan saat memproses permintaan');
            }
        });
    });

    // Initialize button state
    updateSearchButton();
});
</script>
@endsection