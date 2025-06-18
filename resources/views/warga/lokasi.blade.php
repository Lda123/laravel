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
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-search text-blue-600 mr-2"></i>
        Cari Wilayah </h2>
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
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            
            <!-- Kolom Kiri: Peta dan Grafik (3/4 lebar) -->
            <div class="xl:col-span-3 space-y-8">
                
                <!-- Section Peta -->
                <div class="bg-white rounded-xl shadow-md p-6 animate-fade-in">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <i class="fas fa-map text-blue-600 mr-2"></i>
                            Peta Wilayah Surabaya
                        </h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Status Peta:</span>
                            <span id="map-status" class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Siap</span>
                        </div>
                    </div>
                    <div id="map">
                        <div class="flex items-center justify-center h-full text-center">
                            <div>
                                <i class="fas fa-map-marked-alt text-4xl mb-4 text-gray-400"></i>
                                <p class="text-gray-600">Peta Surabaya akan dimuat di sini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Grafik Peningkatan Kondisi - DIPERBAIKI -->
                <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                                Grafik Bulanan Kondisi Keamanan
                            </h2>
                            <p class="text-gray-600 text-sm">Monitoring kondisi keamanan wilayah per bulan (data bulan ini)</p>
                        </div>
                        
                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
                            <!-- Status Indicator -->
                            <div class="flex items-center space-x-2">
                                <span class="status-badge bg-green-100 text-green-800" id="chart-status">
                                    <i class="fas fa-check mr-1"></i>
                                    Aktif
                                </span>
                            </div>
                            
                            <!-- Period Info - DIHAPUS SELECTOR, HANYA INFO -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600 bg-blue-100 px-3 py-2 rounded-lg">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Bulan Ini: {{ \Carbon\Carbon::now()->format('F Y') }}
                                </span>
                            </div>
                            
                            <!-- Refresh Button -->
                            <button onclick="refreshMonthlyCharts()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Grafik Kasus Tidak Aman -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl border border-red-200 p-6 hover-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-red-800">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                Kasus Tidak Aman (Bulan Ini)
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-red-600 bg-red-200 px-2 py-1 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Monthly
                                </span>
                            </div>
                        </div>
                        <!-- Fixed chart container with proper height -->
                        <div class="chart-container" style="height: 250px; position: relative;">
                            <canvas id="tidakAmanChart"></canvas>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <p class="text-xs text-red-600 font-medium">Tertinggi</p>
                                <p class="text-lg font-bold text-red-800" id="max-tidak-aman">0</p>
                            </div>
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <p class="text-xs text-red-600 font-medium">Total Bulan Ini</p>
                                <p class="text-lg font-bold text-red-800" id="total-tidak-aman">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Kondisi Aman -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 p-6 hover-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-green-800">
                                <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                                Kondisi Aman (Bulan Ini)
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-green-600 bg-green-200 px-2 py-1 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Monthly
                                </span>
                            </div>
                        </div>
                        <!-- Fixed chart container with proper height -->
                        <div class="chart-container" style="height: 250px; position: relative;">
                            <canvas id="amanChart"></canvas>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <p class="text-xs text-green-600 font-medium">Tertinggi</p>
                                <p class="text-lg font-bold text-green-800" id="max-aman">0</p>
                            </div>
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <p class="text-xs text-green-600 font-medium">Total Bulan Ini</p>
                                <p class="text-lg font-bold text-green-800" id="total-aman">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Info Data Bulanan -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 mb-1">Informasi Data</h4>
                                <p class="text-xs text-blue-700">
                                    • Data yang ditampilkan adalah data bulan berjalan ({{ \Carbon\Carbon::now()->format('F Y') }})<br>
                                    • Data peta dan popup hanya menampilkan status terkini bulan ini<br>
                                    • Data lebih dari 1 bulan akan tersimpan untuk keperluan analisis historis<br>
                                    • Refresh otomatis setiap 5 menit untuk data real-time
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Informasi (1/4 lebar) -->
            <div class="xl:col-span-1 space-y-6">
                
                <!-- Statistik Wilayah -->
                <div class="info-panel animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                        Statistik Wilayah (Bulan Ini)
                    </h3>
                    <div class="space-y-4">
                        <div class="stat-card bg-gradient-to-r from-green-400 to-green-600">
                            <div class="stat-number" id="stat-aman">{{ $stats->aman ?? 0 }}</div>
                            <div class="stat-label">Wilayah Aman</div>
                        </div>
                        <div class="stat-card bg-gradient-to-r from-red-400 to-red-600">
                            <div class="stat-number" id="stat-waspada">{{ $stats->tidak_aman ?? 0 }}</div>
                            <div class="stat-label">Wilayah Tidak Aman</div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Lokasi Terpilih - DIPERBAIKI POPUP CONTENT -->
                <div class="info-panel animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Informasi Lokasi (Data Bulan Ini)
                    </h3>
                    <div id="location-info">
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-map-marker-alt text-3xl mb-2"></i>
                            <p class="text-sm">Pilih wilayah untuk melihat informasi detail bulan ini</p>
                        </div>
                    </div>
                </div>
                
                <!-- Legenda -->
                <div class="info-panel animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Keterangan Status
                    </h3>
                    <div class="space-y-2">
                        <div class="legend-item">
                            <div class="legend-color bg-green-500"></div>
                            <span class="text-sm">Aman (0-5 kasus/bulan)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-yellow-500"></div>
                            <span class="text-sm">Waspada (6-15 kasus/bulan)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-red-500"></div>
                            <span class="text-sm">Bahaya (>15 kasus/bulan)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-gray-300"></div>
                            <span class="text-sm">Belum ada data bulan ini</span>
                        </div>
                    </div>
                </div>

                <!-- Update Terakhir -->
                <div class="info-panel animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        Update Terakhir
                    </h3>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600" id="last-update-time">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}</p>
                        <p class="text-sm text-gray-600" id="last-update-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d F Y') }}</p>
                        <div class="mt-3 p-2 bg-green-100 rounded-lg">
                            <p class="text-xs text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Data bulan ini - Sistem berjalan normal
                            </p>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Global variables
let map, userMarker, markersLayer;
let tidakAmanChart, amanChart;

$(document).ready(function() {
    // Initialize map setelah DOM ready
    setTimeout(initializeMap, 100);
    setTimeout(initializeMonthlyCharts, 200);
    
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
        $('#search-message').text('Mencari data tracking 1 bulan terakhir...');

        // Update map dengan data pencarian tracking harian
        loadTrackingMapData({
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

function initializeMonthlyCharts() {
    // Destroy existing charts if they exist
    if (tidakAmanChart) {
        tidakAmanChart.destroy();
    }
    if (amanChart) {
        amanChart.destroy();
    }

    // Get current month data from server
    loadMonthlyChartData();
}

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

    // Load initial tracking data dari server (BULAN BERJALAN)
    loadTrackingMapData();
    
    console.log('Peta tracking bulan berjalan berhasil diinisialisasi');
}

// Load monthly chart data
function loadMonthlyChartData() {
    // Mendapatkan filter lokasi yang aktif
    const kecamatan_id = $('#kecamatan').val();
    const kelurahan_id = $('#kelurahan').val();
    const rw_id = $('#rw').val();
    const rt_id = $('#rt').val();

    const requestData = {
        month: new Date().getMonth() + 1,
        year: new Date().getFullYear()
    };

    // Tambahkan filter lokasi jika ada
    if (kecamatan_id) requestData.kecamatan_id = kecamatan_id;
    if (kelurahan_id) requestData.kelurahan_id = kelurahan_id;
    if (rw_id) requestData.rw_id = rw_id;
    if (rt_id) requestData.rt_id = rt_id;

    $.ajax({
        url: "{{ route('lokasi.monthly-chart-data') }}",
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: requestData,
        success: function(response) {
            console.log('Chart data received:', response); // Debug log
            
            if (response.success) {
                createTidakAmanChart(response.data.tidak_aman);
                createAmanChart(response.data.aman);
                updateChartStats(response.data.stats);
                
                // Update info period jika ada filter
                updateChartPeriodInfo(requestData);
            } else {
                console.error('Error loading chart data:', response.message);
                createDefaultCharts();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading chart data:', error);
            console.error('Response:', xhr.responseText); // Debug log
            createDefaultCharts();
        }
    });
}

function updateChartPeriodInfo(filters) {
    let locationInfo = '';
    
    if (filters.rt_id) {
        const rtText = $('#rt option:selected').text();
        const rwText = $('#rw option:selected').text();
        locationInfo = ` - ${rtText}, ${rwText}`;
    } else if (filters.rw_id) {
        const rwText = $('#rw option:selected').text();
        const kelurahanText = $('#kelurahan option:selected').text();
        locationInfo = ` - ${rwText}, ${kelurahanText}`;
    } else if (filters.kelurahan_id) {
        const kelurahanText = $('#kelurahan option:selected').text();
        locationInfo = ` - ${kelurahanText}`;
    } else if (filters.kecamatan_id) {
        const kecamatanText = $('#kecamatan option:selected').text();
        locationInfo = ` - ${kecamatanText}`;
    }

    // Update period info display
    const periodText = `Bulan Ini: {{ \Carbon\Carbon::now()->format('F Y') }}${locationInfo}`;
    $('.period-info').text(periodText);
}


// Create Tidak Aman Chart
function createTidakAmanChart(data) {
    const ctx = document.getElementById('tidakAmanChart').getContext('2d');
    
    // Pastikan data adalah array
    if (!Array.isArray(data)) {
        console.warn('Data tidak aman bukan array:', data);
        data = Array(new Date().getDate()).fill(0);
    }
    
    // Prepare labels untuk hari dalam bulan
    const daysInMonth = new Date().getDate();
    const labels = Array.from({length: daysInMonth}, (_, i) => `${i + 1}`);
    
    // Pastikan data memiliki panjang yang sama dengan labels
    const chartData = data.slice(0, daysInMonth);
    while (chartData.length < daysInMonth) {
        chartData.push(0);
    }
    
    // Destroy existing chart
    if (tidakAmanChart) {
        tidakAmanChart.destroy();
    }
    
    tidakAmanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kasus Tidak Aman',
                data: chartData,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ef4444',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#dc2626',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(239, 68, 68, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#991b1b',
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(239, 68, 68, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#991b1b',
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        maxTicksLimit: 10
                    },
                    title: {
                        display: true,
                        text: 'Tanggal (Bulan Ini)',
                        color: '#991b1b',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    border: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(239, 68, 68, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return `Tanggal ${context[0].label}`;
                        },
                        label: function(context) {
                            const value = context.parsed.y;
                            return value > 0 ? `${value} kasus tidak aman` : 'Tidak ada kasus';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    borderCapStyle: 'round',
                    borderJoinStyle: 'round'
                }
            }
        }
    });
    
    console.log('Tidak Aman Chart created with data:', chartData);
}

// Create Aman Chart - Enhanced
function createAmanChart(data) {
    const ctx = document.getElementById('amanChart').getContext('2d');
    
    // Pastikan data adalah array
    if (!Array.isArray(data)) {
        console.warn('Data aman bukan array:', data);
        data = Array(new Date().getDate()).fill(0);
    }
    
    // Prepare labels untuk hari dalam bulan
    const daysInMonth = new Date().getDate();
    const labels = Array.from({length: daysInMonth}, (_, i) => `${i + 1}`);
    
    // Pastikan data memiliki panjang yang sama dengan labels
    const chartData = data.slice(0, daysInMonth);
    while (chartData.length < daysInMonth) {
        chartData.push(0);
    }
    
    // Destroy existing chart
    if (amanChart) {
        amanChart.destroy();
    }
    
    amanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kondisi Aman',
                data: chartData,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#059669',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(16, 185, 129, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#065f46',
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(16, 185, 129, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#065f46',
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        maxTicksLimit: 10
                    },
                    title: {
                        display: true,
                        text: 'Tanggal (Bulan Ini)',
                        color: '#065f46',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    border: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(16, 185, 129, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return `Tanggal ${context[0].label}`;
                        },
                        label: function(context) {
                            const value = context.parsed.y;
                            return value > 0 ? `${value} kondisi aman` : 'Belum ada data';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    borderCapStyle: 'round',
                    borderJoinStyle: 'round'
                }
            }
        }
    });
    
    console.log('Aman Chart created with data:', chartData);
}

// Create default charts when no data available
function createDefaultCharts() {
    const daysInMonth = new Date().getDate();
    const defaultData = Array(daysInMonth).fill(0);
    
    createTidakAmanChart(defaultData);
    createAmanChart(defaultData);
    
    // Update stats with zero values
    updateChartStats({
        maxTidakAman: 0,
        totalTidakAman: 0,
        maxAman: 0,
        totalAman: 0
    });
}

// Update chart statistics
function updateChartStats(stats) {
    if (!stats) {
        stats = {
            maxTidakAman: 0,
            totalTidakAman: 0,
            maxAman: 0,
            totalAman: 0
        };
    }
    
    // Update dengan animasi
    animateNumber('#max-tidak-aman', stats.maxTidakAman || 0);
    animateNumber('#total-tidak-aman', stats.totalTidakAman || 0);
    animateNumber('#max-aman', stats.maxAman || 0);
    animateNumber('#total-aman', stats.totalAman || 0);
    
    console.log('Chart stats updated:', stats);
}

function animateNumber(selector, targetValue) {
    const element = $(selector);
    const currentValue = parseInt(element.text()) || 0;
    
    if (currentValue === targetValue) return;
    
    const increment = targetValue > currentValue ? 1 : -1;
    const duration = Math.abs(targetValue - currentValue) * 50; // 50ms per step
    const steps = Math.abs(targetValue - currentValue);
    const stepDuration = Math.min(duration / steps, 100);
    
    let current = currentValue;
    const timer = setInterval(() => {
        current += increment;
        element.text(current);
        
        if (current === targetValue) {
            clearInterval(timer);
        }
    }, stepDuration);
}

// Refresh monthly charts function
function refreshMonthlyCharts() {
    $('#chart-status').removeClass('bg-green-100 text-green-800')
                     .addClass('bg-yellow-100 text-yellow-800')
                     .html('<i class="fas fa-spinner fa-spin mr-1"></i>Memuat...');
    
    // Show loading pada chart containers
    $('.chart-container').addClass('opacity-50');
    
    // Reload chart data
    loadMonthlyChartData();
    
    // Reset status after loading
    setTimeout(() => {
        $('#chart-status').removeClass('bg-yellow-100 text-yellow-800')
                         .addClass('bg-green-100 text-green-800')
                         .html('<i class="fas fa-check mr-1"></i>Aktif');
        
        $('.chart-container').removeClass('opacity-50');
    }, 2000);
}

function loadTrackingMapData(filters = {}) {
    $('#map-status').removeClass('bg-blue-100 text-blue-800 bg-red-100 text-red-800')
                    .addClass('bg-yellow-100 text-yellow-800')
                    .text('Memuat data tracking...');

    // AJAX call ke server untuk mendapatkan data tracking harian BULAN BERJALAN
    $.ajax({
        url: "{{ route('lokasi.map-data') }}", 
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            ...filters,
            period: 'current_month', // UBAH: dari 'last_month' ke 'current_month'
            start_date: getCurrentMonthStart(), // UBAH: Tanggal 1 bulan ini
            end_date: getCurrentMonthEnd() // UBAH: Tanggal terakhir bulan ini
        },
        success: function(response) {
            if (response.success) {
                // Clear existing markers
                markersLayer.clearLayers();
                
                // Add markers dari data tracking harian
                response.data.markers.forEach(marker => {
                    addTrackingMarker(marker);
                });

                // Update map center dan zoom
                if (response.data.center) {
                    map.setView([response.data.center.lat, response.data.center.lng], response.data.center.zoom || 12);
                }

                // Update statistics dengan data tracking bulan berjalan
                updateTrackingStatistics(response.data.statistics);
                
                $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                              .addClass('bg-green-100 text-green-800')
                              .text('Data Tracking Aktif');
                
                const totalData = response.data.total_records || 0;
                const currentMonth = getCurrentMonthName(); // TAMBAH: Nama bulan saat ini
                $('#search-message').text(`Data tracking ${currentMonth} berhasil dimuat! (${totalData} record)`);
            } else {
                console.error('Error loading tracking data:', response.message);
                $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                              .addClass('bg-red-100 text-red-800')
                              .text('Error');
                
                $('#search-message').text('Gagal memuat data tracking: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#map-status').removeClass('bg-yellow-100 text-yellow-800')
                          .addClass('bg-red-100 text-red-800')
                          .text('Error');
            
            $('#search-message').text('Terjadi kesalahan saat memuat data tracking');
        }
    });
}

function addTrackingMarker(markerData) {
    // Tentukan status berdasarkan kategori_masalah dari tracking harian
    const status = determineTrackingStatus(markerData.tracking_summary);
    const color = getColorByTrackingStatus(status);
    const totalCases = markerData.tracking_summary.tidak_aman || 0;
    
    const marker = L.marker([markerData.lat, markerData.lng], {
        icon: L.divIcon({
            html: `<div class="tracking-marker w-8 h-8 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold" style="background-color: ${color};" title="Kasus Tidak Aman: ${totalCases}">${totalCases}</div>`,
            className: 'custom-tracking-marker',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        })
    });

    marker.on('click', function() {
        updateTrackingLocationInfo(markerData);
    });

    // Tambahkan event untuk menampilkan detail saat hover
    marker.on('mouseover', function() {
        showTrackingTooltip(markerData);
    });

    markersLayer.addLayer(marker);
}

function determineTrackingStatus(trackingSummary) {
    const aman = trackingSummary.aman || 0;
    const tidakAman = trackingSummary.tidak_aman || 0;
    const belumDicek = trackingSummary.belum_dicek || 0;
    const total = aman + tidakAman + belumDicek;

    if (total === 0) return 'belum_dicek';
    
    // Jika ada kasus tidak aman dalam 1 bulan terakhir
    if (tidakAman > 0) {
        // Jika > 50% kasus tidak aman = bahaya
        if ((tidakAman / total) > 0.5) return 'bahaya';
        // Jika 20-50% kasus tidak aman = waspada  
        if ((tidakAman / total) >= 0.2) return 'waspada';
        // Jika < 20% kasus tidak aman = tidak_aman
        return 'tidak_aman';
    }
    
    // Jika tidak ada kasus tidak aman tapi ada yang dicek = aman
    if (aman > 0) return 'aman';
    
    // Default belum dicek
    return 'belum_dicek';
}

function getColorByTrackingStatus(status) {
    switch(status) {
        case 'aman': return '#10b981';        // Green - Aman
        case 'tidak_aman': return '#f59e0b';  // Orange - Ada kasus tapi sedikit
        case 'waspada': return '#eab308';     // Yellow - Cukup banyak kasus
        case 'bahaya': return '#ef4444';      // Red - Banyak kasus tidak aman
        case 'belum_dicek':
        default: return '#6b7280';            // Gray - Belum ada data
    }
}

function getTrackingStatusText(status) {
    switch(status) {
        case 'aman': return 'Aman';
        case 'tidak_aman': return 'Perlu Perhatian';
        case 'waspada': return 'Waspada';
        case 'bahaya': return 'Bahaya';
        case 'belum_dicek':
        default: return 'Belum Dicek';
    }
}

function updateTrackingStatistics(statistics) {
    if (!statistics) return;
    
    // Update statistik berdasarkan data tracking harian BULAN BERJALAN
    $('#stat-aman').text(statistics.aman || 0);
    $('#stat-waspada').text(statistics.tidak_aman || 0); 
    
    // UBAH: info periode menjadi bulan berjalan
    const currentMonth = getCurrentMonthName();
    $('#stat-period').text(`Bulan ${currentMonth}`);
    $('#stat-total').text(statistics.total_records || 0);
    
    // Update chart jika ada
    if (typeof updateTrackingChart === 'function') {
        updateTrackingChart(statistics);
    }
}

function updateTrackingLocationInfo(markerData) {
    const summary = markerData.tracking_summary;
    const status = determineTrackingStatus(summary);
    const statusColor = getColorByTrackingStatus(status);
    const statusText = getTrackingStatusText(status);
    const currentMonth = getCurrentMonthName(); // TAMBAH
    
    const infoHtml = `
        <div class="tracking-location-info space-y-4">
            <div class="border-b pb-3">
                <h4 class="font-semibold text-gray-800">${markerData.title}</h4>
                <p class="text-sm text-gray-600">${markerData.subtitle || ''}</p>
                <p class="text-xs text-gray-500">${markerData.kecamatan || ''}</p>
            </div>
            
            <div class="tracking-status">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Status Wilayah:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: ${statusColor}">${statusText}</span>
                </div>
            </div>
            
            <div class="tracking-summary bg-gray-50 p-3 rounded">
                <div class="text-sm font-medium text-gray-700 mb-2">Ringkasan Bulan ${currentMonth}:</div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex justify-between">
                        <span class="text-green-600">Aman:</span>
                        <span class="font-bold text-green-600">${summary.aman || 0}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-red-600">Tidak Aman:</span>
                        <span class="font-bold text-red-600">${summary.tidak_aman || 0}</span>
                    </div>
                    <div class="flex justify-between col-span-2">
                        <span class="text-blue-600">Total Akumulasi:</span>
                        <span class="font-bold text-blue-600">${(summary.aman || 0) + (summary.tidak_aman || 0)}</span>
                    </div>
                </div>
            </div>
            
            <div class="tracking-action mt-3 pt-3 border-t">
                <div class="text-xs text-gray-500 mb-2">Rekomendasi:</div>
                <div class="text-sm text-gray-700">${getTrackingRecommendation(status)}</div>
            </div>
        </div>
    `;
    
    $('#location-info').html(infoHtml);
}

function getTrackingRecommendation(status) {
    switch(status) {
        case 'aman':
            return 'Pertahankan kondisi dengan terus melakukan monitoring rutin dan sosialisasi 3M Plus.';
        case 'tidak_aman':
            return 'Tingkatkan frekuensi pemeriksaan dan lakukan tindakan pencegahan tambahan.';
        case 'waspada':
            return 'Perlu perhatian khusus! Intensifkan monitoring dan koordinasi dengan puskesmas.';
        case 'bahaya':
            return 'Status Darurat! Segera lakukan tindakan komprehensif dan hubungi dinas kesehatan.';
        case 'belum_dicek':
        default:
            return 'Segera lakukan pemeriksaan untuk menentukan status wilayah ini.';
    }
}

function showTrackingTooltip(markerData) {
    const summary = markerData.tracking_summary;
    const tidakAman = summary.tidak_aman || 0;
    const currentMonth = getCurrentMonthName(); // TAMBAH
    
    // Implementasi tooltip sederhana
    console.log(`${markerData.title}: ${tidakAman} kasus tidak aman di bulan ${currentMonth}`);
}

function getCurrentMonthStart() {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
}

function getCurrentMonthEnd() {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().split('T')[0];
}

function getCurrentMonthName() {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const date = new Date();
    return months[date.getMonth()];
}

function getCurrentMonthYear() {
    const date = new Date();
    return `${getCurrentMonthName()} ${date.getFullYear()}`;
}

// Auto-update map saat dropdown berubah (real-time filtering)
$('#kecamatan, #kelurahan, #rw, #rt').change(function() {
    const kecamatan_id = $('#kecamatan').val();
    const kelurahan_id = $('#kelurahan').val();
    const rw_id = $('#rw').val();
    const rt_id = $('#rt').val();

    setTimeout(() => {
        loadMonthlyChartData();
    }, 500);
    
    // Update peta jika minimal kecamatan dipilih - dengan data bulan berjalan
    if (kecamatan_id) {
        loadTrackingMapData({
            kecamatan: kecamatan_id,
            kelurahan: kelurahan_id,
            rw: rw_id,
            rt: rt_id
        });
    }
});

// Enhanced button cari untuk data tracking
$('#btnCari').click(function() {
    const kecamatan_id = $('#kecamatan').val();
    const kelurahan_id = $('#kelurahan').val();
    const rw_id = $('#rw').val();
    const rt_id = $('#rt').val();

    if (!kecamatan_id) {
        alert('Pilih minimal Kecamatan untuk melihat data tracking');
        return;
    }

    // Show loading state
    $('#search-result').removeClass('hidden');
    const currentMonthYear = getCurrentMonthYear(); // TAMBAH
    $('#search-message').text(`Menganalisis data tracking ${currentMonthYear}...`);
    
    // Disable button sementara
    $(this).prop('disabled', true).text('Memuat...');

    // Load tracking data dengan filter
    loadTrackingMapData({
        kecamatan: kecamatan_id,
        kelurahan: kelurahan_id,
        rw: rw_id,
        rt: rt_id
    });

    // Re-enable button setelah delay
    setTimeout(() => {
        $(this).prop('disabled', false).text('Cari Lokasi');
    }, 2000);

    console.log('Tracking Search Data:', {
        kecamatan_id, kelurahan_id, rw_id, rt_id,
        period: 'current_month', // UBAH
        date_range: `${getCurrentMonthStart()} to ${getCurrentMonthEnd()}` // UBAH
    });
});

</script>
@endpush