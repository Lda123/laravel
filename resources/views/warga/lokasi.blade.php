@extends('layouts.app')

@section('title', 'Lokasi - DengueCare')

@section('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Peta Lokasi dan Pemantauan DBD</h1>
        
        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Cari Wilayah</h2>
            <form id="searchForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">Kecamatan</label>
                    <select id="kecamatan" name="kecamatan" class="input-focus-effect w-full px-4 py-2 border rounded-lg transition-all duration-300 focus:outline-none">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
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
                        <p class="text-2xl font-bold text-green-800">{{ $stats['aman'] ?? 0 }}</p>
                        <p class="text-gray-600">Aman</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg">
                        <p class="text-2xl font-bold text-red-800">{{ $stats['tidak_aman'] ?? 0 }}</p>
                        <p class="text-gray-600">Tidak Aman</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['belum_dicek'] ?? 0 }}</p>
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
                @foreach($rawanAreas as $area)
                <div class="location-card p-4 rounded-lg border {{ 
                    $area['rumah_tidak_aman'] > 5 || $area['kasus_dbd'] > 2 ? 'risk-high' : 
                    ($area['rumah_tidak_aman'] > 2 || $area['kasus_dbd'] > 0 ? 'risk-medium' : 'risk-low') }}">
                    <h3 class="font-bold text-lg">{{ $area['wilayah'] }}</h3>
                    <p class="text-sm mb-2">Status: 
                        <span class="{{ 
                            $area['rumah_tidak_aman'] > 5 || $area['kasus_dbd'] > 2 ? 'text-red-600 font-bold' : 
                            ($area['rumah_tidak_aman'] > 2 || $area['kasus_dbd'] > 0 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold') }}">
                            {{ 
                                $area['rumah_tidak_aman'] > 5 || $area['kasus_dbd'] > 2 ? 'Rawan Tinggi' : 
                                ($area['rumah_tidak_aman'] > 2 || $area['kasus_dbd'] > 0 ? 'Rawan Sedang' : 'Aman') }}
                        </span>
                    </p>
                    <p class="text-sm">Rumah Tidak Aman: {{ $area['rumah_tidak_aman'] }}</p>
                    <p class="text-sm">Kasus DBD: {{ $area['kasus_dbd'] }}</p>
                    <p class="text-sm mt-2">Total Rumah: {{ $area['total_rumah'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
 <!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="json-data" type="application/json">
    {!! json_encode([
        'trackingData' => $trackingData,
        'rawanAreas' => $rawanAreas,
        'userLocation' => $userLocation,
        'chartLabels' => $chartLabels,
        'chartValues' => $chartValues,
    ]) !!}
</script>

<script>
    const jsonData = JSON.parse(document.getElementById('json-data').textContent);
    const { trackingData, rawanAreas, userLocation, chartLabels, chartValues } = jsonData;
</script>

<script>
    // Initialize Chart
    const ctx = document.getElementById('caseChart').getContext('2d');
    const caseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Kasus DBD',
                data: chartValues,
                backgroundColor: '#3B82F6',
                borderColor: '#1D4ED8',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
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

    let map;
    let wilayahMarker = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize map centered on Surabaya
        map = L.map('map').setView([-7.2575, 112.7521], 13);

        // OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Custom Icons
        const userIcon = L.divIcon({
            className: 'user-icon',
            html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
            iconSize: [26, 26]
        });

        const safeIcon = L.divIcon({
            className: 'safe-icon',
            html: '<div style="background-color: #16a34a; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white;"></div>',
            iconSize: [20, 20]
        });

        const dangerIcon = L.divIcon({
            className: 'danger-icon',
            html: '<div style="background-color: #dc2626; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white;"></div>',
            iconSize: [20, 20]
        });

        const dbdIcon = L.divIcon({
            className: 'dbd-icon',
            html: '<div style="background-color: #b91c1c; width: 18px; height: 18px; border-radius: 50%; border: 2px solid white;"></div>',
            iconSize: [22, 22]
        });

        // User Marker
        if (userLocation.lat && userLocation.lng) {
            L.marker([userLocation.lat, userLocation.lng], {
                icon: userIcon,
                title: userLocation.title
            }).addTo(map).bindPopup(`
                <div style="width: 250px;">
                    <b>${userLocation.title}</b>
                    <p>RT ${userLocation.rt}/RW ${userLocation.rw}</p>
                    <p>${userLocation.kelurahan}, ${userLocation.kecamatan}</p>
                </div>
            `);
        }

        // Tracking Markers
        trackingData.forEach(data => {
            let icon, statusText, additionalInfo;

            if (data.status_kesehatan === 'Terkena DBD') {
                icon = dbdIcon;
                statusText = 'TERKENA DBD';
                additionalInfo = `<p><b>Status:</b> ${data.status_kesehatan}</p>`;
            } else if (data.kategori_masalah === 'Tidak Aman') {
                icon = dangerIcon;
                statusText = 'TIDAK AMAN';
                additionalInfo = `<p><b>Masalah:</b> ${data.deskripsi || 'Lingkungan kotor'}</p>`;
            } else {
                icon = safeIcon;
                statusText = 'AMAN';
                additionalInfo = '<p>Tidak ada masalah yang dilaporkan</p>';
            }

            L.marker([data.lat, data.lng], { icon }).addTo(map).bindPopup(`
                <div style="width: 250px;">
                    <h3>Rumah ${data.nama_warga}</h3>
                    <p><b>Status:</b> ${statusText}</p>
                    <p><b>Wilayah:</b> RT ${data.rt}/RW ${data.rw}, ${data.kelurahan}, ${data.kecamatan}</p>
                    ${additionalInfo}
                    <p><b>Terakhir Dipantau:</b> ${new Date(data.tanggal_pantau).toLocaleDateString()}</p>
                </div>
            `);
        });

        // High Risk Circles
        rawanAreas.forEach(area => {
            if (area.koordinat_lat && area.koordinat_lng) {
                const color = area.rumah_tidak_aman > 5 || area.kasus_dbd > 2 ? '#FF0000' :
                    area.rumah_tidak_aman > 2 || area.kasus_dbd > 0 ? '#FFA500' : '#00FF00';

                L.circle([area.koordinat_lat, area.koordinat_lng], {
                    color,
                    fillColor: color,
                    fillOpacity: 0.3,
                    radius: 200
                }).addTo(map).bindPopup(`
                    <div style="width: 250px;">
                        <h3>${area.wilayah}</h3>
                        <p><b>Status:</b> ${color === '#FF0000' ? 'Rawan Tinggi' : color === '#FFA500' ? 'Rawan Sedang' : 'Aman'}</p>
                        <p><b>Rumah Tidak Aman:</b> ${area.rumah_tidak_aman}</p>
                        <p><b>Kasus DBD:</b> ${area.kasus_dbd}</p>
                        <p><b>Total Rumah:</b> ${area.total_rumah}</p>
                    </div>
                `);
            }
        });
    });

    // Wilayah Search
    document.getElementById('btnCari').addEventListener('click', function () {
        const kecamatan = document.getElementById('kecamatan').value;
        const kelurahan = document.getElementById('kelurahan').value;
        const rw = document.getElementById('rw').value;
        const rt = document.getElementById('rt').value;

        if (!kecamatan) {
            alert('Pilih kecamatan terlebih dahulu');
            return;
        }

        $.ajax({
            url: '{{ route("wilayah.coordinates") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                kecamatan_id: kecamatan,
                kelurahan_id: kelurahan,
                rw_id: rw,
                rt_id: rt
            },
            success: function (response) {
                if (response.success) {
                    if (wilayahMarker) map.removeLayer(wilayahMarker);

                    const wilayahTrackingData = trackingData.filter(item => {
                        return (!kecamatan || item.kecamatan_id == kecamatan) &&
                            (!kelurahan || item.kelurahan_id == kelurahan) &&
                            (!rw || item.rw_id == rw) &&
                            (!rt || item.rt_id == rt);
                    });

                    let rumahTidakAman = 0, kasusDBD = 0;
                    wilayahTrackingData.forEach(data => {
                        if (data.kategori_masalah === 'Tidak Aman') rumahTidakAman++;
                        if (data.status_kesehatan === 'Terkena DBD') kasusDBD++;
                    });

                    let status, color;
                    if (rumahTidakAman > 5 || kasusDBD > 2) {
                        status = 'Rawan Tinggi';
                        color = '#FF0000';
                    } else if (rumahTidakAman > 2 || kasusDBD > 0) {
                        status = 'Rawan Sedang';
                        color = '#FFA500';
                    } else {
                        status = 'Aman';
                        color = '#00FF00';
                    }

                    wilayahMarker = L.marker([response.lat, response.lng], {
                        icon: L.divIcon({
                            className: 'wilayah-icon',
                            html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white;"></div>`,
                            iconSize: [30, 30]
                        })
                    }).addTo(map).bindPopup(`
                        <div style="width: 250px;">
                            <b>${response.nama_wilayah}</b>
                            <p><b>Status:</b> ${status}</p>
                            <p><b>Rumah Tidak Aman:</b> ${rumahTidakAman}</p>
                            <p><b>Kasus DBD:</b> ${kasusDBD}</p>
                            <p><b>Total Rumah Dipantau:</b> ${wilayahTrackingData.length}</p>
                        </div>
                    `);
                    map.setView([response.lat, response.lng], 16);
                } else {
                    alert('Wilayah tidak ditemukan');
                }
            },
            error: function () {
                alert('Gagal memuat data wilayah');
            }
        });
    });

    // Period Filter
    document.getElementById('period-select')?.addEventListener('change', function () {
        window.location.href = '{{ route("lokasi") }}?period=' + this.value;
    });

    // Dynamic Dropdowns
    $(document).ready(function () {
        $('#kecamatan').change(function () {
            $.post('{{ route("dropdown.kelurahan") }}', {
                _token: '{{ csrf_token() }}',
                kecamatan_id: $(this).val()
            }, function (data) {
                $('#kelurahan').html(data).prop('disabled', false);
                $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
                $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
            });
        });

        $('#kelurahan').change(function () {
            $.post('{{ route("dropdown.rw") }}', {
                _token: '{{ csrf_token() }}',
                kelurahan_id: $(this).val()
            }, function (data) {
                $('#rw').html(data).prop('disabled', false);
                $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
            });
        });

        $('#rw').change(function () {
            $.post('{{ route("dropdown.rt") }}', {
                _token: '{{ csrf_token() }}',
                rw_id: $(this).val()
            }, function (data) {
                $('#rt').html(data).prop('disabled', false);
            });
        });
    });
</script>
@endsection