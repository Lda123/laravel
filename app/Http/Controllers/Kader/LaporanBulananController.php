<?php
// app/Http/Controllers/LaporanBulananController.php
namespace App\Http\Controllers\kader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanBulanan;
use App\Models\Kader; // Import Kader model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
Use Illuminate\Support\Facades\Log;

class LaporanBulananController extends Controller
{
    public function index()
    {
        return view('kader.laporan_bulanan');
    }

    public function downloadTemplate()
    {
        $path = public_path('images/template/Tamplate_Laporan_Bulanan_Kader.docx');
        
        // Cek apakah file template ada
        if (!file_exists($path)) {
            return response()->json(['error' => 'Template file tidak ditemukan'], 404);
        }
        
        return response()->download($path);
    }

    public function uploadLaporan(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'laporan' => 'required|file|mimes:docx,pdf,doc|max:10240' // Increase max size to 10MB
            ]);

            if ($request->hasFile('laporan')) {
                $file = $request->file('laporan');
                
                // Pastikan file valid
                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'File yang diupload tidak valid'
                    ], 400);
                }

                // Ambil data kader yang sedang login
                $kader = Auth::user();
                if (!$kader) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Kader tidak terautentikasi'
                    ], 401);
                }

                // Buat nama file dengan format: nama_lengkap_id_originalname
                $namaLengkap = Str::slug($kader->nama_lengkap, '_');
                $kaderId = $kader->id;
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                
                // Format: nama_lengkap_id_namafile.extension
                $fileName = $namaLengkap . '_' . $kaderId . '_' . Str::slug($originalName, '_') . '.' . $extension;

                // Pastikan direktori ada
                $directory = 'public/laporan_bulanan';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                // Simpan file ke storage/app/public/laporan_bulanan/
                $path = $file->storeAs('laporan_bulanan', $fileName, 'public');

                // Cek apakah file berhasil disimpan
                if (!$path) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Gagal menyimpan file'
                    ], 500);
                }

                // Simpan data ke database
                $laporan = LaporanBulanan::create([
                    'kader_id' => Auth::id(),
                    'nama_file' => $fileName,
                    'path_file' => $path,
                    'nama_asli_file' => $file->getClientOriginalName(),
                    'ukuran_file' => $file->getSize(),
                    'tanggal_upload' => now(),
                ]);

                // Verifikasi file tersimpan di sistem file
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Laporan berhasil diupload',
                        'data' => [
                            'id' => $laporan->id,
                            'nama_file' => $fileName,
                            'ukuran' => round($file->getSize() / 1024, 2) . ' KB',
                            'tanggal_upload' => $laporan->tanggal_upload->format('d/m/Y H:i:s')
                        ]
                    ]);
                } else {
                    // Hapus record dari database jika file tidak tersimpan
                    $laporan->delete();
                    return response()->json([
                        'success' => false, 
                        'message' => 'File gagal tersimpan di server'
                    ], 500);
                }
            }

            return response()->json([
                'success' => false, 
                'message' => 'File tidak ditemukan dalam request'
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Upload laporan error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method tambahan untuk melihat daftar laporan
    public function myLaporan()
    {
        $laporan = LaporanBulanan::where('kader_id', Auth::id())
            ->orderBy('tanggal_upload', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $laporan
        ]);
    }

    // Method untuk download laporan yang sudah diupload
    public function downloadLaporan($id)
    {
        $laporan = LaporanBulanan::where('id', $id)
            ->where('kader_id', Auth::id())
            ->first();

        if (!$laporan) {
            return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
        }

        $filePath = storage_path('app/public/' . $laporan->path_file);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan di server'], 404);
        }

        return response()->download($filePath, $laporan->nama_asli_file);
    }
}