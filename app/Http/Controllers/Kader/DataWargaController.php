<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Warga;
use Illuminate\Http\Request;

class DataWargaController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
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
        
        // Build query
        $query = Warga::with([
            'rt.rw.kelurahan.kecamatan',
            'trackingHarians' => function($q) {
                $q->latest()->limit(1);
            }
        ]);
        
        // Apply filters
        if ($request->filled('kecamatan')) {
            $query->whereHas('rt.rw.kelurahan.kecamatan', function($q) use ($request) {
                $q->where('id', $request->kecamatan);
            });
        }
        
        if ($request->filled('kelurahan')) {
            $query->whereHas('rt.rw.kelurahan', function($q) use ($request) {
                $q->where('id', $request->kelurahan);
            });
        }
        
        if ($request->filled('rw')) {
            $query->whereHas('rt.rw', function($q) use ($request) {
                $q->where('id', $request->rw);
            });
        }
        
        if ($request->filled('rt')) {
            $query->where('rt_id', $request->rt);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%");
            });
        }
        
        // Get paginated results
        $wargas = $query->orderBy('nama_lengkap')->paginate(15);
        
        return view('kader.data_warga', compact(
            'kecamatans',
            'kelurahans', 
            'rws',
            'rts',
            'wargas'
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

    public function getWilayahKoordinat(Request $request)
    {
        // Implementasi pencarian koordinat berdasarkan wilayah
        // Contoh sederhana:
        $kecamatan = Kecamatan::find($request->kecamatan_id);
        $kelurahan = Kelurahan::find($request->kelurahan_id);
        $rw = Rw::find($request->rw_id);
        $rt = Rt::find($request->rt_id);
        
        if (!$kecamatan) {
            return response()->json([
                'success' => false,
                'message' => 'Kecamatan tidak ditemukan'
            ]);
        }
        
        // Contoh sederhana - sesuaikan dengan struktur database Anda
        $lat = $rt->koordinat_lat ?? $rw->koordinat_lat ?? $kelurahan->koordinat_lat ?? $kecamatan->koordinat_lat ?? -7.2575;
        $lng = $rt->koordinat_lng ?? $rw->koordinat_lng ?? $kelurahan->koordinat_lng ?? $kecamatan->koordinat_lng ?? 112.7521;
        
        $nama_wilayah = '';
        if ($rt) $nama_wilayah = "RT {$rt->nomor_rt}";
        if ($rw) $nama_wilayah .= $nama_wilayah ? "/RW {$rw->nomor_rw}" : "RW {$rw->nomor_rw}";
        if ($kelurahan) $nama_wilayah .= ", {$kelurahan->nama_kelurahan}";
        if ($kecamatan) $nama_wilayah .= ", {$kecamatan->nama_kecamatan}";
        
        return response()->json([
            'success' => true,
            'lat' => $lat,
            'lng' => $lng,
            'nama_wilayah' => $nama_wilayah
        ]);
    }

}