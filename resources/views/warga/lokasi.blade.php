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
    .animate-slide-in {
        animation: slideIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideIn {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .btn-hover {
        transition: all 0.3s ease;
    }
    .btn-hover:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
        </div>
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
                <span>Area Aman</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-600 rounded-full mr-2 border-2 border-white shadow"></div>
                <span>Area Rawan</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-600 rounded-full mr-2 border-2 border-white shadow"></div>
                <span>Belum Ada Data</span>
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
                <h2 class="text-xl font-semibold text-gray-800">Grafik Status Rumah</h2>
                <select id="period-select" class="input-focus-effect px-4 pr-8 py-1.5 border rounded-lg transition-all duration-300 focus:outline-none">
                    <option value="harian">Harian</option>
                    <option value="mingguan">Mingguan</option>
                    <option value="bulanan" selected>Bulanan</option>
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

<script>
   // Initialize variables
let map;
let markers = [];
let userLocationMarker = null;
let caseChart = null;

// Initialize when document is ready
$(document).ready(function() {
    initializeMap();
    initializeChart();
    initializeEventHandlers();
    loadMapData();
});

function initializeMap() {
    // Initialize map centered on Surabaya
    map = L.map('map').setView([-7.2575, 112.7521], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
}

function initializeChart() {
    const ctx = document.getElementById('caseChart').getContext('2d');
    const chartData = JSON.parse(document.getElementById('chart-data').value);
    
    // Initial chart data - will be updated via AJAX
    const initialData = {
        labels: chartData.labels,
        datasets: chartData.datasets
    };
    
    caseChart = new Chart(ctx, {
        type: 'bar',
        data: initialData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function initializeEventHandlers() {
    // Kecamatan change handler
    $('#kecamatan').change(function() {
        var kecamatan_id = $(this).val();
        
        if(kecamatan_id) {
            // Show loading spinner
            showLoading('kelurahan');
            
            $.ajax({
                url: "{{ route('lokasi.get-kelurahan') }}", // Sesuaikan dengan route Anda
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    kecamatan_id: kecamatan_id
                },
                success: function(data) {
                    hideLoading('kelurahan');
                    $('#kelurahan').html(data)
                                   .prop('disabled', false)
                                   .removeClass('dropdown-disabled');
                    
                    // Reset dependent dropdowns
                    resetDropdowns(['rw', 'rt']);
                    updateSearchButton();
                },
                error: function() {
                    hideLoading('kelurahan');
                    alert('Gagal memuat data kelurahan');
                    resetDropdowns(['kelurahan', 'rw', 'rt']);
                }
            });
        } else {
            resetDropdowns(['kelurahan', 'rw', 'rt']);
        }
    });

    // Kelurahan change handler
    $('#kelurahan').change(function() {
        var kelurahan_id = $(this).val();
        
        if(kelurahan_id) {
            // Show loading spinner
            showLoading('rw');
            
            $.ajax({
                url: "{{ route('lokasi.get-rw') }}", // Sesuaikan dengan route Anda
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    kelurahan_id: kelurahan_id
                },
                success: function(data) {
                    hideLoading('rw');
                    $('#rw').html(data)
                            .prop('disabled', false)
                            .removeClass('dropdown-disabled');
                    
                    // Reset dependent dropdown
                    resetDropdowns(['rt']);
                    updateSearchButton();
                },
                error: function() {
                    hideLoading('rw');
                    alert('Gagal memuat data RW');
                    resetDropdowns(['rw', 'rt']);
                }
            });
        } else {
            resetDropdowns(['rw', 'rt']);
        }
    });

    // RW change handler
    $('#rw').change(function() {
        var rw_id = $(this).val();
        
        if(rw_id) {
            // Show loading spinner
            showLoading('rt');
            
            $.ajax({
                url: "{{ route('lokasi.get-rt') }}", // Sesuaikan dengan route Anda
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    rw_id: rw_id
                },
                success: function(data) {
                    hideLoading('rt');
                    $('#rt').html(data)
                           .prop('disabled', false)
                           .removeClass('dropdown-disabled');
                    
                    updateSearchButton();
                },
                error: function() {
                    hideLoading('rt');
                    alert('Gagal memuat data RT');
                    resetDropdowns(['rt']);
                }
            });
        } else {
            resetDropdowns(['rt']);
        }
    });

    // RT change handler (optional - for additional functionality)
    $('#rt').change(function() {
        updateSearchButton();
    });

    // Search button handler
    $('#btnCari').click(function() {
        if (!$(this).prop('disabled')) {
            performSearch();
        }
    });

    // Map control handlers
    $('#btnResetMap').click(function() {
        resetMap();
    });

    $('#btnUserLocation').click(function() {
        getUserLocation();
    });

    // Chart period change handler
    $('#period-select').change(function() {
        updateChart($(this).val());
    });
}

// Helper function to show loading spinner
function showLoading(elementId) {
    $('#' + elementId + '-loading').removeClass('hidden');
}

// Helper function to hide loading spinner
function hideLoading(elementId) {
    $('#' + elementId + '-loading').addClass('hidden');
}

// Helper function to reset dropdowns
function resetDropdowns(dropdownIds) {
    dropdownIds.forEach(function(id) {
        let defaultText = '';
        switch(id) {
            case 'kelurahan':
                defaultText = 'Pilih Kecamatan terlebih dahulu';
                break;
            case 'rw':
                defaultText = 'Pilih Kelurahan terlebih dahulu';
                break;
            case 'rt':
                defaultText = 'Pilih RW terlebih dahulu';
                break;
        }
        
        $('#' + id).html('<option value="">' + defaultText + '</option>')
                   .prop('disabled', true)
                   .addClass('dropdown-disabled');
    });
    
    updateSearchButton();
}

// Helper function to update search button state
function updateSearchButton() {
    const kecamatan = $('#kecamatan').val();
    const kelurahan = $('#kelurahan').val();
    
    // Enable search button if at least kecamatan and kelurahan are selected
    if (kecamatan && kelurahan) {
        $('#btnCari').prop('disabled', false).removeClass('cursor-not-allowed');
    } else {
        $('#btnCari').prop('disabled', true).addClass('cursor-not-allowed');
    }
}

// Function to perform search
function performSearch() {
    const kecamatan = $('#kecamatan').val();
    const kelurahan = $('#kelurahan').val();
    const rw = $('#rw').val();
    const rt = $('#rt').val();
    
    // Build search parameters
    let searchData = {
        _token: "{{ csrf_token() }}",
        kecamatan_id: kecamatan,
        kelurahan_id: kelurahan
    };
    
    if (rw) searchData.rw_id = rw;
    if (rt) searchData.rt_id = rt;
    
    // Perform search AJAX request
    $.ajax({
        url: "{{ route('lokasi.search') }}", // Sesuaikan dengan route search Anda
        type: "POST",
        data: searchData,
        success: function(response) {
            // Show search result
            showSearchResult(response.message, 'success');
            
            // Update map with search results
            if (response.coordinates) {
                focusAreaOnMap(response.coordinates.lat, response.coordinates.lng, response.area_name);
            }
            
            // Update markers if provided
            if (response.markers) {
                updateMapMarkers(response.markers);
            }
        },
        error: function() {
            showSearchResult('Gagal melakukan pencarian lokasi', 'error');
        }
    });
}

// Function to show search result message
function showSearchResult(message, type) {
    const resultDiv = $('#search-result');
    const messageSpan = $('#search-message');
    
    // Remove existing classes
    resultDiv.removeClass('hidden');
    resultDiv.find('div').removeClass('bg-green-100 border-green-400 text-green-700 bg-red-100 border-red-400 text-red-700');
    
    // Add appropriate classes based on type
    if (type === 'success') {
        resultDiv.find('div').addClass('bg-green-100 border-green-400 text-green-700');
    } else {
        resultDiv.find('div').addClass('bg-red-100 border-red-400 text-red-700');
    }
    
    messageSpan.text(message);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        resultDiv.addClass('hidden');
    }, 5000);
}

// Function to focus area on map
function focusAreaOnMap(lat, lng, areaName) {
    if (map && lat && lng) {
        map.setView([lat, lng], 15);
        
        // Add marker for the focused area
        const marker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup(areaName || 'Lokasi yang dicari')
            .openPopup();
        
        markers.push(marker);
    }
}

// Function to update map markers
function updateMapMarkers(newMarkers) {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Add new markers
    newMarkers.forEach(markerData => {
        const marker = L.marker([markerData.lat, markerData.lng])
            .addTo(map)
            .bindPopup(markerData.popup || '');
        
        markers.push(marker);
    });
}

// Function to reset map
function resetMap() {
    // Clear all markers except user location
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Reset map view to Surabaya
    map.setView([-7.2575, 112.7521], 12);
    
    // Hide search result
    $('#search-result').addClass('hidden');
}

// Function to get user location
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Remove existing user location marker
                if (userLocationMarker) {
                    map.removeLayer(userLocationMarker);
                }
                
                // Add user location marker
                userLocationMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'wilayah-icon',
                        html: '<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow"></div>',
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    })
                }).addTo(map).bindPopup('Lokasi Anda');
                
                // Center map on user location
                map.setView([lat, lng], 15);
            },
            function(error) {
                alert('Gagal mendapatkan lokasi Anda: ' + error.message);
            }
        );
    } else {
        alert('Geolocation tidak didukung oleh browser Anda');
    }
}

// Function to load initial map data
function loadMapData() {
    // Load initial map markers and data
    $.ajax({
        url: "{{ route('lokasi.map-data') }}", // Sesuaikan dengan route Anda
        type: "GET",
        success: function(response) {
            if (response.markers) {
                updateMapMarkers(response.markers);
            }
        },
        error: function() {
            console.log('Gagal memuat data peta');
        }
    });
}

// Function to update chart based on period
function updateChart(period) {
    $.ajax({
        url: "{{ route('lokasi.chart-data') }}", // Sesuaikan dengan route Anda
        type: "GET",
        data: { period: period },
        success: function(response) {
            if (caseChart && response.chartData) {
                caseChart.data.labels = response.chartData.labels;
                caseChart.data.datasets = response.chartData.datasets;
                caseChart.update();
            }
        },
        error: function() {
            console.log('Gagal memuat data chart');
        }
    });
}
</script>