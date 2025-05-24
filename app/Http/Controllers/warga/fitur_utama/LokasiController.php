<?php

namespace App\Http\Controllers\warga\fitur_utama;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{

    public function index(Request $request)
    {
        $id_warga = Auth::guard('warga')->id();
        $period = $request->get('period', 'mingguan');

        // Get user's location data
        $user_data = DB::table('warga as w')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select('w.*', 'rt.nomor_rt', 'rt.rw_id', 'rw.nomor_rw', 'rw.kelurahan_id',
                    'kel.nama_kelurahan', 'kel.kecamatan_id', 'kec.nama_kecamatan',
                    'rt.koordinat_lat', 'rt.koordinat_lng')
            ->where('w.id', $id_warga)
            ->first();

        // Tracking data for map markers
        $tracking_data = DB::table('tracking_harian as th')
            ->join('warga as w', 'th.warga_id', '=', 'w.id')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select('th.*', 'w.nama_lengkap',
                    'rt.id as rt_id', 'rt.nomor_rt', 'rt.koordinat_lat', 'rt.koordinat_lng',
                    'rw.id as rw_id', 'rw.nomor_rw',
                    'kel.id as kelurahan_id', 'kel.nama_kelurahan',
                    'kec.id as kecamatan_id', 'kec.nama_kecamatan')
            ->get()
            ->map(function($row) {
                return [
                    'id' => $row->id,
                    'lat' => $row->koordinat_lat ?: -7.2575 + (mt_rand(-100, 100) / 1000),
                    'lng' => $row->koordinat_lng ?: 112.7521 + (mt_rand(-100, 100) / 1000),
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

        // Data grafik kasus per waktu
        $case_data = $this->getCaseData($period);

        // Area rawan (berdasarkan kategori_masalah)
        $rawan_areas = DB::table('tracking_harian as th')
            ->join('warga as w', 'th.warga_id', '=', 'w.id')
            ->join('rt', 'w.rt_id', '=', 'rt.id')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->selectRaw("
                CONCAT(kec.nama_kecamatan, ', ', kel.nama_kelurahan, ', RW ', rw.nomor_rw, ', RT ', rt.nomor_rt) as wilayah,
                kec.nama_kecamatan, kel.nama_kelurahan, rw.nomor_rw, rt.nomor_rt,
                rt.koordinat_lat, rt.koordinat_lng,
                rt.id as rt_id, rw.id as rw_id, kel.id as kelurahan_id, kec.id as kecamatan_id,
                COUNT(CASE WHEN th.kategori_masalah = 'Tidak Aman' THEN 1 END) as rumah_tidak_aman,
                COUNT(*) as total_rumah
            ")
            ->groupBy('kec.nama_kecamatan', 'kel.nama_kelurahan', 'rw.nomor_rw', 'rt.nomor_rt', 
                      'rt.koordinat_lat', 'rt.koordinat_lng', 'rt.id', 'rw.id', 'kel.id', 'kec.id')
            ->havingRaw('rumah_tidak_aman > 0')
            ->orderByDesc('rumah_tidak_aman')
            ->limit(6)
            ->get();

        $kecamatan_options = DB::table('kecamatan')
            ->orderBy('nama_kecamatan')
            ->get();

        $user_location = [
            'lat' => $user_data->koordinat_lat ?: -7.2575,
            'lng' => $user_data->koordinat_lng ?: 112.7521,
            'title' => 'Lokasi Anda (RT ' . $user_data->nomor_rt . '/RW ' . $user_data->nomor_rw . ')',
            'rt' => $user_data->nomor_rt,
            'rw' => $user_data->nomor_rw,
            'kelurahan' => $user_data->nama_kelurahan,
            'kecamatan' => $user_data->nama_kecamatan
        ];

        return view('warga.lokasi', compact(
            'tracking_data', 'rawan_areas', 'user_location', 'stats', 
            'case_data', 'kecamatan_options', 'period'
        ));
    }

    private function getCaseData($period)
    {
        switch ($period) {
            case 'harian':
                return DB::table('tracking_harian')
                    ->selectRaw('DATE(tanggal) as tanggal, COUNT(*) as jumlah')
                    ->where('kategori_masalah', 'Tidak Aman')
                    ->groupByRaw('DATE(tanggal)')
                    ->orderByDesc('tanggal')
                    ->limit(7)
                    ->get();

            case 'bulanan':
                return DB::table('tracking_harian')
                    ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as jumlah")
                    ->where('kategori_masalah', 'Tidak Aman')
                    ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
                    ->orderByDesc('bulan')
                    ->limit(6)
                    ->get();

            default: // mingguan
                return DB::table('tracking_harian')
                    ->selectRaw('YEARWEEK(tanggal) as minggu, COUNT(*) as jumlah')
                    ->where('kategori_masalah', 'Tidak Aman')
                    ->groupByRaw('YEARWEEK(tanggal)')
                    ->orderByDesc('minggu')
                    ->limit(4)
                    ->get();
        }
    }

    public function getWilayahCoordinates(Request $request)
    {
        $kecamatan_id = $request->kecamatan_id;
        $kelurahan_id = $request->kelurahan_id;
        $rw_id = $request->rw_id;
        $rt_id = $request->rt_id;

        $query = DB::table('rt')
            ->join('rw', 'rt.rw_id', '=', 'rw.id')
            ->join('kelurahan as kel', 'rw.kelurahan_id', '=', 'kel.id')
            ->join('kecamatan as kec', 'kel.kecamatan_id', '=', 'kec.id')
            ->select('rt.koordinat_lat as lat', 'rt.koordinat_lng as lng',
                    DB::raw("CONCAT(kec.nama_kecamatan, ', ', kel.nama_kelurahan, ', RW ', rw.nomor_rw, ', RT ', rt.nomor_rt) as nama_wilayah"));

        if ($rt_id) {
            $query->where('rt.id', $rt_id);
        } elseif ($rw_id) {
            $query->where('rw.id', $rw_id);
        } elseif ($kelurahan_id) {
            $query->where('kel.id', $kelurahan_id);
        } elseif ($kecamatan_id) {
            $query->where('kec.id', $kecamatan_id);
        }

        $result = $query->first();

        if ($result) {
            return response()->json([
                'success' => true,
                'lat' => $result->lat ?: -7.2575,
                'lng' => $result->lng ?: 112.7521,
                'nama_wilayah' => $result->nama_wilayah
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function getKelurahan(Request $request)
    {
        $kelurahan = DB::table('kelurahan')
            ->where('kecamatan_id', $request->kecamatan_id)
            ->orderBy('nama_kelurahan')
            ->get();

        $options = "<option value=''>Pilih Kelurahan</option>";
        foreach ($kelurahan as $item) {
            $options .= "<option value='{$item->id}'>{$item->nama_kelurahan}</option>";
        }

        return response($options);
    }

    public function getRw(Request $request)
    {
        $rw = DB::table('rw')
            ->where('kelurahan_id', $request->kelurahan_id)
            ->orderBy('nomor_rw')
            ->get();

        $options = "<option value=''>Pilih RW</option>";
        foreach ($rw as $item) {
            $options .= "<option value='{$item->id}'>RW {$item->nomor_rw}</option>";
        }

        return response($options);
    }

    public function getRt(Request $request)
    {
        $rt = DB::table('rt')
            ->where('rw_id', $request->rw_id)
            ->orderBy('nomor_rt')
            ->get();

        $options = "<option value=''>Pilih RT</option>";
        foreach ($rt as $item) {
            $options .= "<option value='{$item->id}'>RT {$item->nomor_rt}</option>";
        }

        return response($options);
    }
}
