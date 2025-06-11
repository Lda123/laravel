<?php

namespace App\Http\Controllers\kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $kader = Auth::guard('kader')->user();
        
        // Generate greeting based on time
        $hour = now()->hour;
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 12 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // Ambil pelatihan/event khusus untuk kader saja
        $events = DB::table('list_event')
            ->where('kategori_pengguna', 'kader')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Ambil ID pelatihan yang sudah didaftarkan oleh kader
        $registeredEvents = DB::table('event_kader')
            ->where('id_kader', $kader->id)
            ->pluck('id_event')
            ->toArray();

        // Notifikasi (berdasarkan tracking_harian hari ini)
        $notifikasi = null;
        $pendingReports = DB::table('tracking_harian')
            ->where('kader_id', $kader->id)
            ->whereDate('tanggal', Carbon::today())
            ->count();
        
        if ($pendingReports == 0) {
            $notifikasi = [
                'title' => 'Tracking Harian Belum Dibuat',
                'message' => 'Jangan lupa untuk membuat tracking harian hari ini.'
            ];
        }

        return view('kader.dashboard', compact(
            'kader',
            'greeting',
            'events',
            'registeredEvents',
            'notifikasi'
        ));
    }

    /**
     * Menampilkan halaman pelatihan yang sudah didaftarkan kader
     */
    public function pelatihanSaya()
    {
        try {
            $kader = Auth::guard('kader')->user();

            $pelatihan = DB::table('event_kader')
                ->join('list_event', 'event_kader.id_event', '=', 'list_event.id')
                ->where('event_kader.id_kader', $kader->id)
                ->where('list_event.kategori_pengguna', 'kader')
                ->select('list_event.*', 'event_kader.created_at as tanggal_daftar')
                ->orderBy('list_event.tanggal', 'asc')
                ->get();

            return view('kader.event_kader', compact('kader', 'pelatihan'));
        } catch (\Exception $e) {
            Log::error('Error in pelatihanSaya: ' . $e->getMessage());
            return redirect()->route('kader.dashboard')->with('error', 'Terjadi kesalahan saat mengakses pelatihan');
        }
    }

    /**
     * Mendaftarkan kader ke sebuah pelatihan
     */
    public function daftarPelatihan(Request $request)
    {
        try {
            $eventId = $request->event_id;
            $kaderId = Auth::guard('kader')->id();

            // Validasi event adalah untuk kader
            $event = DB::table('list_event')
                ->where('id', $eventId)
                ->where('kategori_pengguna', 'kader')
                ->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak ditemukan atau bukan untuk kader!'
                ], 404);
            }

            // Cek apakah sudah terdaftar
            $alreadyRegistered = DB::table('event_kader')
                ->where('id_kader', $kaderId)
                ->where('id_event', $eventId)
                ->exists();

            if ($alreadyRegistered) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar pada pelatihan ini!'
                ], 409);
            }

            // Daftarkan ke pelatihan
            DB::table('event_kader')->insert([
                'id_kader' => $kaderId,
                'id_event' => $eventId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendaftar pelatihan!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in daftarPelatihan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar pelatihan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Membatalkan pendaftaran pelatihan (via JSON)
     */
    public function batalkanPelatihan(Request $request)
    {
        try {
            $eventId = $request->input('cancel');
            $kaderId = Auth::guard('kader')->id();

            // Validasi bahwa event ini milik kader yang sedang login
            $eventKader = DB::table('event_kader')
                ->where('id_event', $eventId)
                ->where('id_kader', $kaderId)
                ->first();

            if (!$eventKader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran tidak ditemukan!'
                ], 404);
            }

            $deleted = DB::table('event_kader')
                ->where('id_event', $eventId)
                ->where('id_kader', $kaderId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membatalkan pendaftaran!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan pendaftaran!'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in batalkanPelatihan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pendaftaran.'
            ], 500);
        }
    }
}