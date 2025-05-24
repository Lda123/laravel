<?php
namespace App\Http\Controllers\warga\register;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;

class AjaxController extends Controller
{
    public function getKelurahan(Request $request)
    {
        $kelurahan = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
                              ->orderBy('nama_kelurahan', 'asc')
                              ->get();
    
        return response()->json([
            'options' => view('partials.options.kelurahan', compact('kelurahan'))->render()
        ]);
    }
    

    public function getRw(Request $request)
    {
        $rw = Rw::where('kelurahan_id', $request->kelurahan_id)
                    ->orderBy('nomor_rw', 'asc')->get();

        return response()->json([
            'options' => view('partials.options.rw', compact('rw'))->render()
        ]);
    }

    public function getRt(Request $request)
    {
        $rt = Rt::where('rw_id', $request->rw_id)
                    ->orderBy('nomor_rt', 'asc')->get();

        return response()->json([
            'options' => view('partials.options.rt', compact('rt'))->render()
        ]);
    }
}
