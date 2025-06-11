<?php

namespace App\Http\Controllers\warga\fitur_utama;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function index()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        return view('warga.laporan', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'kelurahan_id' => 'required|exists:kelurahan,id',
            'rw_id' => 'required|exists:rw,id',
            'rt_id' => 'required|exists:rt,id',
            'jenis_laporan' => 'required|in:Jentik Nyamuk,Kasus DBD,Lingkungan Kotor',
            'deskripsi' => 'required|string|min:10',
            'alamat_detail' => 'required|string',
            'foto_pelaporan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();
        $data['warga_id'] = auth('warga')->id();
        $data['status'] = Laporan::STATUS_PENDING;

        // Handle file upload
        if ($request->hasFile('foto_pelaporan')) {
            $file = $request->file('foto_pelaporan');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Create directory if doesn't exist
            $path = public_path('laporan_warga');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            // Move file to public/laporan_warga
            $file->move($path, $filename);
            $data['foto_pelaporan'] = $filename;
        }

        Laporan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim!'
        ]);
    }

    // AJAX endpoints for hierarchical dropdowns
    public function getKelurahan(Request $request)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
                               ->orderBy('nama_kelurahan')
                               ->get();
        
        $html = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahans as $kelurahan) {
            $html .= '<option value="' . $kelurahan->id . '">' . $kelurahan->nama_kelurahan . '</option>';
        }
        
        return response($html);
    }

    public function getRw(Request $request)
    {
        $rws = Rw::where('kelurahan_id', $request->kelurahan_id)
                 ->orderBy('nama_rw')
                 ->get();
        
        $html = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $html .= '<option value="' . $rw->id . '">' . $rw->nama_rw . '</option>';
        }
        
        return response($html);
    }

    public function getRt(Request $request)
    {
        $rts = Rt::where('rw_id', $request->rw_id)
                 ->orderBy('nama_rt')
                 ->get();
        
        $html = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $html .= '<option value="' . $rt->id . '">' . $rt->nama_rt . '</option>';
        }
        
        return response($html);
    }

    public function history()
    {
        $laporans = Laporan::where('warga_id', auth('warga')->id())
                          ->with(['kecamatan', 'kelurahan', 'rw', 'rt'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('warga.laporan', compact('laporans'));
    }
}