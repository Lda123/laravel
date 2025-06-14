<?php

namespace App\Http\Controllers\warga\fitur_utama;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Warga;
use App\Models\TrackingHarian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LokasiController extends Controller
{
    public function index()
    {
        // Ambil data kecamatan untuk dropdown
        $kecamatan_options = Kecamatan::orderBy('nama_kecamatan')->get();
        
        // Ambil statistik rumah
        $stats = $this->getRumahStats();
        
        // Ambil data grafik kasus
        $chartData = $this->getChartData('bulanan');
        
        // Ambil wilayah rawan
        $rawan_areas = $this->getRawanAreas();
        
        return view('warga.lokasi', compact(
            'kecamatan_options', 
            'stats', 
            'chartData', 
            'rawan_areas'
        ));
    }
    
/*************  ✨ Windsurf Command ⭐  *************/
/**
 * Retrieve a list of kelurahan (sub-districts) based on the provided kecamatan (district) ID.
 *

/*******  b420ada5-b71e-4392-806b-568d63165dd4  *******/
    public function getKelurahan(Request $request)
    {
        $kecamatanId = $request->kecamatan_id;
        $kelurahan = Kelurahan::where('kecamatan_id', $kecamatanId)
                             ->orderBy('nama_kelurahan')
                             ->get(['id', 'nama_kelurahan']);
        
        return response()->json($kelurahan);
    }
    
    public function getRw(Request $request)
    {
        $kelurahanId = $request->kelurahan_id;
        $rw = Rw::where('kelurahan_id', $kelurahanId)
                ->orderBy('nomor_rw')
                ->get(['id', 'nomor_rw']);
        
        return response()->json($rw);
    }
    
    public function getRt(Request $request)
    {
        $rwId = $request->rw_id;
        $rt = Rt::where('rw_id', $rwId)
                ->orderBy('nomor_rt')
                ->get(['id', 'nomor_rt', 'koordinat_lat', 'koordinat_lng']);
        
        return response()->json($rt);
    }
    
/*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Mencari lokasi berdasarkan RT ID dan mengembalikan data lokasi, koordinat, dan data rumah di RT tersebut.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
/*******  a134d207-b489-4dec-b0d8-52e3d66c482a  *******/
    public function cariLokasi(Request $request)
    {
        $rtId = $request->rt_id;
        
        if (!$rtId) {
            return response()->json(['error' => 'RT tidak ditemukan'], 404);
        }
        
        $rt = Rt::with(['rw.kelurahan.kecamatan'])->find($rtId);
        
        if (!$rt) {
            return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
        }
        
        // Ambil data rumah di RT tersebut
        $rumahData = $this->getRumahDataByRt($rtId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'rt' => $rt,
                'koordinat' => [
                    'lat' => $rt->koordinat_lat,
                    'lng' => $rt->koordinat_lng
                ],
                'rumah' => $rumahData,
                'alamat_lengkap' => "RT {$rt->nomor_rt}, RW {$rt->rw->nomor_rw}, {$rt->rw->kelurahan->nama_kelurahan}, {$rt->rw->kelurahan->kecamatan->nama_kecamatan}"
            ]
        ]);
    }
    
    public function getMapData()
    {
        // Ambil semua RT dengan koordinat
        $rtData = Rt::with(['rw.kelurahan.kecamatan'])
                    ->whereNotNull('koordinat_lat')
                    ->whereNotNull('koordinat_lng')
                    ->get();
        
        $mapData = [];
        
        foreach ($rtData as $rt) {
            $rumahStats = $this->getRumahDataByRt($rt->id);
            
            $mapData[] = [
                'id' => $rt->id,
                'nama' => "RT {$rt->nomor_rt}, RW {$rt->rw->nomor_rw}",
                'alamat' => "{$rt->rw->kelurahan->nama_kelurahan}, {$rt->rw->kelurahan->kecamatan->nama_kecamatan}",
                'lat' => floatval($rt->koordinat_lat),
                'lng' => floatval($rt->koordinat_lng),
                'rumah_aman' => $rumahStats['aman'],
                'rumah_tidak_aman' => $rumahStats['tidak_aman'],
                'total_rumah' => $rumahStats['total'],
                'status' => $rumahStats['tidak_aman'] > 0 ? 'rawan' : 'aman'
            ];
        }
        
        return response()->json($mapData);
    }
    
    public function getChartData($period = 'bulanan')
    {
        $data = [];
        $labels = [];
        
        switch ($period) {
            case 'harian':
                // Data 7 hari terakhir
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('d M');
                    
                    $aman = TrackingHarian::whereDate('tanggal', $date)
                                         ->where('kategori_masalah', 'Aman')
                                         ->count();
                    $tidak_aman = TrackingHarian::whereDate('tanggal', $date)
                                               ->where('kategori_masalah', 'Tidak Aman')
                                               ->count();
                    
                    $data['aman'][] = $aman;
                    $data['tidak_aman'][] = $tidak_aman;
                }
                break;
                
            case 'mingguan':
                // Data 8 minggu terakhir
                for ($i = 7; $i >= 0; $i--) {
                    $startWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                    $endWeek = Carbon::now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'W' . $startWeek->weekOfYear;
                    
                    $aman = TrackingHarian::whereBetween('tanggal', [$startWeek, $endWeek])
                                         ->where('kategori_masalah', 'Aman')
                                         ->count();
                    $tidak_aman = TrackingHarian::whereBetween('tanggal', [$startWeek, $endWeek])
                                               ->where('kategori_masalah', 'Tidak Aman')
                                               ->count();
                    
                    $data['aman'][] = $aman;
                    $data['tidak_aman'][] = $tidak_aman;
                }
                break;
                
            case 'bulanan':
            default:
                // Data 6 bulan terakhir
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    
                    $aman = TrackingHarian::whereMonth('tanggal', $date->month)
                                         ->whereYear('tanggal', $date->year)
                                         ->where('kategori_masalah', 'Aman')
                                         ->count();
                    $tidak_aman = TrackingHarian::whereMonth('tanggal', $date->month)
                                               ->whereYear('tanggal', $date->year)
                                               ->where('kategori_masalah', 'Tidak Aman')
                                               ->count();
                    
                    $data['aman'][] = $aman;
                    $data['tidak_aman'][] = $tidak_aman;
                }
                break;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rumah Aman',
                    'data' => $data['aman'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Rumah Tidak Aman',
                    'data' => $data['tidak_aman'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2
                ]
            ]
        ];
    }
    
    public function updateChart(Request $request)
    {
        $period = $request->get('period', 'bulanan');
        $chartData = $this->getChartData($period);
        
        return response()->json($chartData);
    }
    
    private function getRumahStats()
    {
        $stats = new \stdClass();
        $stats->aman = TrackingHarian::where('kategori_masalah', 'Aman')->count();
        $stats->tidak_aman = TrackingHarian::where('kategori_masalah', 'Tidak Aman')->count();
        $stats->belum_dicek = TrackingHarian::where('kategori_masalah', 'Belum Dicek')
                                          ->orWhereNull('kategori_masalah')
                                          ->count();
        
        return $stats;
    }
    
    private function getRawanAreas()
    {
        return DB::table('rt')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan', 'rw.kelurahan_id', '=', 'kelurahan.id')
            ->join('kecamatan', 'kelurahan.kecamatan_id', '=', 'kecamatan.id')
            ->leftJoin('warga', 'rt.id', '=', 'warga.rt_id')
            ->leftJoin('tracking_harian', function($join) {
                $join->on('warga.id', '=', 'tracking_harian.warga_id')
                     ->where('tracking_harian.kategori_masalah', '=', 'Tidak Aman');
            })
            ->select(
                DB::raw("CONCAT('RT ', rt.nomor_rt, ', RW ', rw.nomor_rw, ', ', kelurahan.nama_kelurahan) as wilayah"),
                'rt.koordinat_lat',
                'rt.koordinat_lng',
                DB::raw('COUNT(DISTINCT warga.id) as total_rumah'),
                DB::raw('COUNT(DISTINCT tracking_harian.id) as rumah_tidak_aman')
            )
            ->whereNotNull('rt.koordinat_lat')
            ->whereNotNull('rt.koordinat_lng')
            ->groupBy('rt.id', 'rt.nomor_rt', 'rw.nomor_rw', 'kelurahan.nama_kelurahan', 'rt.koordinat_lat', 'rt.koordinat_lng')
            ->having('total_rumah', '>', 0)
            ->orderBy('rumah_tidak_aman', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getRumahDataByRt($rtId)
    {
        $totalRumah = Warga::where('rt_id', $rtId)->count();
        
        $aman = TrackingHarian::join('warga', 'tracking_harian.warga_id', '=', 'warga.id')
                              ->where('warga.rt_id', $rtId)
                              ->where('tracking_harian.kategori_masalah', 'Aman')
                              ->count();
                              
        $tidak_aman = TrackingHarian::join('warga', 'tracking_harian.warga_id', '=', 'warga.id')
                                   ->where('warga.rt_id', $rtId)
                                   ->where('tracking_harian.kategori_masalah', 'Tidak Aman')
                                   ->count();
        
        return [
            'total' => $totalRumah,
            'aman' => $aman,
            'tidak_aman' => $tidak_aman,
            'belum_dicek' => $totalRumah - ($aman + $tidak_aman)
        ];
    }
}
