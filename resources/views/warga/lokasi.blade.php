@extends('layouts.warga')

@section('title', 'Lokasi - DengueCare')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
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
    </style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Peta Lokasi dan Pemantauan DBD</h1>
    
    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Cari Wilayah</h2>
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf
            <div>
                <label class="block text-gray-700 mb-2">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatan_options as $kecamatan)
                        <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelurahan</label>
                <select id="kelurahan" name="kelurahan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none" disabled>
                    <option value="">Pilih Kelurahan</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">RW</label>
                <select id="rw" name="rw" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none" disabled>
                    <option value="">Pilih RW</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">RT</label>
                <select id="rt" name="rt" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none" disabled>
                    <option value="">Pilih RT</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" id="btnCari" class="input-focus-effect w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Map Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Peta Pemantauan DBD Surabaya</h2>
        <div id="map"></div>
    </div>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Status Rumah -->
        <div class="bg-white rounded-xl shadow-md p-6">
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
        <div class="bg-white rounded-xl shadow-md p-6">
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
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Wilayah dengan Potensi DBD Tinggi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($rawan_areas as $area)
                @php
                    $riskClass = 'risk-low';
                    $riskText = 'Aman';
                    $riskColor = 'text-green-600';
                    
                    if($area->rumah_tidak_aman > 5) {
                        $riskClass = 'risk-high';
                        $riskText = 'Rawan Tinggi';
                        $riskColor = 'text-red-600';
                    } elseif($area->rumah_tidak_aman > 2) {
                        $riskClass = 'risk-medium';
                        $riskText = 'Rawan Sedang';
                        $riskColor = 'text-yellow-600';
                    }
                @endphp
                <div class="location-card p-4 rounded-lg border {{ $riskClass }}">
                    <h3 class="font-bold text-lg">{{ $area->wilayah }}</h3>
                    <p class="text-sm mb-2">Status: 
                        <span class="{{ $riskColor }} font-bold">{{ $riskText }}</span>
                    </p>
                    <p class="text-sm">Rumah Tidak Aman: {{ $area->rumah_tidak_aman }}</p>
                    <p class="text-sm mt-2">Total Rumah: {{ $area->total_rumah }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Pass data from controller to JavaScript
    const trackingData = @json($tracking_data);
    const rawanAreas = @json($rawan_areas);
    const userLocation = @json($user_location);
    const caseData = @json($case_data);

    // Initialize Map with Leaflet
    let map;
    let wilayahMarker = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map centered on Surabaya
        map = L.map('map').setView([-7.2575, 112.7521], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Custom icons
        const userIcon = L.divIcon({
            className: 'user-icon',
            html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
            iconSize: [26, 26]
        });

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

        // Add user marker if coordinates exist
        if (userLocation.lat && userLocation.lng) {
            const userMarker = L.marker([userLocation.lat, userLocation.lng], {
                icon: userIcon,
                title: userLocation.title
            }).addTo(map).bindPopup(`
                <div style="width: 250px;">
                    <b>${userLocation.title}</b>
                    <p><b>Wilayah Anda:</b></p>
                    <p>RT ${userLocation.rt}/RW ${userLocation.rw}</p>
                    <p>${userLocation.kelurahan}, ${userLocation.kecamatan}</p>
                </div>
            `);
        }

        // Add tracking markers
        trackingData.forEach(data => {
            let icon;
            let statusText = '';
            let additionalInfo = '';
            
            if (data.kategori_masalah === 'Tidak Aman') {
                icon = dangerIcon;
                statusText = '<span class="status-tidak-aman">TIDAK AMAN</span>';
                additionalInfo = `<p><b>Masalah:</b> ${data.deskripsi || 'Lingkungan kotor'}</p>`;
            } else {
                icon = safeIcon;
                statusText = '<span class="status-aman">AMAN</span>';
                additionalInfo = '<p>Tidak ada masalah yang dilaporkan</p>';
            }
            
            const marker = L.marker([data.lat, data.lng], {
                icon: icon
            }).addTo(map).bindPopup(`
                <div style="width: 250px;">
                    <h3>Rumah ${data.nama_warga}</h3>
                    <p><b>Status:</b> ${statusText}</p>
                    <p><b>Wilayah:</b> RT ${data.rt}/RW ${data.rw}, ${data.kelurahan}, ${data.kecamatan}</p>
                    ${additionalInfo}
                    <p><b>Terakhir Dipantau:</b> ${new Date(data.tanggal).toLocaleDateString()}</p>
                </div>
            `);
        });

        // Add high risk areas as circles
        rawanAreas.forEach(area => {
            if (area.koordinat_lat && area.koordinat_lng) {
                const color = area.rumah_tidak_aman > 5 ? '#FF0000' : 
                            (area.rumah_tidak_aman > 2 ? '#FFA500' : '#00FF00');
                
                const circle = L.circle([area.koordinat_lat, area.koordinat_lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.3,
                    radius: 200
                }).addTo(map).bindPopup(`
                    <div style="width: 250px;">
                        <h3>${area.wilayah}</h3>
                        <p><b>Status:</b> 
                            ${area.rumah_tidak_aman > 5 ? 'Rawan Tinggi' : 
                             area.rumah_tidak_aman > 2 ? 'Rawan Sedang' : 'Aman'}
                        </p>
                        <p><b>Rumah Tidak Aman:</b> ${area.rumah_tidak_aman}</p>
                        <p><b>Total Rumah:</b> ${area.total_rumah}</p>
                    </div>
                `);
            }
        });

        // Initialize Chart
        initializeChart();
    });

    function initializeChart() {
        const ctx = document.getElementById('caseChart').getContext('2d');
        
        // Process case data for chart
        let labels = [];
        let data = [];
        
        caseData.forEach(item => {
            if (item.tanggal) {
                labels.push(new Date(item.tanggal).toLocaleDateString());
            } else if (item.bulan) {
                labels.push(item.bulan);
            } else if (item.minggu) {
                labels.push(`Minggu ${item.minggu}`);
            }
            data.push(item.jumlah);
        });

        // Reverse arrays to show chronological order
        labels.reverse();
        data.reverse();

        const caseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kasus Tidak Aman',
                    data: data,
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
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Search functionality
    document.getElementById('btnCari').addEventListener('click', function() {
        const kecamatan = document.getElementById('kecamatan').value;
        const kelurahan = document.getElementById('kelurahan').value;
        const rw = document.getElementById('rw').value;
        const rt = document.getElementById('rt').value;
        
        if (!kecamatan) {
            alert('Pilih kecamatan terlebih dahulu');
            return;
        }
        
        // AJAX request to get coordinates
        $.ajax({
            url: '{{ route("lokasi.coordinates") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                kecamatan_id: kecamatan,
                kelurahan_id: kelurahan,
                rw_id: rw,
                rt_id: rt
            },
            success: function(response) {
                if (response.success) {
                    // Remove previous marker if exists
                    if (wilayahMarker) {
                        map.removeLayer(wilayahMarker);
                    }
                    
                    // Filter tracking data for this area
                    const wilayahTrackingData = trackingData.filter(item => {
                        let match = true;
                        if (kecamatan) match = match && (item.kecamatan_id == kecamatan);
                        if (kelurahan) match = match && (item.kelurahan_id == kelurahan);
                        if (rw) match = match && (item.rw_id == rw);
                        if (rt) match = match && (item.rt_id == rt);
                        return match;
                    });
                    
                    // Calculate area status
                    let rumahTidakAman = 0;
                    
                    wilayahTrackingData.forEach(data => {
                        if (data.kategori_masalah === 'Tidak Aman') rumahTidakAman++;
                    });
                    
                    // Determine status and color
                    let status, color;
                    if (rumahTidakAman > 5) {
                        status = 'Rawan Tinggi';
                        color = '#FF0000';
                    } else if (rumahTidakAman > 2) {
                        status = 'Rawan Sedang';
                        color = '#FFA500';
                    } else {
                        status = 'Aman';
                        color = '#00FF00';
                    }
                    
                    // Add marker for searched area
                    wilayahMarker = L.marker([response.lat, response.lng], {
                        icon: L.divIcon({
                            className: 'wilayah-icon',
                            html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>`,
                            iconSize: [30, 30]
                        })
                    }).addTo(map).bindPopup(`
                        <div style="width: 250px;">
                            <b>${response.nama_wilayah}</b>
                            <p><b>Status:</b> <span style="color: ${color}; font-weight: bold;">${status}</span></p>
                            <p><b>Rumah Tidak Aman:</b> ${rumahTidakAman}</p>
                            <p><b>Total Rumah Dipantau:</b> ${wilayahTrackingData.length}</p>
                        </div>
                    `);
                    
                    // Zoom to selected area
                    map.setView([response.lat, response.lng], 16);
                    
                } else {
                    alert('Wilayah tidak ditemukan');
                }
            },
            error: function() {
                alert('Gagal memuat data wilayah');
            }
        });
    });

    // Period select change
    document.getElementById('period-select').addEventListener('change', function() {
        window.location.href = `{{ route('warga.lokasi') }}?period=${this.value}`;
    });

    // Dynamic dropdowns with jQuery
    $(document).ready(function() {
        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Kecamatan dropdown change event
        $('#kecamatan').change(function() {
            var kecamatan_id = $(this).val();
            if(kecamatan_id) {
                $.ajax({
                    url: '{{ route("lokasi.kelurahan") }}',
                    type: 'POST',
                    data: {kecamatan_id: kecamatan_id},
                    success: function(data) {
                        $('#kelurahan').html(data).prop('disabled', false);
                        $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
                        $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                        alert('Gagal memuat data kelurahan. Silakan coba lagi.');
                    }
                });
            } else {
                $('#kelurahan, #rw, #rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
            }
        });

        // Kelurahan dropdown change event
        $('#kelurahan').change(function() {
            var kelurahan_id = $(this).val();
            if(kelurahan_id) {
                $.ajax({
                    url: '{{ route("lokasi.rw") }}',
                    type: 'POST',
                    data: {kelurahan_id: kelurahan_id},
                    success: function(data) {
                        $('#rw').html(data).prop('disabled', false);
                        $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                        alert('Gagal memuat data RW. Silakan coba lagi.');
                    }
                });
            } else {
                $('#rw, #rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
            }
        });

        // RW dropdown change event
        $('#rw').change(function() {
            var rw_id = $(this).val();
            if(rw_id) {
                $.ajax({
                    url: '{{ route("lokasi.rt") }}',
                    type: 'POST',
                    data: {rw_id: rw_id},
                    success: function(data) {
                        $('#rt').html(data).prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                        alert('Gagal memuat data RT. Silakan coba lagi.');
                    }
                });
            } else {
                $('#rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
            }
        });
    });
</script>
@endsection