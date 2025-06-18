<?php

namespace App\Http\Controllers\Warga\fitur_utama;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();
        return view('warga.laporan.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_laporan' => 'required|in:Jentik Nyamuk,Kasus DBD,Lingkungan Kotor',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'kelurahan_id' => 'required|exists:kelurahan,id',
            'rw_id' => 'required|exists:rw,id',
            'rt_id' => 'required|exists:rt,id',
            'alamat_detail' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'foto_pelaporan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle file upload
        $fotoPath = null;
        if ($request->hasFile('foto_pelaporan')) {
            $wargaId = Auth::id();
            $reportCount = Laporan::where('warga_id', $wargaId)->count() + 1;
            $extension = $request->file('foto_pelaporan')->getClientOriginalExtension();
            $filename = "{$wargaId}_pelaporan_{$reportCount}.{$extension}";
            
            // Simpan file ke folder public/laporan_warga
            $fotoPath = $request->file('foto_pelaporan')->storeAs(
                'laporan_warga', 
                $filename,
                'public'
            );
        }

        // Create new report
        $laporan = Laporan::create([
            'warga_id' => Auth::id(),
            'jenis_laporan' => $validated['jenis_laporan'],
            'kecamatan_id' => $validated['kecamatan_id'],
            'kelurahan_id' => $validated['kelurahan_id'],
            'rw_id' => $validated['rw_id'],
            'rt_id' => $validated['rt_id'],
            'alamat_detail' => $validated['alamat_detail'],
            'deskripsi' => $validated['deskripsi'],
            'status' => Laporan::STATUS_PENDING,
            'foto_pelaporan' => $fotoPath,
        ]);

        return redirect()->route('warga.laporan.index')
            ->with('success', 'Laporan berhasil dikirim! Kami akan segera memprosesnya.');
    }

    public function index()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();
        $laporans = Laporan::where('warga_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('warga.laporan', compact('laporans', 'kecamatans'));
    }

    // AJAX methods for dropdowns
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
}