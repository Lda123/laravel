@extends('layouts.warga')

@section('title', 'Lokasi - DengueCare')

@section('custom-css')
<style>
    .input-focus-effect:focus {
        box-shadow: 0 0 0 3px rgba(34, 107, 210, 0.3);
        border-color: #226BD2;
    }
    
    select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
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
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Map Styles */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .info-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Peta Lokasi dan Pemantauan DBD</h1>
    
    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 animate-fade-in">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Cari Wilayah</h2>
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf
            <!-- Kecamatan -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatan_options as $kecamatan)
                        <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Kelurahan -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium">Kelurahan</label>
                <select id="kelurahan" name="kelurahan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                </select>
                <div id="kelurahan-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat kelurahan...</span>
                </div>
            </div>
            
            <!-- RW -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium">RW</label>
                <select id="rw" name="rw" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih Kelurahan terlebih dahulu</option>
                </select>
                <div id="rw-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat RW...</span>
                </div>
            </div>
            
            <!-- RT -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium">RT</label>
                <select id="rt" name="rt" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none dropdown-disabled" disabled>
                    <option value="">Pilih RW terlebih dahulu</option>
                </select>
                <div id="rt-loading" class="hidden mt-2">
                    <span class="spinner"></span> <span class="text-sm text-gray-600">Memuat RT...</span>
                </div>
            </div>
            
            <!-- Button Cari -->
            <div class="flex items-end">
                <button type="button" id="btnCari" class="input-focus-effect w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                    Cari Lokasi
                </button>
            </div>
        </form>
        
        <!-- Search Result -->
        <div id="search-result" class="mt-4 hidden">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <span id="search-message"></span>
            </div>
        </div>
    </div>

    <!-- Peta dan Statistik -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Kolom Peta -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6 animate-fade-in">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Peta Wilayah Surabaya</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Status Peta:</span>
                        <span id="map-status" class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Siap</span>
                    </div>
                </div>
                <div id="map"></div>
            </div>
        </div>
        
        <!-- Kolom Informasi -->
        <div class="space-y-6">
            <!-- Statistik Wilayah -->
            <div class="info-panel animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Wilayah</h3>
                <div class="space-y-4">
                    <div class="stat-card bg-gradient-to-r from-green-400 to-green-600">
                        <div class="stat-number" id="stat-aman">{{ $stats->aman ?? 0 }}</div>
                        <div class="stat-label">Wilayah Aman</div>
                    </div>
                    <div class="stat-card bg-gradient-to-r from-yellow-400 to-orange-500">
                        <div class="stat-number" id="stat-waspada">{{ $stats->tidak_aman ?? 0 }}</div>
                        <div class="stat-label">Wilayah Waspada</div>
                    </div>
                    <div class="stat-card bg-gradient-to-r from-red-400 to-red-600">
                        <div class="stat-number" id="stat-bahaya">{{ $stats->belum_dicek ?? 0 }}</div>
                        <div class="stat-label">Belum Dicek</div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Lokasi Terpilih -->
            <div class="info-panel animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Lokasi</h3>
                <div id="location-info">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-map-marker-alt text-3xl mb-2"></i>
                        <p>Pilih wilayah untuk melihat informasi detail</p>
                    </div>
                </div>
            </div>
            
            <!-- Legenda -->
            <div class="info-panel animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
                <div class="space-y-2">
                    <div class="legend-item">
                        <div class="legend-color bg-green-500"></div>
                        <span class="text-sm">Aman (0-5 kasus)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-yellow-500"></div>
                        <span class="text-sm">Waspada (6-15 kasus)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-red-500"></div>
                        <span class="text-sm">Bahaya (>15 kasus)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-gray-300"></div>
                        <span class="text-sm">Belum ada data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet CSS dan JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Global variables
let map;
let userMarker;
let markersLayer;

$(document).ready(function() {
    // Initialize map setelah DOM ready
    setTimeout(initializeMap, 100);
    
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

    // Kecamatan change handler
    $('#kecamatan').change(function() {
        const kecamatan_id = $(this).val();
        resetDependentDropdowns('kecamatan');

        if (kecamatan_id) {
            toggleDropdown('kelurahan', false, 'kelurahan-loading');

            $.ajax({
                url: "{{ route('lokasi.kelurahan') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

    // Kelurahan change handler
    $('#kelurahan').change(function() {
        const kelurahan_id = $(this).val();
        resetDependentDropdowns('kelurahan');

        if (kelurahan_id) {
            toggleDropdown('rw', false, 'rw-loading');

            $.ajax({
                url: "{{ route('lokasi.rw') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

    // RW change handler
    $('#rw').change(function() {
        const rw_id = $(this).val();
        resetDependentDropdowns('rw');

        if (rw_id) {
            toggleDropdown('rt', false, 'rt-loading');

            $.ajax({
                url: "{{ route('lokasi.rt') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

        // Show search result
        $('#search-result').removeClass('hidden');
        $('#search-message').text('Mencari lokasi di peta...');

        // Update map dengan data pencarian
        loadMapData({
            kecamatan: kecamatan_id,
            kelurahan: kelurahan_id,
            rw: rw_id,
            rt: rt_id
        });

        console.log('Search Data:', {
            kecamatan_id, kelurahan_id, rw_id, rt_id
        });
    });

    // Initialize button state
    updateSearchButton();
});

// Map Functions
function initializeMap() {
    // Initialize map centered on Surabaya
    map = L.map('map').setView([-7.2575, 112.7521], 11);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Buat layer group untuk markers
    markersLayer = L.layerGroup().addTo(map);

    // Load initial map data dari server
    loadMapData();
    
    console.log('Peta berhasil diinisialisasi');
}

function loadMapData(filters = {}) {
    $('#map-status').removeClass('bg-blue-100 text-blue-800 bg-red-100 text-red-800')
                    .addClass('bg-yellow-100 text-yellow-800')
                    .text('Memuat...');

    // AJAX call ke server untuk mendapatkan data real
    $.ajax({
        url: "{{ route('lokasi.map-data') }}", // Pastikan route ini ada
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: filters,
        success: function(response) {
            if (response.success) {
                // Clear existing markers
                markersLayer.clearLayers();
                
                // Add markers dari data real
                response.data.markers.forEach(marker => {
                    addRealAreaMarker(marker);
                });

                // Update map center dan zoom
                if (response.data.center) {
                    map.setView([response.data.center.lat, response.data.center.lng], response.data.center.zoom || 12);
                }

                // Update statistics dengan data real
                updateRealStatistics();
                
                $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                              .addClass('bg-green-100 text-green-800')
                              .text('Aktif');
                
                $('#search-message').text('Data peta berhasil dimuat!');
            } else {
                console.error('Error loading map data:', response.message);
                $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                              .addClass('bg-red-100 text-red-800')
                              .text('Error');
                
                $('#search-message').text('Gagal memuat data peta: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                          .addClass('bg-red-100 text-red-800')
                          .text('Error');
            
            $('#search-message').text('Terjadi kesalahan saat memuat data peta');
        }
    });
}

function addSampleData() {
    // Clear existing markers
    markersLayer.clearLayers();
    
    // Sample data untuk demonstration
    const sampleAreas = [
        {
            name: "Kecamatan Sukolilo",
            lat: -7.2885,
            lng: 112.8007,
            level: "aman",
            cases: 3,
            population: 15000
        },
        {
            name: "Kecamatan Gubeng", 
            lat: -7.2772,
            lng: 112.7478,
            level: "waspada",
            cases: 12,
            population: 20000
        },
        {
            name: "Kecamatan Wonokromo",
            lat: -7.3089,
            lng: 112.7375,
            level: "bahaya",
            cases: 25,
            population: 18000
        }
    ];

    sampleAreas.forEach(area => {
        addAreaMarker(area);
    });

    // Update statistics
    updateStatistics({
        aman: 1,
        waspada: 1,
        bahaya: 1
    });
}

function addRealAreaMarker(markerData) {
    const color = getColorByStatus(markerData.status);
    
    const marker = L.marker([markerData.lat, markerData.lng], {
        icon: L.divIcon({
            html: `<div class="w-6 h-6 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold" style="background-color: ${color};">${markerData.cases || 0}</div>`,
            className: 'custom-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        })
    });

    // Menggunakan popup content yang sudah digenerate dari server
    marker.bindPopup(markerData.popup_content);

    marker.on('click', function() {
        updateRealLocationInfo(markerData);
    });

    markersLayer.addLayer(marker);
}

function getColorByStatus(status) {
    switch(status) {
        case 'aman': return '#10b981';        // Green
        case 'tidak_aman': return '#f59e0b';  // Orange  
        case 'waspada': return '#eab308';     // Yellow
        case 'bahaya': return '#ef4444';      // Red
        case 'belum_dicek':
        default: return '#6b7280';            // Gray
    }
}

function getStatusText(status) {
    switch(status) {
        case 'aman': return 'Aman';
        case 'tidak_aman': return 'Tidak Aman';
        case 'waspada': return 'Waspada';
        case 'bahaya': return 'Bahaya';
        case 'belum_dicek':
        default: return 'Belum Dicek';
    }
}

function getColorByLevel(level) {
    switch(level) {
        case 'aman': return '#10b981';
        case 'waspada': return '#f59e0b';
        case 'bahaya': return '#ef4444';
        default: return '#6b7280';
    }
}

function updateRealStatistics() {
    // AJAX call untuk mendapatkan statistik real
    $.ajax({
        url: "{{ route('lokasi.statistics') }}", // Route baru untuk statistik
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#stat-aman').text(response.data.aman || 0);
                $('#stat-waspada').text(response.data.tidak_aman || 0);
                $('#stat-bahaya').text(response.data.belum_dicek || 0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading statistics:', error);
        }
    });
}

function updateRealLocationInfo(markerData) {
    const statusColor = getColorByStatus(markerData.status);
    const statusText = getStatusText(markerData.status);
    
    const infoHtml = `
        <div class="space-y-3">
            <div class="border-b pb-2">
                <h4 class="font-semibold text-gray-800">${markerData.title}</h4>
                <p class="text-sm text-gray-600">${markerData.subtitle || ''}</p>
                <p class="text-xs text-gray-500">${markerData.kecamatan || ''}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium ml-1" style="color: ${statusColor}">${statusText}</span>
                </div>
                <div>
                    <span class="text-gray-600">Kasus Tidak Aman:</span>
                    <span class="font-medium ml-1 text-red-600">${markerData.cases || 0}</span>
                </div>
                <div>
                    <span class="text-gray-600">Total Warga:</span>
                    <span class="font-medium ml-1">${markerData.population || 0}</span>
                </div>
                <div>
                    <span class="text-gray-600">Tipe:</span>
                    <span class="font-medium ml-1">${markerData.type === 'rt_selected' ? 'RT Terpilih' : 'RT Umum'}</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t">
                <div class="text-xs text-gray-500">
                    Koordinat: ${markerData.lat.toFixed(6)}, ${markerData.lng.toFixed(6)}
                </div>
            </div>
        </div>
    `;
    
    $('#location-info').html(infoHtml);
}

$('#kecamatan, #kelurahan, #rw, #rt').change(function() {
    // Jika ada perubahan pada dropdown, update peta secara otomatis
    const kecamatan_id = $('#kecamatan').val();
    const kelurahan_id = $('#kelurahan').val();
    const rw_id = $('#rw').val();
    const rt_id = $('#rt').val();
    
    // Hanya update peta jika minimal kecamatan dipilih
    if (kecamatan_id) {
        loadMapData({
            kecamatan: kecamatan_id,
            kelurahan: kelurahan_id,
            rw: rw_id,
            rt: rt_id
        });
    }
});

// Update button cari untuk menggunakan data real
$('#btnCari').click(function() {
    const kecamatan_id = $('#kecamatan').val();
    const kelurahan_id = $('#kelurahan').val();
    const rw_id = $('#rw').val();
    const rt_id = $('#rt').val();

    if (!kecamatan_id) {
        alert('Pilih minimal Kecamatan untuk pencarian');
        return;
    }

    // Show search result
    $('#search-result').removeClass('hidden');
    $('#search-message').text('Mencari lokasi di peta...');

    // Update map dengan data pencarian real
    loadMapData({
        kecamatan: kecamatan_id,
        kelurahan: kelurahan_id,
        rw: rw_id,
        rt: rt_id
    });

    console.log('Search Data:', {
        kecamatan_id, kelurahan_id, rw_id, rt_id
    });
});

</script>
@endpush