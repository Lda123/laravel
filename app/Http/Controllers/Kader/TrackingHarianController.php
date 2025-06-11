<?php

namespace App\Http\Controllers\kader;

use App\Http\Controllers\Controller;
use App\Models\TrackingHarian;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class TrackingHarianController extends Controller
{
    public function index()
    {
        $kader = Auth::guard('kader')->user();
        
        if (!$kader) {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai kader.');
        }
        
        $trackings = TrackingHarian::with(['warga.rt', 'kader'])
            ->where('kader_id', $kader->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $warga = Warga::with('rt')
            ->when($kader->rt_id, function($query) use ($kader) {
                $query->where('rt_id', $kader->rt_id);
            })
            ->select('id', 'nik', 'nama_lengkap', 'rt_id')
            ->get();

        return view('kader.tracking_harian', compact('trackings', 'warga', 'kader'));
    }

    public function show($id)
    {
        try {
            // Pastikan kader terautentikasi
            $kader = Auth::guard('kader')->user();
            
            if (!$kader) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            // Cari tracking berdasarkan ID dan pastikan milik kader yang sedang login
            $tracking = TrackingHarian::with(['warga', 'warga.rt', 'kader'])
                ->where('id', $id)
                ->where('kader_id', $kader->id) // Pastikan hanya bisa akses tracking milik sendiri
                ->first();
            
            if (!$tracking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Data tracking tidak ditemukan'
                ], 404);
            }

            // Format data untuk response
            $data = [
                'id' => $tracking->id,
                'tanggal' => $tracking->tanggal->format('Y-m-d'),
                'nama_warga' => $tracking->warga->nama_lengkap ?? $tracking->nama_warga ?? 'Tidak diketahui',
                'warga_nik' => $tracking->warga->nik ?? $tracking->warga_nik ?? '-',
                'rt' => $tracking->warga && $tracking->warga->rt ? $tracking->warga->rt->nomor_rt : '-',
                'kader' => $tracking->kader->nama_lengkap ?? 'Tidak diketahui',
                'kategori_masalah' => $tracking->kategori_masalah,
                'deskripsi' => $tracking->deskripsi,
                'bukti_foto' => null,
                'status' => $tracking->status ?? 'Selesai'
            ];

            // Handle bukti foto dengan berbagai kemungkinan path
            if ($tracking->bukti_foto) {
                $fotoPath = $tracking->bukti_foto;
                
                // Jika path sudah lengkap (dimulai dengan http)
                if (str_starts_with($fotoPath, 'http')) {
                    $data['bukti_foto'] = $fotoPath;
                }
                // Jika path dimulai dengan storage/
                elseif (str_starts_with($fotoPath, 'storage/')) {
                    $data['bukti_foto'] = asset($fotoPath);
                }
                // Jika path dimulai dengan tracking_harian/
                elseif (str_starts_with($fotoPath, 'tracking_harian/')) {
                    $data['bukti_foto'] = asset($fotoPath);
                }
                // Path lainnya
                else {
                    $data['bukti_foto'] = asset('storage/' . $fotoPath);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing tracking detail: ' . $e->getMessage(), [
                'tracking_id' => $id,
                'kader_id' => Auth::guard('kader')->id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'kategori_masalah' => 'required|in:Aman,Tidak Aman,Belum Dicek',
            'deskripsi' => 'required|string|max:500',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini',
            'warga_id.required' => 'Pilih warga terlebih dahulu',
            'warga_id.exists' => 'Data warga tidak valid',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'bukti_foto.image' => 'File harus berupa gambar',
            'bukti_foto.max' => 'Ukuran file maksimal 2MB',
        ]);

        try {
            // Pastikan kader terautentikasi
            $kader = Auth::guard('kader')->user();
            
            if (!$kader) {
                Alert::error('Gagal', 'Sesi login telah berakhir. Silakan login ulang.');
                return redirect()->route('login');
            }

            // Cek apakah sudah ada tracking untuk warga ini pada tanggal yang sama
            $existingTracking = TrackingHarian::where('warga_id', $validated['warga_id'])
                ->where('tanggal', $validated['tanggal'])
                ->where('kader_id', $kader->id)
                ->first();

            if ($existingTracking) {
                Alert::warning('Peringatan', 'Tracking untuk warga ini pada tanggal tersebut sudah ada.');
                return back()->withInput();
            }

            // Load warga dengan relasi RT
            $warga = Warga::with('rt')->findOrFail($validated['warga_id']);

            // Prepare data untuk disimpan
            $data = [
                'warga_id' => $warga->id,
                'warga_nik' => $warga->nik,
                'nama_warga' => $warga->nama_lengkap,
                'kader_id' => $kader->id,
                'tanggal' => $validated['tanggal'],
                'kategori_masalah' => $validated['kategori_masalah'],
                'deskripsi' => $validated['deskripsi'],
                'status' => 'Selesai',
            ];

            // Handle upload foto jika ada
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                
                // Buat nama file yang unik
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Pastikan folder public/tracking_harian ada
                $publicPath = public_path('tracking_harian');
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }
                
                // Simpan file ke public/tracking_harian
                $file->move($publicPath, $filename);
                
                // Simpan path yang benar ke database
                $data['bukti_foto'] = 'tracking_harian/' . $filename;
                
                Log::info('File uploaded successfully', [
                    'filename' => $filename,
                    'path' => $publicPath . '/' . $filename,
                    'exists' => file_exists($publicPath . '/' . $filename)
                ]);
            }

            // Simpan data
            TrackingHarian::create($data);
            Alert::success('Berhasil', 'Data tracking harian berhasil disimpan');
            return redirect()->route('kader.tracking-harian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Gagal', 'Data yang dimasukkan tidak valid');
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating tracking: ' . $e->getMessage(), [
                'kader_id' => $kader->id ?? null,
                'request_data' => $request->all()
            ]);
            
            Alert::error('Gagal', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return back()->withInput();
        }
    }
}