<?php

namespace App\Http\Controllers\warga\fitur_utama;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\models\Rt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $id_warga = Auth::guard('warga')->id();
        $period = $request->get('period', 'mingguan');

        // Data koordinat default Surabaya
        $defaultLat = -7.2575;
        $defaultLng = 112.7521;

        // Get user's location data with fallback to default coordinates
        $user_data = DB::table('warga as w')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select(
                'w.*', 
                'rt.nomor_rt', 
                'rt.rw_id', 
                'rw.nomor_rw', 
                'rw.kelurahan_id',
                'kel.nama_kelurahan', 
                'kel.kecamatan_id', 
                'kec.nama_kecamatan',
                DB::raw('COALESCE(rt.koordinat_lat, '.$defaultLat.') as koordinat_lat'),
                DB::raw('COALESCE(rt.koordinat_lng, '.$defaultLng.') as koordinat_lng')
            )
            ->where('w.id', $id_warga)
            ->first();

        // Tracking data with proper coordinates handling
        $tracking_data = DB::table('tracking_harian as th')
            ->join('warga as w', 'th.warga_id', '=', 'w.id')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select(
                'th.*', 
                'w.nama_lengkap',
                'rt.id as rt_id', 
                'rt.nomor_rt', 
                DB::raw('COALESCE(rt.koordinat_lat, '.$defaultLat.') as koordinat_lat'),
                DB::raw('COALESCE(rt.koordinat_lng, '.$defaultLng.') as koordinat_lng'),
                'rw.id as rw_id', 
                'rw.nomor_rw',
                'kel.id as kelurahan_id', 
                'kel.nama_kelurahan',
                'kec.id as kecamatan_id', 
                'kec.nama_kecamatan'
            )
            ->whereNotNull('rt.koordinat_lat')
            ->whereNotNull('rt.koordinat_lng')
            ->get()
            ->map(function($row) {
                return [
                    'id' => $row->id,
                    'lat' => (float)$row->koordinat_lat,
                    'lng' => (float)$row->koordinat_lng,
                    'kategori_masalah' => $row->kategori_masalah,
                    'deskripsi' => $row->deskripsi,
                    'tanggal' => $row->tanggal,
                    'rt' => $row->nomor_rt,
                    'rw' => $row->nomor_rw,
                    'kelurahan' => $row->nama_kelurahan,
                    'kecamatan' => $row->nama_kecamatan,
                    'nama_warga' => $row->nama_lengkap,
                    'rt_id' => $row->rt_id,
                    'rw_id' => $row->rw_id,
                    'kelurahan_id' => $row->kelurahan_id,
                    'kecamatan_id' => $row->kecamatan_id
                ];
            });

        // Statistik kategori masalah
        $stats = DB::table('tracking_harian')
            ->selectRaw("
                COUNT(CASE WHEN kategori_masalah = 'Aman' THEN 1 END) as aman,
                COUNT(CASE WHEN kategori_masalah = 'Tidak Aman' THEN 1 END) as tidak_aman,
                COUNT(CASE WHEN kategori_masalah = 'Belum Dicek' THEN 1 END) as belum_dicek
            ")
            ->first();

        // Data grafik kasus per waktu dengan format yang lebih rapi
        $case_data = $this->getCaseData($period);

        // Area rawan dengan koordinat yang valid
        $rawan_areas = DB::table('tracking_harian as th')
            ->join('warga as w', 'th.warga_id', '=', 'w.id')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->selectRaw("
                CONCAT(kec.nama_kecamatan, ', ', kel.nama_kelurahan, ', RW ', rw.nomor_rw, ', RT ', rt.nomor_rt) as wilayah,
                kec.nama_kecamatan, 
                kel.nama_kelurahan, 
                rw.nomor_rw, 
                rt.nomor_rt,
                COALESCE(rt.koordinat_lat, ".$defaultLat.") as koordinat_lat,
                COALESCE(rt.koordinat_lng, ".$defaultLng.") as koordinat_lng,
                rt.id as rt_id, 
                rw.id as rw_id, 
                kel.id as kelurahan_id, 
                kec.id as kecamatan_id,
                COUNT(CASE WHEN th.kategori_masalah = 'Tidak Aman' THEN 1 END) as rumah_tidak_aman,
                COUNT(*) as total_rumah
            ")
            ->whereNotNull('rt.koordinat_lat')
            ->whereNotNull('rt.koordinat_lng')
            ->groupBy(
                'kec.nama_kecamatan', 
                'kel.nama_kelurahan', 
                'rw.nomor_rw', 
                'rt.nomor_rt', 
                'rt.koordinat_lat', 
                'rt.koordinat_lng', 
                'rt.id', 
                'rw.id', 
                'kel.id', 
                'kec.id'
            )
            ->havingRaw('rumah_tidak_aman > 0')
            ->orderByDesc('rumah_tidak_aman')
            ->limit(10)
            ->get();

        // Get kecamatan options
        $kecamatan_options = DB::table('kecamatan')
            ->orderBy('nama_kecamatan')
            ->get();

        // User location data
        $user_location = [
            'lat' => (float)$user_data->koordinat_lat,
            'lng' => (float)$user_data->koordinat_lng,
            'title' => 'Lokasi Anda (RT ' . $user_data->nomor_rt . '/RW ' . $user_data->nomor_rw . ')',
            'rt' => $user_data->nomor_rt,
            'rw' => $user_data->nomor_rw,
            'kelurahan' => $user_data->nama_kelurahan,
            'kecamatan' => $user_data->nama_kecamatan
        ];

        return view('warga.lokasi', compact(
            'tracking_data', 
            'rawan_areas', 
            'user_location', 
            'stats', 
            'case_data', 
            'kecamatan_options', 
            'period',
            'defaultLat',
            'defaultLng'
        ));
    }

    private function getCaseData($period)
    {
        $query = DB::table('tracking_harian')
            ->where('kategori_masalah', 'Tidak Aman');

        switch ($period) {
            case 'harian':
                return $query->selectRaw('DATE(tanggal) as periode, COUNT(*) as jumlah')
                    ->groupByRaw('DATE(tanggal)')
                    ->orderByRaw('DATE(tanggal) DESC')
                    ->limit(7)
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => date('d M', strtotime($item->periode)),
                            'value' => $item->jumlah
                        ];
                    });

            case 'bulanan':
                return $query->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as periode, COUNT(*) as jumlah")
                    ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
                    ->orderByRaw("DATE_FORMAT(tanggal, '%Y-%m') DESC")
                    ->limit(6)
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => date('M Y', strtotime($item->periode . '-01')),
                            'value' => $item->jumlah
                        ];
                    });

            default: // mingguan
                return $query->selectRaw('YEARWEEK(tanggal) as periode, COUNT(*) as jumlah')
                    ->groupByRaw('YEARWEEK(tanggal)')
                    ->orderByRaw('YEARWEEK(tanggal) DESC')
                    ->limit(4)
                    ->get()
                    ->map(function($item) {
                        $year = substr($item->periode, 0, 4);
                        $week = substr($item->periode, 4, 2);
                        return [
                            'label' => 'Minggu ' . $week,
                            'value' => $item->jumlah
                        ];
                    });
        }
    }

    public function getWilayahCoordinates(Request $request)
    {
        $query = DB::table('rt')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select(
                'rt.koordinat_lat as lat', 
                'rt.koordinat_lng as lng',
                DB::raw("CONCAT(kec.nama_kecamatan, ', ', kel.nama_kelurahan, ', RW ', rw.nomor_rw, ', RT ', rt.nomor_rt) as nama_wilayah")
            )
            ->whereNotNull('rt.koordinat_lat')
            ->whereNotNull('rt.koordinat_lng');

        if ($request->rt_id) {
            $query->where('rt.id', $request->rt_id);
        } elseif ($request->rw_id) {
            $query->where('rw.id', $request->rw_id);
        } elseif ($request->kelurahan_id) {
            $query->where('kel.id', $request->kelurahan_id);
        } elseif ($request->kecamatan_id) {
            $query->where('kec.id', $request->kecamatan_id);
        } else {
            return response()->json(['success' => false, 'message' => 'Pilih wilayah terlebih dahulu']);
        }

        $result = $query->first();

        if ($result) {
            return response()->json([
                'success' => true,
                'lat' => (float)$result->lat,
                'lng' => (float)$result->lng,
                'nama_wilayah' => $result->nama_wilayah
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Koordinat wilayah tidak ditemukan']);
    }

    public function getKelurahan(Request $request) {
        $kelurahan = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
                    ->orderBy('nama_kelurahan')
                    ->get();
        
        if ($kelurahan->isEmpty()) {
            return response()->json(['options' => '<option value="">Tidak ada kelurahan</option>']);
        }
        
        $options = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahan as $item) {
            $options .= '<option value="'.$item->id.'">'.$item->nama_kelurahan.'</option>';
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRw(Request $request)
    {
        $rws = Rw::where('kelurahan_id', $request->kelurahan_id)
            ->orderBy('nomor_rw', 'asc')
            ->get();
        
        if ($rws->isEmpty()) {
            return response()->json(['options' => '<option value="">Tidak ada RW</option>']);
        }
        
        $options = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $options .= '<option value="'.$rw->id.'">RW '.$rw->nomor_rw.'</option>';
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRt(Request $request)
    {
        $rts = Rt::where('rw_id', $request->rw_id)
            ->orderBy('nomor_rt', 'asc')
            ->get();
        
        if ($rts->isEmpty()) {
            return response()->json(['options' => '<option value="">Tidak ada RT</option>']);
        }
        
        $options = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $options .= '<option value="'.$rt->id.'">RT '.$rt->nomor_rt.'</option>';
        }
        
        return response()->json(['options' => $options]);
    }

    public function updatePeriod(Request $request)
    {
        $period = $request->get('period', 'mingguan');
        $case_data = $this->getCaseData($period);
        
        return response()->json([
            'success' => true,
            'data' => $case_data
        ]);
    }
}