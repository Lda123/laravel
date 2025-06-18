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
        $query = Warga::with(['rt.rw.kelurahan', 'trackingHarians' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filter by location if any location parameter is provided
        if ($request->filled('rt')) {
            $query->where('rt_id', $request->rt);
        } elseif ($request->filled('rw')) {
            $rts = Rt::where('rw_id', $request->rw)->pluck('id');
            $query->whereIn('rt_id', $rts);
        } elseif ($request->filled('kelurahan')) {
            $rws = Rw::where('kelurahan_id', $request->kelurahan)->pluck('id');
            $rts = Rt::whereIn('rw_id', $rws)->pluck('id');
            $query->whereIn('rt_id', $rts);
        } elseif ($request->filled('kecamatan')) {
            $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan)->pluck('id');
            $rws = Rw::whereIn('kelurahan_id', $kelurahans)->pluck('id');
            $rts = Rt::whereIn('rw_id', $rws)->pluck('id');
            $query->whereIn('rt_id', $rts);
        }

        $wargas = $query->paginate(20);

        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        $kelurahans = $request->filled('kecamatan') 
            ? Kelurahan::where('kecamatan_id', $request->kecamatan)->orderBy('nama_kelurahan')->get() 
            : collect();
        $rws = $request->filled('kelurahan') 
            ? Rw::where('kelurahan_id', $request->kelurahan)->orderBy('nomor_rw')->get() 
            : collect();
        $rts = $request->filled('rw') 
            ? Rt::where('rw_id', $request->rw)->orderBy('nomor_rt')->get() 
            : collect();

        return view('kader.data_warga', compact(
            'wargas', 
            'kecamatans', 
            'kelurahans', 
            'rws', 
            'rts'
        ));
    }

    public function getKelurahan(Request $request)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
            ->orderBy('nama_kelurahan')
            ->get();
        
        $options = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahans as $kelurahan) {
            $options .= "<option value='{$kelurahan->id}'>{$kelurahan->nama_kelurahan}</option>";
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRw(Request $request)
    {
        $rws = Rw::where('kelurahan_id', $request->kelurahan_id)
            ->orderBy('nomor_rw')
            ->get();
        
        $options = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $options .= "<option value='{$rw->id}'>RW ".str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT)."</option>";
        }
        
        return response()->json(['options' => $options]);
    }

    public function getRt(Request $request)
    {
        $rts = Rt::where('rw_id', $request->rw_id)
            ->orderBy('nomor_rt')
            ->get();
        
        $options = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $options .= "<option value='{$rt->id}'>RT ".str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT)."</option>";
        }
        
        return response()->json(['options' => $options]);
    }
}