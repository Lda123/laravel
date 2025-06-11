<?php

namespace App\Http\Controllers\warga\fitur_utama;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingHarian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        try {
            $warga = Auth::guard('warga')->user();
            
            if (!$warga) {
                return redirect()->route('warga.login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            // Get the latest tracking data with proper relationship
            $latestData = TrackingHarian::where('warga_id', $warga->id)
                ->with(['kader' => function($query) {
                    $query->select('id', 'nama_lengkap', 'nama');
                }])
                ->latest('tanggal')
                ->first();

            // Transform latest data for consistency with view expectations
            if ($latestData) {
                $latestData->tanggal_pantau = $latestData->tanggal;
                $latestData->status = $latestData->kategori_masalah ?? 'Belum Dicek';
                $latestData->keterangan = $latestData->deskripsi ?? 'Tidak ada keterangan';
                
                // Add missing fields that the blade expects
                if (!isset($latestData->jam_pantau)) {
                    $latestData->jam_pantau = $latestData->created_at ? $latestData->created_at->format('H:i:s') : null;
                }
                if (!isset($latestData->tingkat_risiko)) {
                    $latestData->tingkat_risiko = $this->determineTingkatRisiko($latestData->kategori_masalah);
                }
                if (!isset($latestData->detail_masalah)) {
                    $latestData->detail_masalah = $latestData->deskripsi;
                }
                if (!isset($latestData->rekomendasi_tindakan)) {
                    $latestData->rekomendasi_tindakan = $this->getRekomendasi($latestData->kategori_masalah);
                }
                if (!isset($latestData->tindak_lanjut)) {
                    $latestData->tindak_lanjut = null;
                }
                if (!isset($latestData->catatan_pencegahan)) {
                    $latestData->catatan_pencegahan = $this->getCatatanPencegahan($latestData->kategori_masalah);
                }
            }

            // Add debugging
            Log::info('Latest Data for warga ' . $warga->id, [
                'latest_data' => $latestData ? [
                    'id' => $latestData->id,
                    'tanggal' => $latestData->tanggal,
                    'kategori_masalah' => $latestData->kategori_masalah,
                    'status' => $latestData->status,
                    'deskripsi' => $latestData->deskripsi,
                    'kader_nama' => $latestData->kader ? $latestData->kader->nama_lengkap : null,
                    'bukti_foto' => $latestData->bukti_foto
                ] : 'No data found'
            ]);
                
            // Get statistics with corrected field names
            $stats = (object) [
                'total' => TrackingHarian::where('warga_id', $warga->id)->count(),
                'tidak_aman_count' => TrackingHarian::where('warga_id', $warga->id)
                    ->where('kategori_masalah', 'Tidak Aman')
                    ->count(),
                'aman_count' => TrackingHarian::where('warga_id', $warga->id)
                    ->where('kategori_masalah', 'Aman')
                    ->count(),
                'belum_dicek_count' => TrackingHarian::where('warga_id', $warga->id)
                    ->where(function($query) {
                        $query->where('kategori_masalah', 'Belum Dicek')
                              ->orWhereNull('kategori_masalah');
                    })
                    ->count()
            ];
                
            // Get history with filters
            $riwayatQuery = TrackingHarian::where('warga_id', $warga->id)
                ->with(['kader' => function($query) {
                    $query->select('id', 'nama_lengkap', 'nama');
                }])
                ->orderBy('tanggal', 'desc');
                
            // Apply filters
            if ($request->has('bulan') && $request->bulan != '') {
                $riwayatQuery->whereMonth('tanggal', $request->bulan);
            }
            
            if ($request->has('tahun') && $request->tahun != '') {
                $riwayatQuery->whereYear('tanggal', $request->tahun);
            }
            
            if ($request->has('status') && $request->status != '') {
                $riwayatQuery->where('kategori_masalah', $request->status);
            }
            
            $riwayat = $riwayatQuery->paginate(10)->withQueryString();
            
            // Transform data untuk konsistensi tampilan
            $riwayat->getCollection()->transform(function ($item) {
                // Pastikan semua field yang dibutuhkan ada dengan konsistensi penamaan
                $item->tanggal_pantau = $item->tanggal;
                $item->status = $item->kategori_masalah ?? 'Belum Dicek';
                $item->keterangan = $item->deskripsi ?? 'Tidak ada keterangan';
                
                // Add missing fields for blade compatibility
                $item->jam_pantau = $item->created_at ? $item->created_at->format('H:i:s') : null;
                $item->tingkat_risiko = $this->determineTingkatRisiko($item->kategori_masalah);
                $item->detail_masalah = $item->deskripsi;
                $item->rekomendasi_tindakan = $this->getRekomendasi($item->kategori_masalah);
                $item->tindak_lanjut = null;
                $item->catatan_pencegahan = $this->getCatatanPencegahan($item->kategori_masalah);
                
                // Format tanggal untuk tampilan yang lebih baik
                if ($item->tanggal) {
                    $item->tanggal_formatted = Carbon::parse($item->tanggal)->format('d M Y');
                    $item->tanggal_full = Carbon::parse($item->tanggal)->format('l, d F Y');
                }
                
                return $item;
            });
            
            return view('warga.riwayat', [
                'latestData' => $latestData,
                'stats' => $stats,
                'riwayat' => $riwayat,
                'currentMonth' => $request->get('bulan', ''),
                'currentYear' => $request->get('tahun', ''),
                'currentStatus' => $request->get('status', '')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in warga riwayat index: ' . $e->getMessage(), [
                'warga_id' => Auth::guard('warga')->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memuat data riwayat: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Pastikan warga terautentikasi
            $warga = Auth::guard('warga')->user();
            
            if (!$warga) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            // Cari tracking berdasarkan ID dan pastikan milik warga yang sedang login
            $tracking = TrackingHarian::with(['kader' => function($query) {
                    $query->select('id', 'nama_lengkap', 'nama');
                }])
                ->where('id', $id)
                ->where('warga_id', $warga->id) // Pastikan hanya bisa akses tracking milik sendiri
                ->first();
                
            if (!$tracking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Data tracking tidak ditemukan'
                ], 404);
            }
            
            // Format data untuk response - sesuaikan dengan yang diharapkan blade/JavaScript
            $data = [
                'id' => $tracking->id,
                'tanggal_pantau' => $tracking->tanggal->format('Y-m-d'),
                'jam_pantau' => $tracking->created_at ? $tracking->created_at->format('H:i:s') : null,
                'status' => $tracking->kategori_masalah ?? 'Belum Dicek',
                'kategori_masalah' => $tracking->kategori_masalah,
                'detail_masalah' => $tracking->deskripsi,
                'tingkat_risiko' => $this->determineTingkatRisiko($tracking->kategori_masalah),
                'keterangan' => $tracking->deskripsi ?? 'Tidak ada keterangan',
                'rekomendasi_tindakan' => $this->getRekomendasi($tracking->kategori_masalah),
                'tindak_lanjut' => null, // Field ini tidak ada di TrackingHarian, bisa ditambahkan nanti
                'catatan_pencegahan' => $this->getCatatanPencegahan($tracking->kategori_masalah),
                'kader' => [
                    'nama' => $tracking->kader->nama_lengkap ?? $tracking->kader->nama ?? 'Tidak diketahui',
                    'nama_lengkap' => $tracking->kader->nama_lengkap ?? 'Tidak diketahui',
                    'telepon' => $tracking->kader->telepon ?? null
                ],
                'bukti_foto' => null
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
                    $data['bukti_foto'] = asset('storage/' . $fotoPath);
                }
                // Path lainnya
                else {
                    $data['bukti_foto'] = asset('storage/' . $fotoPath);
                }
                
                // Cek apakah file benar-benar ada (optional, bisa di-comment jika mengganggu performance)
                try {
                    $realPath = str_replace(url('/'), '', $data['bukti_foto']);
                    if (!file_exists(public_path($realPath))) {
                        Log::warning('Image file not found: ' . $realPath);
                        // Jangan set null, biarkan tetap ada URL untuk debugging
                    }
                } catch (\Exception $e) {
                    Log::warning('Error checking image file: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing tracking detail for warga: ' . $e->getMessage(), [
                'tracking_id' => $id,
                'warga_id' => Auth::guard('warga')->id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Method tambahan untuk mendapatkan statistik detail
     */
    public function getStats()
    {
        try {
            $warga = Auth::guard('warga')->user();
            
            if (!$warga) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $stats = [
                'total' => TrackingHarian::where('warga_id', $warga->id)->count(),
                'aman' => TrackingHarian::where('warga_id', $warga->id)
                    ->where('kategori_masalah', 'Aman')->count(),
                'tidak_aman' => TrackingHarian::where('warga_id', $warga->id)
                    ->where('kategori_masalah', 'Tidak Aman')->count(),
                'belum_dicek' => TrackingHarian::where('warga_id', $warga->id)
                    ->where(function($query) {
                        $query->where('kategori_masalah', 'Belum Dicek')
                              ->orWhereNull('kategori_masalah');
                    })->count(),
                'last_check' => TrackingHarian::where('warga_id', $warga->id)
                    ->latest('tanggal')->first()?->tanggal?->format('Y-m-d')
            ];

            return response()->json(['success' => true, 'data' => $stats]);

        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * Helper method untuk menentukan tingkat risiko berdasarkan kategori masalah
     */
    private function determineTingkatRisiko($kategoriMasalah)
    {
        return match($kategoriMasalah) {
            'Tidak Aman' => 'Tinggi',
            'Aman' => 'Rendah',
            default => 'Sedang'
        };
    }

    /**
     * Helper method untuk mendapatkan rekomendasi berdasarkan kategori masalah
     */
    private function getRekomendasi($kategoriMasalah)
    {
        return match($kategoriMasalah) {
            'Tidak Aman' => 'Segera bersihkan genangan air, tutup tempat penampungan air, dan lakukan fogging jika diperlukan',
            'Aman' => 'Pertahankan kebersihan lingkungan dan lakukan pemeriksaan rutin',
            default => 'Lakukan pemeriksaan menyeluruh terhadap tempat-tempat yang berpotensi menjadi sarang nyamuk'
        };
    }

    /**
     * Helper method untuk mendapatkan catatan pencegahan
     */
    private function getCatatanPencegahan($kategoriMasalah)
    {
        return match($kategoriMasalah) {
            'Aman' => 'Lanjutkan rutinitas 3M (Menguras, Menutup, Mengubur) dan jaga kebersihan lingkungan',
            'Tidak Aman' => 'Tingkatkan kewaspadaan dan segera lakukan tindakan pemberantasan sarang nyamuk',
            default => 'Lakukan pemantauan berkala dan terapkan prinsip 3M Plus'
        };
    }
}