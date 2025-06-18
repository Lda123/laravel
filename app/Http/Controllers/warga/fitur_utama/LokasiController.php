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

        // UBAH: Get real stats from tracking harian bulan berjalan
        $stats = $this->getTrackingStatistics();

        // Default user location (Surabaya center)
        $user_location = [
            'lat' => -7.2575,
            'lng' => 112.7521,
            'title' => 'Surabaya'
        ];

        // UBAH: period menjadi current_month
        $period = 'current_month';
        $defaultLat = -7.2575;
        $defaultLng = 112.7521;

        return view('warga.lokasi', compact(
            'kecamatan_options',
            'kelurahans', 
            'rws',
            'rts',
            'stats',
            'user_location',
            'period',
            'defaultLat',
            'defaultLng'
        ));
    }

    // Tidak ada perubahan pada method dropdown
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

    // UBAH: Main method untuk mendapatkan data tracking harian map - bulan berjalan
    public function getTrackingMapData(Request $request)
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

            // Base query untuk RT dengan koordinat
            $rtsQuery = Rt::with(['rw.kelurahan.kecamatan'])
                ->whereNotNull('koordinat_lat')
                ->whereNotNull('koordinat_lng');

            // Apply filters berdasarkan request
            if ($request->filled('rt')) {
                $rtsQuery->where('id', $request->rt);
                $coordinates['zoom'] = 16;
            } elseif ($request->filled('rw')) {
                $rtsQuery->where('rw_id', $request->rw);
                $coordinates['zoom'] = 15;
            } elseif ($request->filled('kelurahan')) {
                $rtsQuery->whereHas('rw', function($q) use ($request) {
                    $q->where('kelurahan_id', $request->kelurahan);
                });
                $coordinates['zoom'] = 14;
            } elseif ($request->filled('kecamatan')) {
                $rtsQuery->whereHas('rw.kelurahan', function($q) use ($request) {
                    $q->where('kecamatan_id', $request->kecamatan);
                });
                $coordinates['zoom'] = 13;
            } else {
                // Limit untuk performa jika tidak ada filter
                $rtsQuery->limit(100);
            }

            $rts = $rtsQuery->get();

            // Process each RT untuk mendapatkan data tracking bulan berjalan
            foreach ($rts as $rt) {
                $trackingData = $this->getTrackingDataForRt($rt->id);
                
                $mapData[] = [
                    'id' => $rt->id,
                    'lat' => (float) $rt->koordinat_lat,
                    'lng' => (float) $rt->koordinat_lng,
                    'title' => "RT {$rt->nomor_rt}/RW {$rt->rw->nomor_rw}",
                    'subtitle' => "{$rt->rw->kelurahan->nama_kelurahan}",
                    'kecamatan' => $rt->rw->kelurahan->kecamatan->nama_kecamatan,
                    'tracking_summary' => $trackingData['summary'],
                    'total_warga' => $trackingData['total_warga'],
                    'last_check' => $trackingData['last_check'],
                    'trend' => $trackingData['trend'],
                    'active_kaders' => $trackingData['active_kaders']
                ];
            }

            // Set center coordinates jika ada data
            if (!empty($mapData)) {
                $avgLat = array_sum(array_column($mapData, 'lat')) / count($mapData);
                $avgLng = array_sum(array_column($mapData, 'lng')) / count($mapData);
                
                $coordinates['lat'] = $avgLat;
                $coordinates['lng'] = $avgLng;
            }

            // Get overall statistics
            $statistics = $this->getOverallTrackingStatistics($mapData);

            return response()->json([
                'success' => true,
                'data' => [
                    'markers' => $mapData,
                    'center' => $coordinates,
                    'statistics' => $statistics,
                    'total_records' => count($mapData),
                    'period' => 'current_month' // UBAH: dari 'last_month' ke 'current_month'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data tracking: ' . $e->getMessage()
            ]);
        }
    }

    // UBAH: Method untuk mendapatkan tracking data RT - bulan berjalan
    private function getTrackingDataForRt($rtId)
    {
        // Get all warga in this RT
        $wargaIds = Warga::where('rt_id', $rtId)->pluck('id');
        $totalWarga = $wargaIds->count();

        if ($totalWarga == 0) {
            return [
                'total_warga' => 0,
                'summary' => [
                    'aman' => 0,
                    'tidak_aman' => 0,
                    'belum_dicek' => 0
                ],
                'last_check' => 'Belum ada data',
                'trend' => 'Tidak ada data',
                'active_kaders' => 0
            ];
        }

        // UBAH: Get tracking data untuk bulan berjalan saja
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $trackingData = TrackingHarian::whereIn('warga_id', $wargaIds)
            ->whereBetween('tanggal', [$currentMonthStart, $currentMonthEnd])
            ->get();

        // Count berdasarkan kategori_masalah
        $aman = $trackingData->where('kategori_masalah', 'Aman')->count();
        $tidakAman = $trackingData->where('kategori_masalah', 'Tidak Aman')->count();
        
        // Hitung warga yang belum pernah dicek dalam bulan berjalan
        $wargaYangSudahDicek = $trackingData->pluck('warga_id')->unique()->count();
        $belumDicek = $totalWarga - $wargaYangSudahDicek;

        // Get last check
        $lastTracking = $trackingData->sortByDesc('created_at')->first();
        $lastCheck = $lastTracking ? 
            Carbon::parse($lastTracking->created_at)->diffForHumans() : 
            'Belum ada pemeriksaan bulan ini';

        // UBAH: Calculate trend (compare dengan bulan sebelumnya)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $previousMonthData = TrackingHarian::whereIn('warga_id', $wargaIds)
            ->whereBetween('tanggal', [$lastMonthStart, $lastMonthEnd])
            ->where('kategori_masalah', 'Tidak Aman')
            ->count();

        $trend = 'Stabil';
        if ($previousMonthData > 0) {
            if ($tidakAman > $previousMonthData) {
                $trend = 'Meningkat';
            } elseif ($tidakAman < $previousMonthData) {
                $trend = 'Menurun';
            }
        } elseif ($tidakAman > 0) {
            $trend = 'Baru muncul bulan ini';
        }

        // Estimate active kaders (simplified)
        $activeKaders = max(1, intval($totalWarga / 20)); // Asumsi 1 kader per 20 warga

        return [
            'total_warga' => $totalWarga,
            'summary' => [
                'aman' => $aman,
                'tidak_aman' => $tidakAman,
                'belum_dicek' => $belumDicek
            ],
            'last_check' => $lastCheck,
            'trend' => $trend,
            'active_kaders' => $activeKaders
        ];
    }

    // Tidak ada perubahan pada method ini
    private function getOverallTrackingStatistics($mapData)
    {
        $amanCount = 0;
        $waspada = 0;
        $bahaya = 0;
        $belumDicek = 0;
        $totalRecords = 0;

        foreach ($mapData as $data) {
            $summary = $data['tracking_summary'];
            $total = $summary['aman'] + $summary['tidak_aman'] + $summary['belum_dicek'];
            
            if ($total == 0) {
                $belumDicek++;
                continue;
            }

            $totalRecords += $total;
            
            // Determine status based on percentage
            $tidakAmanPercentage = $total > 0 ? ($summary['tidak_aman'] / $total) : 0;
            
            if ($summary['tidak_aman'] == 0 && $summary['aman'] > 0) {
                $amanCount++;
            } elseif ($tidakAmanPercentage > 0.5) {
                $bahaya++;
            } elseif ($tidakAmanPercentage >= 0.2) {
                $waspada++;
            } else {
                $belumDicek++;
            }
        }

        return [
            'aman' => $amanCount,
            'waspada' => $waspada,
            'bahaya' => $bahaya,
            'belum_dicek' => $belumDicek,
            'total_records' => $totalRecords,
            'total_areas' => count($mapData)
        ];
    }

    // UBAH: Method untuk mendapatkan statistik bulan berjalan
    private function getTrackingStatistics()
    {
        // UBAH: Get statistics untuk bulan berjalan saja
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $rts = Rt::whereNotNull('koordinat_lat')
                ->whereNotNull('koordinat_lng')
                ->get();

        $amanCount = 0;
        $tidakAmanCount = 0;
        $belumDicekCount = 0;

        foreach ($rts as $rt) {
            $trackingData = $this->getTrackingDataForRt($rt->id);
            $summary = $trackingData['summary'];
            
            if ($summary['tidak_aman'] > 0) {
                $tidakAmanCount++;
            } elseif ($summary['aman'] > 0) {
                $amanCount++;
            } else {
                $belumDicekCount++;
            }
        }

        return (object) [
            'aman' => $amanCount,
            'tidak_aman' => $tidakAmanCount,
            'belum_dicek' => $belumDicekCount
        ];
    }

    // Tidak ada perubahan pada method ini
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

    // TAMBAH: Method baru untuk mendapatkan data chart dengan filter lokasi
    public function getMonthlyChartData(Request $request) {
        try {
            $month = $request->get('month', date('n'));
            $year = $request->get('year', date('Y'));
            
            // Mendapatkan jumlah hari dalam bulan
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            // Inisialisasi array untuk setiap hari dalam bulan
            $tidakAmanData = array_fill(0, $daysInMonth, 0);
            $amanData = array_fill(0, $daysInMonth, 0);
            
            // Query data tracking harian untuk bulan ini
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, $daysInMonth)->endOfDay();
            
            // TAMBAH: Base query untuk tracking dengan filter lokasi
            $trackingQuery = TrackingHarian::whereBetween('tanggal', [$startDate, $endDate]);
            
            // TAMBAH: Apply filter lokasi jika ada
            if ($request->filled('rt_id')) {
                $wargaIds = Warga::where('rt_id', $request->rt_id)->pluck('id');
                $trackingQuery->whereIn('warga_id', $wargaIds);
            } elseif ($request->filled('rw_id')) {
                $wargaIds = Warga::whereHas('rt', function($q) use ($request) {
                    $q->where('rw_id', $request->rw_id);
                })->pluck('id');
                $trackingQuery->whereIn('warga_id', $wargaIds);
            } elseif ($request->filled('kelurahan_id')) {
                $wargaIds = Warga::whereHas('rt.rw', function($q) use ($request) {
                    $q->where('kelurahan_id', $request->kelurahan_id);
                })->pluck('id');
                $trackingQuery->whereIn('warga_id', $wargaIds);
            } elseif ($request->filled('kecamatan_id')) {
                $wargaIds = Warga::whereHas('rt.rw.kelurahan', function($q) use ($request) {
                    $q->where('kecamatan_id', $request->kecamatan_id);
                })->pluck('id');
                $trackingQuery->whereIn('warga_id', $wargaIds);
            }
            
            // Mendapatkan data tracking harian per hari
            $trackingData = $trackingQuery
                ->selectRaw('DATE(tanggal) as tanggal, kategori_masalah, COUNT(*) as jumlah')
                ->groupBy('tanggal', 'kategori_masalah')
                ->get();
            
            // Mengisi data ke array berdasarkan tanggal
            foreach ($trackingData as $data) {
                $dayOfMonth = Carbon::parse($data->tanggal)->day;
                $arrayIndex = $dayOfMonth - 1; // Array dimulai dari 0
                
                if ($data->kategori_masalah === 'Tidak Aman') {
                    $tidakAmanData[$arrayIndex] = (int) $data->jumlah;
                } elseif ($data->kategori_masalah === 'Aman') {
                    $amanData[$arrayIndex] = (int) $data->jumlah;
                }
            }
            
            // Menghitung statistik
            $maxTidakAman = max($tidakAmanData);
            $totalTidakAman = array_sum($tidakAmanData);
            $maxAman = max($amanData);
            $totalAman = array_sum($amanData);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tidak_aman' => $tidakAmanData,
                    'aman' => $amanData,
                    'stats' => [
                        'maxTidakAman' => $maxTidakAman,
                        'totalTidakAman' => $totalTidakAman,
                        'maxAman' => $maxAman,
                        'totalAman' => $totalAman
                    ]
                ],
                'debug' => [
                    'period' => "{$month}/{$year}",
                    'days_in_month' => $daysInMonth,
                    'raw_data_count' => $trackingData->count(),
                    'date_range' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ],
                    'filters_applied' => [
                        'kecamatan_id' => $request->get('kecamatan_id'),
                        'kelurahan_id' => $request->get('kelurahan_id'),
                        'rw_id' => $request->get('rw_id'),
                        'rt_id' => $request->get('rt_id')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data grafik: ' . $e->getMessage(),
                'data' => [
                    'tidak_aman' => array_fill(0, date('t'), 0),
                    'aman' => array_fill(0, date('t'), 0),
                    'stats' => [
                        'maxTidakAman' => 0,
                        'totalTidakAman' => 0, // PERBAIKI: typo totalTidakAwan
                        'maxAman' => 0,
                        'totalAman' => 0
                    ]
                ]
            ]);
        }
    }
}