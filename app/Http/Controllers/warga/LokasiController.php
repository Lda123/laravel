<?php

namespace App\Http\Controllers\warga;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\TrackingHarian;


class LokasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $period = request('period', 'mingguan');
        
        // Get user location data
        $userLocation = $this->getUserLocation($user);
        
        // Get tracking data
        $trackingData = $this->getTrackingData();
        
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get case data for chart
        $chartData = $this->getChartData($period);
        
        // Get high risk areas
        $rawanAreas = $this->getHighRiskAreas();
        
        // Get kecamatan list
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        
        return view('warga.lokasi', [    // Updated to use warga.lokasi view
            'userLocation' => $userLocation,
            'trackingData' => $trackingData,
            'stats' => $stats,
            'chartLabels' => $chartData['labels'],
            'chartValues' => $chartData['values'],
            'rawanAreas' => $rawanAreas,
            'kecamatans' => $kecamatans,
            'period' => $period
        ]);
    }

    private function getUserLocation($user)
    {
        return [
            'lat' => $user->rt->koordinat_lat ?? -7.2575,
            'lng' => $user->rt->koordinat_lng ?? 112.7521,
            'title' => 'Lokasi Anda (RT ' . ($user->rt->nomor_rt ?? '') . '/RW ' . ($user->rt->rw->nomor_rw ?? '') . ')',
            'rt' => $user->rt->nomor_rt ?? '',
            'rw' => $user->rt->rw->nomor_rw ?? '',
            'kelurahan' => $user->rt->rw->kelurahan->nama_kelurahan ?? '',
            'kecamatan' => $user->rt->rw->kelurahan->kecamatan->nama_kecamatan ?? '',
            'rt_id' => $user->rt->id ?? null,
            'rw_id' => $user->rt->rw->id ?? null,
            'kelurahan_id' => $user->rt->rw->kelurahan->id ?? null,
            'kecamatan_id' => $user->rt->rw->kelurahan->kecamatan->id ?? null
        ];
    }

    private function getTrackingData()
    {
        return TrackingHarian::with(['warga.rt.rw.kelurahan.kecamatan'])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'lat' => $item->warga->rt->koordinat_lat ?? -7.2575 + (mt_rand(-100, 100) / 1000),
                    'lng' => $item->warga->rt->koordinat_lng ?? 112.7521 + (mt_rand(-100, 100) / 1000),
                    'status_lingkungan' => $item->status_lingkungan,
                    'status_kesehatan' => $item->status_kesehatan,
                    'kategori_masalah' => $item->kategori_masalah,
                    'deskripsi' => $item->deskripsi,
                    'tanggal_pantau' => $item->tanggal_pantau,
                    'rt' => $item->warga->rt->nomor_rt,
                    'rw' => $item->warga->rt->rw->nomor_rw,
                    'kelurahan' => $item->warga->rt->rw->kelurahan->nama_kelurahan,
                    'kecamatan' => $item->warga->rt->rw->kelurahan->kecamatan->nama_kecamatan,
                    'nama_warga' => $item->warga->nama_lengkap,
                    'rt_id' => $item->warga->rt->id,
                    'rw_id' => $item->warga->rt->rw->id,
                    'kelurahan_id' => $item->warga->rt->rw->kelurahan->id,
                    'kecamatan_id' => $item->warga->rt->rw->kelurahan->kecamatan->id
                ];
            });
    }

    private function getStatistics()
    {
        return [
            'aman' => TrackingHarian::where('kategori_masalah', 'Aman')->count(),
            'tidak_aman' => TrackingHarian::where('kategori_masalah', 'Tidak Aman')->count(),
            'belum_dicek' => TrackingHarian::where('kategori_masalah', 'Belum Dicek')->count()
        ];
    }

    private function getChartData($period)
    {
        if ($period == 'harian') {
            $data = TrackingHarian::select(DB::raw('DATE(tanggal_pantau) as label'), DB::raw('COUNT(*) as value'))
                ->where('status_kesehatan', 'Terkena DBD')
                ->groupBy(DB::raw('DATE(tanggal_pantau)'))
                ->orderBy('label', 'DESC')
                ->limit(7)
                ->get();
        } elseif ($period == 'bulanan') {
            $data = TrackingHarian::select(DB::raw("DATE_FORMAT(tanggal_pantau, '%Y-%m') as label"), DB::raw('COUNT(*) as value'))
                ->where('status_kesehatan', 'Terkena DBD')
                ->groupBy(DB::raw("DATE_FORMAT(tanggal_pantau, '%Y-%m')"))
                ->orderBy('label', 'DESC')
                ->limit(6)
                ->get();
        } else {
            // Default to weekly
            $data = TrackingHarian::select(DB::raw('YEARWEEK(tanggal_pantau) as label'), DB::raw('COUNT(*) as value'))
                ->where('status_kesehatan', 'Terkena DBD')
                ->groupBy(DB::raw('YEARWEEK(tanggal_pantau)'))
                ->orderBy('label', 'DESC')
                ->limit(4)
                ->get();
        }

        return [
            'labels' => $data->pluck('label')->toArray(),
            'values' => $data->pluck('value')->toArray()
        ];
    }

    private function getHighRiskAreas()
    {
        return DB::table('tracking_harian as th')
            ->select(
                DB::raw("CONCAT(kec.nama_kecamatan, ', ', kel.nama_kelurahan, ', RW ', rw.nomor_rw, ', RT ', rt.nomor_rt) as wilayah"),
                'kec.nama_kecamatan', 'kel.nama_kelurahan', 'rw.nomor_rw', 'rt.nomor_rt',
                'rt.koordinat_lat', 'rt.koordinat_lng',
                'rt.id as rt_id', 'rw.id as rw_id', 'kel.id as kelurahan_id', 'kec.id as kecamatan_id',
                DB::raw("COUNT(CASE WHEN th.kategori_masalah = 'Tidak Aman' THEN 1 END) as rumah_tidak_aman"),
                DB::raw("COUNT(CASE WHEN th.status_kesehatan = 'Terkena DBD' THEN 1 END) as kasus_dbd"),
                DB::raw("COUNT(*) as total_rumah")
            )
            ->join('warga as w', 'th.warga_id', '=', 'w.id')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->groupBy('kec.nama_kecamatan', 'kel.nama_kelurahan', 'rw.nomor_rw', 'rt.nomor_rt', 'rt.koordinat_lat', 'rt.koordinat_lng')
            ->havingRaw('rumah_tidak_aman > 0 OR kasus_dbd > 0')
            ->orderByRaw('rumah_tidak_aman DESC, kasus_dbd DESC')
            ->limit(6)
            ->get()
            ->toArray();
    }

    public function getWilayahCoordinates(Request $request)
    {
        $query = Rt::query();
        
        if ($request->rt_id) {
            $query->where('id', $request->rt_id);
        } elseif ($request->rw_id) {
            $query->where('rw_id', $request->rw_id);
        } elseif ($request->kelurahan_id) {
            $query->whereHas('rw', function($q) use ($request) {
                $q->where('kelurahan_id', $request->kelurahan_id);
            });
        } elseif ($request->kecamatan_id) {
            $query->whereHas('rw.kelurahan', function($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            });
        }
        
        $rt = $query->first();
        
        if ($rt) {
            return response()->json([
                'success' => true,
                'lat' => $rt->koordinat_lat,
                'lng' => $rt->koordinat_lng,
                'nama_wilayah' => $rt->rw->kelurahan->kecamatan->nama_kecamatan . ', ' . 
                                  $rt->rw->kelurahan->nama_kelurahan . ', RW ' . 
                                  $rt->rw->nomor_rw . ', RT ' . $rt->nomor_rt
            ]);
        }
        
        return response()->json(['success' => false]);
    }

    public function getKelurahan(Request $request)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
            ->orderBy('nama_kelurahan')
            ->get();
        
        $options = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahans as $kelurahan) {
            $options .= '<option value="'.$kelurahan->id.'">'.$kelurahan->nama_kelurahan.'</option>';
        }
        
        return $options;
    }

    public function getRw(Request $request)
    {
        $rws = Rw::where('kelurahan_id', $request->kelurahan_id)
            ->orderBy('nomor_rw')
            ->get();
        
        $options = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $options .= '<option value="'.$rw->id.'">'.$rw->nomor_rw.'</option>';
        }
        
        return $options;
    }

    public function getRt(Request $request)
    {
        $rts = Rt::where('rw_id', $request->rw_id)
            ->orderBy('nomor_rt')
            ->get();
        
        $options = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $options .= '<option value="'.$rt->id.'">'.$rt->nomor_rt.'</option>';
        }
        
        return $options;
    }
}