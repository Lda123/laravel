<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EventWarga;
use App\Models\ListEvent;
use Carbon\Carbon;

/**
 * @method \Illuminate\Routing\Controller middleware(string|array $middleware, array $options = [])
 */
class EventController extends Controller
{
    /**
     * Constructor - menerapkan middleware auth khusus warga
     */
    public function __construct()
    {
        $this->middleware('auth:warga');
    }

    /**
     * Menampilkan daftar event yang diikuti oleh warga yang sedang login.
     *
     * @return \Illuminate\Http\Response
     */
    public function myEvents()
    {
        try {
            $warga = Auth::guard('warga')->user();

            $events = DB::table('event_warga')
                ->join('list_event', 'event_warga.id_event', '=', 'list_event.id')
                ->where('event_warga.id_warga', $warga->id)
                ->select('list_event.*', 'event_warga.created_at as tanggal_daftar')
                ->get();

            return view('warga.eventsaya', compact('warga', 'events'));
        } catch (\Exception $e) {
            Log::error('Error in myEvents: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengakses event.');
            return redirect()->route('warga.dashboard');
        }
    }

    /**
     * Membatalkan pendaftaran event oleh warga yang sedang login.
     *
     * @param  int  $id ID event
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelEvent($id)
{
    try {
        $id_warga = Auth::guard('warga')->id();

        $deleted = DB::table('event_warga')
            ->where('id_warga', $id_warga)
            ->where('id_event', $id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran event berhasil dibatalkan.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran event tidak ditemukan atau sudah dibatalkan.'
            ], 404);
        }
    } catch (\Exception $e) {
        Log::error('Error canceling event: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server saat membatalkan pendaftaran event.'
        ], 500);
    }    
}
    /**
     * Mendaftarkan warga ke event (optional).
     * Tambahkan method ini jika kamu ingin mendukung pendaftaran event.
     */
    public function registerEvent($id)
    {
        try {
            $id_warga = Auth::guard('warga')->id();

            $existing = DB::table('event_warga')
                ->where('id_warga', $id_warga)
                ->where('id_event', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar pada event ini.'
                ], 409);
            }

            DB::table('event_warga')->insert([
                'id_warga' => $id_warga,
                'id_event' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendaftar ke event.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error registering event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar ke event.'
            ], 500);
        }
    }
}
