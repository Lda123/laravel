<?php

namespace App\Http\Controllers\warga\fitur_utama;
USE App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Warga;
use App\Models\TrackingHarian;
use Carbon\Carbon;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options
        $kecamatan_options = Kecamatan::orderBy('nama_kecamatan')->get();
        
        // Initialize collections for dropdowns
        $kelurahans = collect();
        $rws = collect();
        $rts = collect();
        
        // If kecamatan is selected, load kelurahans
        if ($request->filled('kecamatan')) {
            $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan)
                ->orderBy('nama_kelurahan')
                ->get();
        }
        
        // If kelurahan is selected, load RWs
        if ($request->filled('kelurahan')) {
            $rws = Rw::where('kelurahan_id', $request->kelurahan)
                ->orderBy('nomor_rw')
                ->get();
        }
        
        // If RW is selected, load RTs
        if ($request->filled('rw')) {
            $rts = Rt::where('rw_id', $request->rw)
                ->orderBy('nomor_rt')
                ->get();
        }

        // Get real stats from database
        $stats = $this->getAreaStatistics();

        // Default user location (Surabaya center)
        $user_location = [
            'lat' => -7.2575,
            'lng' => 112.7521,
            'title' => 'Surabaya',
            'rt' => '',
            'rw' => '',
            'kelurahan' => '',
            'kecamatan' => ''
        ];

        // Get real data
        $tracking_data = $this->getTrackingData();
        $rawan_areas = $this->getRawanAreas();
        $rtMarkers = $this->getRtMarkers();
        $period = 'harian';
        $defaultLat = -7.2575;
        $defaultLng = 112.7521;

        return view('warga.lokasi', compact(
            'kecamatan_options',
            'kelurahans', 
            'rws',
            'rts',
            'stats',
            'user_location',
            'tracking_data',
            'rawan_areas',
            'rtMarkers',
            'period',
            'defaultLat',
            'defaultLng'
        ));
    }

    public function getKelurahan(Request $request)
    {
        $kecamatan_id = $request->kecamatan_id;
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)
            ->orderBy('nama_kelurahan', 'asc')
            ->get();
        
        $options = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahans as $kelurahan) {
            $options .= "<option value='{$kelurahan->id}'>{$kelurahan->nama_kelurahan}</option>";
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRw(Request $request)
    {
        $kelurahan_id = $request->kelurahan_id;
        $rws = Rw::where('kelurahan_id', $kelurahan_id)
            ->orderBy('nomor_rw', 'asc')
            ->get();
        
        $options = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $options .= "<option value='{$rw->id}'>RW ".str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT)."</option>";
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRt(Request $request)
    {
        $rw_id = $request->rw_id;
        $rts = Rt::where('rw_id', $rw_id)
            ->orderBy('nomor_rt', 'asc')
            ->get();
        
        $options = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $options .= "<option value='{$rt->id}'>RT ".str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT)."</option>";
        }
        
        return response()->json(['options' => $options]);
    }

    public function updatePeriod(Request $request)
    {
        // This will be implemented later when you add chart functionality
        return response()->json([
            'success' => false,
            'message' => 'Fitur chart belum diimplementasikan'
        ]);
    }

    public function updateChart(Request $request)
    {
        // This will be implemented later when you add chart functionality
        return response()->json([
            'success' => false,
            'message' => 'Fitur chart belum diimplementasikan'
        ]);
    }

    public function getMapData(Request $request)
    {
        try {
            // Default koordinat Surabaya
            $defaultCoordinates = [
                'lat' => -7.2575,
                'lng' => 112.7521,
                'zoom' => 11
            ];

            $mapData = [];
            $coordinates = $defaultCoordinates;

            // Jika ada filter wilayah yang dipilih
            if ($request->filled(['kecamatan', 'kelurahan', 'rw', 'rt'])) {
                $rt = Rt::with(['rw.kelurahan.kecamatan'])
                    ->where('id', $request->rt)
                    ->first();

                if ($rt && $rt->koordinat_lat && $rt->koordinat_lng) {
                    $coordinates = [
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'zoom' => 16
                    ];

                    // Get real tracking data untuk RT ini
                    $trackingStats = $this->getTrackingStatsForRt($rt->id);

                    // Data marker untuk RT yang dipilih
                    $mapData[] = [
                        'id' => $rt->id,
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'title' => "RT {$rt->nomor_rt}",
                        'subtitle' => "RW {$rt->rw->nomor_rw}, {$rt->rw->kelurahan->nama_kelurahan}",
                        'kecamatan' => $rt->rw->kelurahan->kecamatan->nama_kecamatan,
                        'type' => 'rt_selected',
                        'status' => $trackingStats['status'],
                        'cases' => $trackingStats['tidak_aman'],
                        'population' => $trackingStats['total_warga'],
                        'popup_content' => $this->generatePopupContent($rt, $trackingStats)
                    ];
                }
            } elseif ($request->filled(['kecamatan', 'kelurahan', 'rw'])) {
                // Tampilkan semua RT dalam RW yang dipilih
                $rts = Rt::with(['rw.kelurahan.kecamatan'])
                    ->where('rw_id', $request->rw)
                    ->whereNotNull('koordinat_lat')
                    ->whereNotNull('koordinat_lng')
                    ->get();

                foreach ($rts as $rt) {
                    $trackingStats = $this->getTrackingStatsForRt($rt->id);
                    
                    $mapData[] = [
                        'id' => $rt->id,
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'title' => "RT {$rt->nomor_rt}",
                        'subtitle' => "RW {$rt->rw->nomor_rw}",
                        'kecamatan' => $rt->rw->kelurahan->kecamatan->nama_kecamatan,
                        'kelurahan' => $rt->rw->kelurahan->nama_kelurahan,
                        'type' => 'rt_in_rw',
                        'status' => $trackingStats['status'],
                        'cases' => $trackingStats['tidak_aman'],
                        'population' => $trackingStats['total_warga'],
                        'popup_content' => $this->generatePopupContent($rt, $trackingStats)
                    ];
                }

                // Set koordinat ke RT pertama jika ada
                if ($rts->isNotEmpty()) {
                    $firstRt = $rts->first();
                    $coordinates = [
                        'lat' => (float) $firstRt->koordinat_lat,
                        'lng' => (float) $firstRt->koordinat_lng,
                        'zoom' => 15
                    ];
                }
            } elseif ($request->filled(['kecamatan', 'kelurahan'])) {
                // Tampilkan semua RT dalam Kelurahan yang dipilih
                $rts = Rt::with(['rw.kelurahan.kecamatan'])
                    ->whereHas('rw.kelurahan', function($q) use ($request) {
                        $q->where('id', $request->kelurahan);
                    })
                    ->whereNotNull('koordinat_lat')
                    ->whereNotNull('koordinat_lng')
                    ->get();

                foreach ($rts as $rt) {
                    $trackingStats = $this->getTrackingStatsForRt($rt->id);
                    
                    $mapData[] = [
                        'id' => $rt->id,
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'title' => "RT {$rt->nomor_rt}/RW {$rt->rw->nomor_rw}",
                        'subtitle' => $rt->rw->kelurahan->nama_kelurahan,
                        'kecamatan' => $rt->rw->kelurahan->kecamatan->nama_kecamatan,
                        'type' => 'rt_in_kelurahan',
                        'status' => $trackingStats['status'],
                        'cases' => $trackingStats['tidak_aman'],
                        'population' => $trackingStats['total_warga'],
                        'popup_content' => $this->generatePopupContent($rt, $trackingStats)
                    ];
                }

                if ($rts->isNotEmpty()) {
                    // Hitung center dari semua RT
                    $avgLat = $rts->avg('koordinat_lat');
                    $avgLng = $rts->avg('koordinat_lng');
                    $coordinates = [
                        'lat' => (float) $avgLat,
                        'lng' => (float) $avgLng,
                        'zoom' => 14
                    ];
                }
            } else {
                // Tampilkan semua RT yang memiliki koordinat (untuk tampilan awal)
                $rts = Rt::with(['rw.kelurahan.kecamatan'])
                    ->whereNotNull('koordinat_lat')
                    ->whereNotNull('koordinat_lng')
                    ->limit(50) // Batasi untuk performa
                    ->get();

                foreach ($rts as $rt) {
                    $trackingStats = $this->getTrackingStatsForRt($rt->id);
                    
                    $mapData[] = [
                        'id' => $rt->id,
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'title' => "RT {$rt->nomor_rt}/RW {$rt->rw->nomor_rw}",
                        'subtitle' => "{$rt->rw->kelurahan->nama_kelurahan}, {$rt->rw->kelurahan->kecamatan->nama_kecamatan}",
                        'type' => 'rt_general',
                        'status' => $trackingStats['status'],
                        'cases' => $trackingStats['tidak_aman'],
                        'population' => $trackingStats['total_warga'],
                        'popup_content' => $this->generatePopupContent($rt, $trackingStats)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'markers' => $mapData,
                    'center' => $coordinates,
                    'bounds' => $this->calculateBounds($mapData)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data peta: ' . $e->getMessage()
            ]);
        }
    }

    private function generatePopupContent($rt, $trackingStats = null)
    {
        if (!$trackingStats) {
            $trackingStats = $this->getTrackingStatsForRt($rt->id);
        }

        $statusClass = $this->getStatusClass($trackingStats['status']);
        $statusText = $this->getStatusText($trackingStats['status']);
        
        return "
            <div class='p-3'>
                <h4 class='font-semibold text-lg mb-2'>RT {$rt->nomor_rt} / RW {$rt->rw->nomor_rw}</h4>
                <p class='text-sm text-gray-600 mb-2'>{$rt->rw->kelurahan->nama_kelurahan}, {$rt->rw->kelurahan->kecamatan->nama_kecamatan}</p>
                <div class='space-y-1 text-sm'>
                    <div class='flex justify-between'>
                        <span>Jumlah Warga:</span>
                        <span class='font-medium'>{$trackingStats['total_warga']} orang</span>
                    </div>
                    <div class='flex justify-between'>
                        <span>Kasus Tidak Aman:</span>
                        <span class='font-medium text-red-600'>{$trackingStats['tidak_aman']} kasus</span>
                    </div>
                    <div class='flex justify-between'>
                        <span>Kondisi Aman:</span>
                        <span class='font-medium text-green-600'>{$trackingStats['aman']} kasus</span>
                    </div>
                    <div class='flex justify-between'>
                        <span>Belum Dicek:</span>
                        <span class='font-medium text-yellow-600'>{$trackingStats['belum_dicek']} kasus</span>
                    </div>
                    <div class='flex justify-between mt-2'>
                        <span>Status Wilayah:</span>
                        <span class='px-2 py-1 rounded text-xs {$statusClass}'>{$statusText}</span>
                    </div>
                    <div class='flex justify-between'>
                        <span>Update Terakhir:</span>
                        <span class='text-xs text-gray-500'>{$trackingStats['last_update']}</span>
                    </div>
                </div>
            </div>
        ";
    }

    private function getTrackingStatsForRt($rtId)
    {
        // Get all warga in this RT
        $wargaIds = Warga::where('rt_id', $rtId)->pluck('id');
        $totalWarga = $wargaIds->count();

        if ($totalWarga == 0) {
            return [
                'total_warga' => 0,
                'aman' => 0,
                'tidak_aman' => 0,
                'belum_dicek' => 0,
                'status' => 'belum_dicek',
                'last_update' => 'Belum ada data'
            ];
        }

        // Get tracking data for this month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $trackingData = TrackingHarian::whereIn('warga_id', $wargaIds)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->get();

        // Count status
        $aman = $trackingData->where('kategori_masalah', 'Aman')->count();
        $tidakAman = $trackingData->where('kategori_masalah', 'Tidak Aman')->count();
        $belumDicek = $totalWarga - $aman - $tidakAman;

        // Determine overall status
        $status = 'belum_dicek';
        if ($tidakAman > 0) {
            if ($tidakAman > 15) {
                $status = 'bahaya';
            } elseif ($tidakAman > 5) {
                $status = 'waspada';
            } else {
                $status = 'tidak_aman';
            }
        } elseif ($aman > 0) {
            $status = 'aman';
        }

        // Get last update
        $lastUpdate = $trackingData->max('created_at');
        $lastUpdateFormatted = $lastUpdate ? 
            Carbon::parse($lastUpdate)->diffForHumans() : 
            'Belum ada data';

        return [
            'total_warga' => $totalWarga,
            'aman' => $aman,
            'tidak_aman' => $tidakAman,
            'belum_dicek' => $belumDicek,
            'status' => $status,
            'last_update' => $lastUpdateFormatted
        ];
    }

    private function getStatusClass($status)
    {
        switch($status) {
            case 'aman':
                return 'bg-green-100 text-green-800';
            case 'tidak_aman':
                return 'bg-orange-100 text-orange-800';
            case 'waspada':
                return 'bg-yellow-100 text-yellow-800';
            case 'bahaya':
                return 'bg-red-100 text-red-800';
            case 'belum_dicek':
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    private function getStatusText($status)
    {
        switch($status) {
            case 'aman':
                return 'Aman';
            case 'tidak_aman':
                return 'Tidak Aman';
            case 'waspada':
                return 'Waspada';
            case 'bahaya':
                return 'Bahaya';
            case 'belum_dicek':
            default:
                return 'Belum Dicek';
        }
    }

    private function calculateBounds($markers)
    {
        if (empty($markers)) {
            return null;
        }

        $lats = array_column($markers, 'lat');
        $lngs = array_column($markers, 'lng');

        return [
            'north' => max($lats) + 0.001,
            'south' => min($lats) - 0.001,
            'east' => max($lngs) + 0.001,
            'west' => min($lngs) - 0.001
        ];
    }

    public function getWilayahKoordinat(Request $request)
    {
        try {
            $coordinates = null;
            $info = [];

            if ($request->filled('rt_id')) {
                $rt = Rt::with(['rw.kelurahan.kecamatan'])
                    ->where('id', $request->rt_id)
                    ->first();

                if ($rt && $rt->koordinat_lat && $rt->koordinat_lng) {
                    $coordinates = [
                        'lat' => (float) $rt->koordinat_lat,
                        'lng' => (float) $rt->koordinat_lng,
                        'zoom' => 16
                    ];
                    $info = [
                        'rt' => $rt->nomor_rt,
                        'rw' => $rt->rw->nomor_rw,
                        'kelurahan' => $rt->rw->kelurahan->nama_kelurahan,
                        'kecamatan' => $rt->rw->kelurahan->kecamatan->nama_kecamatan
                    ];
                }
            }

            return response()->json([
                'success' => !!$coordinates,
                'coordinates' => $coordinates,
                'info' => $info,
                'message' => $coordinates ? 'Koordinat ditemukan' : 'Koordinat tidak tersedia untuk wilayah ini'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan koordinat: ' . $e->getMessage()
            ]);
        }
    }

    private function getAreaStatistics()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all RT that have coordinates
        $rts = Rt::whereNotNull('koordinat_lat')
                ->whereNotNull('koordinat_lng')
                ->get();

        $amanCount = 0;
        $tidakAmanCount = 0;
        $belumDicekCount = 0;

        foreach ($rts as $rt) {
            $stats = $this->getTrackingStatsForRt($rt->id);
            
            switch($stats['status']) {
                case 'aman':
                    $amanCount++;
                    break;
                case 'tidak_aman':
                case 'waspada':
                case 'bahaya':
                    $tidakAmanCount++;
                    break;
                case 'belum_dicek':
                default:
                    $belumDicekCount++;
                    break;
            }
        }

        return (object) [
            'aman' => $amanCount,
            'tidak_aman' => $tidakAmanCount,
            'belum_dicek' => $belumDicekCount
        ];
    }

    private function getTrackingData()
    {
        // Return recent tracking data for charts (implement later)
        return [];
    }

    private function getRawanAreas()
    {
        // Get areas with high cases
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $rts = Rt::with(['rw.kelurahan.kecamatan'])
            ->whereNotNull('koordinat_lat')
            ->whereNotNull('koordinat_lng')
            ->get();

        $rawanAreas = [];
        
        foreach ($rts as $rt) {
            $stats = $this->getTrackingStatsForRt($rt->id);
            
            if ($stats['status'] === 'bahaya' || $stats['tidak_aman'] > 10) {
                $rawanAreas[] = [
                    'rt_id' => $rt->id,
                    'nama' => "RT {$rt->nomor_rt}/RW {$rt->rw->nomor_rw}",
                    'wilayah' => "{$rt->rw->kelurahan->nama_kelurahan}, {$rt->rw->kelurahan->kecamatan->nama_kecamatan}",
                    'kasus' => $stats['tidak_aman'],
                    'lat' => $rt->koordinat_lat,
                    'lng' => $rt->koordinat_lng
                ];
            }
        }

        return $rawanAreas;
    }

    private function getRtMarkers()
    {
        // Get all RT markers for map
        $rts = Rt::with(['rw.kelurahan.kecamatan'])
            ->whereNotNull('koordinat_lat')
            ->whereNotNull('koordinat_lng')
            ->limit(100) // Limit for performance
            ->get();

        $markers = [];
        
        foreach ($rts as $rt) {
            $stats = $this->getTrackingStatsForRt($rt->id);
            
            $markers[] = [
                'id' => $rt->id,
                'lat' => (float) $rt->koordinat_lat,
                'lng' => (float) $rt->koordinat_lng,
                'title' => "RT {$rt->nomor_rt}/RW {$rt->rw->nomor_rw}",
                'status' => $stats['status'],
                'cases' => $stats['tidak_aman'],
                'info' => $this->generatePopupContent($rt, $stats)
            ];
        }

        return $markers;
    }
}