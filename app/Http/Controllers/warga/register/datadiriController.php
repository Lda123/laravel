<?php
// app/Http/Controllers/RegistrationController.php
namespace App\Http\Controllers\warga\register;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Session;

class datadiriController extends Controller
{
    public function showDataDiriForm()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();
        return view('register.data_diri', compact('kecamatans'));
    }
public function storeDataDiri(Request $request)
{
    $validated = $request->validate([
        'nik' => 'required|string|max:16',
        'nama_lengkap' => 'required|string|max:255',
        'tempat_lahir' => 'required|string|max:255',
        'tanggal_lahir' => 'required|date|before:-18 years',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat_lengkap' => 'required|string|max:255',
        'rt_id' => 'required|exists:rt,id',
    ]);

    // Ambil detail RT -> RW -> Kelurahan -> Kecamatan
    $rt = \App\Models\Rt::with('rw.kelurahan.kecamatan')->findOrFail($validated['rt_id']);
    $rw = $rt->rw;
    $kelurahan = $rw->kelurahan;
    $kecamatan = $kelurahan->kecamatan;

    // Menyiapkan data diri
    $dataDiri = [
        'nik' => $validated['nik'],
        'nama_lengkap' => $validated['nama_lengkap'],
        'tempat_lahir' => $validated['tempat_lahir'],
        'tanggal_lahir' => $validated['tanggal_lahir'],
        'jenis_kelamin' => $validated['jenis_kelamin'],
        'alamat_lengkap' => $validated['alamat_lengkap'],
        'rt_id' => $validated['rt_id'],
        'kecamatan' => $kecamatan->nama_kecamatan,
        'kelurahan' => $kelurahan->nama_kelurahan,
        'rt_rw' => "RT {$rt->nomor_rt}/RW {$rw->nomor_rw}",
    ];

    // Periksa apakah telepon dan password sudah ada di session dan tambahkan jika ada
    if (Session::has('telepon')) {
        $dataDiri['telepon'] = Session::get('telepon');
    }
    if (Session::has('password')) {
        $dataDiri['password'] = Session::get('password');
    }

    // Menyimpan data diri ke session
    Session::put('data_diri', $dataDiri);

    // Redirect ke halaman upload foto
    return redirect()->route('register.upload-foto');
}



    public function getKelurahan(Request $request)
    {
        $kecamatan_id = $request->kecamatan_id;
        $kelurahans = \App\Models\Kelurahan::where('kecamatan_id', $kecamatan_id)
            ->orderBy('nama_kelurahan', 'asc')
            ->get();
        
        $options = '<option value="">Pilih Kelurahan</option>';
        foreach ($kelurahans as $kelurahan) {
            $options .= "<option value='{$kelurahan->id}'>{$kelurahan->nama_kelurahan}</option>";
        }
        
        return $options;
    }

    public function getRw(Request $request)
    {
        $kelurahan_id = $request->kelurahan_id;
        $rws = \App\Models\Rw::where('kelurahan_id', $kelurahan_id)
            ->orderBy('nomor_rw', 'asc')
            ->get();
        
        $options = '<option value="">Pilih RW</option>';
        foreach ($rws as $rw) {
            $options .= "<option value='{$rw->id}'>{$rw->nomor_rw}</option>";
        }
        
        return $options;
    }

    public function getRt(Request $request)
    {
        $rw_id = $request->rw_id;
        $rts = \App\Models\Rt::where('rw_id', $rw_id)
            ->orderBy('nomor_rt', 'asc')
            ->get();
        
        $options = '<option value="">Pilih RT</option>';
        foreach ($rts as $rt) {
            $options .= "<option value='{$rt->id}'>{$rt->nomor_rt}</option>";
        }
        
        return $options;
    }
}