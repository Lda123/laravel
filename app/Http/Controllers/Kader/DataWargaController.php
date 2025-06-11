<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Warga;
use App\Models\TrackingHarian;
use Illuminate\Http\Request;

class DataWargaController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options - mengambil semua data tanpa batasan
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        $kelurahans = Kelurahan::orderBy('nama_kelurahan')->get();
        $rws = Rw::orderBy('nomor_rw')->get();
        $rts = Rt::orderBy('nomor_rt')->get();
        
        // Build query dengan eager loading yang benar
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
        
        // Transform data untuk menambahkan wilayah dan status
        $wargas->getCollection()->transform(function ($warga) {
            // Set wilayah
            if ($warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan && $warga->rt->rw->kelurahan->kecamatan) {
                $warga->wilayah = sprintf(
                    'Kec. %s, Kel. %s, RW %02d, RT %02d',
                    $warga->rt->rw->kelurahan->kecamatan->nama_kecamatan,
                    $warga->rt->rw->kelurahan->nama_kelurahan,
                    $warga->rt->rw->nomor_rw,
                    $warga->rt->nomor_rt
                );
            } else {
                $warga->wilayah = 'Data tidak lengkap';
            }
            
            // Set status dari tracking harian terbaru
            $latestTracking = $warga->trackingHarians->first();
            if ($latestTracking) {
                $warga->status_display = $latestTracking->kategori_masalah ?? 'Belum Diperiksa';
            } else {
                $warga->status_display = 'Belum Diperiksa';
            }
            
            return $warga;
        });
        
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
        try {
            $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)
                ->orderBy('nama_kelurahan')
                ->get();
                
            return response()->json($kelurahans);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data kelurahan'], 500);
        }
    }

    public function getRw(Request $request)
    {
        try {
            $rws = Rw::where('kelurahan_id', $request->kelurahan_id)
                ->orderBy('nomor_rw')
                ->get();
                
            return response()->json($rws);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data RW'], 500);
        }
    }

    public function getRt(Request $request)
    {
        try {
            $rts = Rt::where('rw_id', $request->rw_id)
                ->orderBy('nomor_rt')
                ->get();
                
            return response()->json($rts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data RT'], 500);
        }
    }

    public function getWargaDetail($id)
    {
        try {
            // Pastikan kader terautentikasi
            $kader = Auth::guard('kader')->user();
            
            if (!$kader) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 401);
            }

            $warga = Warga::with([
                'rt.rw.kelurahan.kecamatan',
                'trackingHarians' => function($q) {
                    $q->with('kader')->latest()->limit(1);
                }
            ])->findOrFail($id);

            // Format wilayah
            if ($warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan && $warga->rt->rw->kelurahan->kecamatan) {
                $warga->wilayah = sprintf(
                    'Kec. %s, Kel. %s, RW %02d, RT %02d',
                    $warga->rt->rw->kelurahan->kecamatan->nama_kecamatan,
                    $warga->rt->rw->kelurahan->nama_kelurahan,
                    $warga->rt->rw->nomor_rw,
                    $warga->rt->nomor_rt
                );
            } else {
                $warga->wilayah = 'Data tidak lengkap';
            }

            // Format alamat lengkap
            $alamat_parts = [];
            if ($warga->alamat_lengkap) {
                $alamat_parts[] = $warga->alamat_lengkap;
            }
            if ($warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan) {
                $alamat_parts[] = sprintf(
                    'RT %02d/RW %02d, %s',
                    $warga->rt->nomor_rt,
                    $warga->rt->rw->nomor_rw,
                    $warga->rt->rw->kelurahan->nama_kelurahan
                );
                if ($warga->rt->rw->kelurahan->kecamatan) {
                    $alamat_parts[] = 'Kec. ' . $warga->rt->rw->kelurahan->kecamatan->nama_kecamatan;
                }
            }
            $warga->alamat_formatted = implode(', ', $alamat_parts);

            // Get latest tracking dengan status yang benar
            $latestTracking = $warga->trackingHarians->first();
            
            // Format response data
            $response = [
                'id' => $warga->id,
                'nik' => $warga->nik,
                'nama_lengkap' => $warga->nama_lengkap,
                'tempat_lahir' => $warga->tempat_lahir,
                'tanggal_lahir' => $warga->tanggal_lahir,
                'jenis_kelamin' => $warga->jenis_kelamin,
                'alamat_lengkap' => $warga->alamat_formatted,
                'wilayah' => $warga->wilayah,
                'telepon' => $warga->telepon,
                'foto_ktp' => $warga->foto_ktp,
                'foto_diri_ktp' => $warga->foto_diri_ktp,
                'latest_tracking' => null
            ];

            // Format latest tracking data jika ada
            if ($latestTracking) {
                $bukti_foto = null;
                
                // Handle bukti foto dengan berbagai kemungkinan path
                if ($latestTracking->bukti_foto) {
                    $fotoPath = $latestTracking->bukti_foto;
                    
                    // Jika path sudah lengkap (dimulai dengan http)
                    if (str_starts_with($fotoPath, 'http')) {
                        $bukti_foto = $fotoPath;
                    }
                    // Jika path dimulai dengan storage/
                    elseif (str_starts_with($fotoPath, 'storage/')) {
                        $bukti_foto = asset($fotoPath);
                    }
                    // Jika path dimulai dengan tracking_harian/
                    elseif (str_starts_with($fotoPath, 'tracking_harian/')) {
                        $bukti_foto = asset('storage/' . $fotoPath);
                    }
                    // Path lainnya
                    else {
                        $bukti_foto = asset('storage/' . $fotoPath);
                    }
                }

                $response['latest_tracking'] = [
                    'id' => $latestTracking->id,
                    'tanggal' => $latestTracking->tanggal ? $latestTracking->tanggal->format('Y-m-d') : null,
                    'kategori_masalah' => $latestTracking->kategori_masalah ?? 'Belum Diperiksa',
                    'status' => $latestTracking->status ?? 'Selesai',
                    'bukti_foto' => $bukti_foto,
                    'deskripsi' => $latestTracking->deskripsi,
                    'kader' => $latestTracking->kader ? $latestTracking->kader->nama_lengkap : 'Tidak diketahui',
                    'rt' => $warga->rt ? sprintf('RT %02d', $warga->rt->nomor_rt) : '-',
                    'rw' => $warga->rt && $warga->rt->rw ? sprintf('RW %02d', $warga->rt->rw->nomor_rw) : '-',
                    'kelurahan' => $warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan ? $warga->rt->rw->kelurahan->nama_kelurahan : '-',
                    'kecamatan' => $warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan && $warga->rt->rw->kelurahan->kecamatan ? $warga->rt->rw->kelurahan->kecamatan->nama_kecamatan : '-'
                ];
            }

            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Error getting warga detail: ' . $e->getMessage(), [
                'warga_id' => $id,
                'kader_id' => Auth::guard('kader')->id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Data warga tidak ditemukan',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Method untuk menampilkan detail tracking berdasarkan ID tracking
     * Ini adalah method tambahan yang disesuaikan dengan method show yang Anda berikan
     */
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
            $tracking = TrackingHarian::with(['warga.rt.rw.kelurahan.kecamatan', 'kader'])
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
                'tanggal' => $tracking->tanggal ? $tracking->tanggal->format('Y-m-d') : null,
                'nama_warga' => $tracking->warga->nama_lengkap ?? $tracking->nama_warga ?? 'Tidak diketahui',
                'warga_nik' => $tracking->warga->nik ?? $tracking->warga_nik ?? '-',
                'rt' => $tracking->warga && $tracking->warga->rt ? sprintf('RT %02d', $tracking->warga->rt->nomor_rt) : '-',
                'rw' => $tracking->warga && $tracking->warga->rt && $tracking->warga->rt->rw ? sprintf('RW %02d', $tracking->warga->rt->rw->nomor_rw) : '-',
                'kelurahan' => $tracking->warga && $tracking->warga->rt && $tracking->warga->rt->rw && $tracking->warga->rt->rw->kelurahan ? $tracking->warga->rt->rw->kelurahan->nama_kelurahan : '-',
                'kecamatan' => $tracking->warga && $tracking->warga->rt && $tracking->warga->rt->rw && $tracking->warga->rt->rw->kelurahan && $tracking->warga->rt->rw->kelurahan->kecamatan ? $tracking->warga->rt->rw->kelurahan->kecamatan->nama_kecamatan : '-',
                'kader' => $tracking->kader->nama_lengkap ?? 'Tidak diketahui',
                'kategori_masalah' => $tracking->kategori_masalah ?? 'Belum Diperiksa',
                'deskripsi' => $tracking->deskripsi,
                'bukti_foto' => $tracking->bukti_foto,
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
                    $data['bukti_foto'] = asset('storage/' . $fotoPath);
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
}