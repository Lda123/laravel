<?php
namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InformasiController extends Controller
{

    public function index(Request $request)
    {
    $query = Edukasi::where('kategori_pengguna', 'Warga');

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', "%{$request->search}%")
              ->orWhere('isi', 'like', "%{$request->search}%");
        });
    }

    if ($request->tipe && $request->tipe != 'all') {
        $query->where('tipe', $request->tipe);
    }

    $edukasiList = $query->orderBy('dibuat_pada', 'desc')->get();

    return view('warga.informasi', compact('edukasiList'));
}

public function show($id)
{
    $content = Edukasi::where('kategori_pengguna', 'Warga')->findOrFail($id);
    return view('warga.informasi', compact('content'));
}

}