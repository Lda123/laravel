<?php

namespace App\Http\Controllers\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WargaController extends Controller
{
    /**
     * Menampilkan dashboard warga dengan status keluhan dan daftar event
     */
    public function dashboard()
    {
        $warga = Auth::user();
        $today = Carbon::today()->toDateString();
        
        // Cek apakah warga sudah mengisi keluhan hari ini
        $sudahIsiKeluhan = DB::table('keluhan_harian')
            ->where('id_warga', $warga->id)
            ->whereDate('tanggal', $today)
            ->exists();
        
        // Ambil semua event yang tersedia
        $events = DB::table('list_event')->get();
        
        // Ambil ID event yang sudah didaftarkan oleh warga
        $registeredEvents = DB::table('event_warga')
            ->where('id_warga', $warga->id)
            ->pluck('id_event')
            ->toArray();
        
        return view('warga.dashboard', compact(
            'warga',
            'sudahIsiKeluhan',
            'events',
            'registeredEvents',
            'today'
        ));
    }

    /**
     * Mendaftarkan warga ke sebuah event
     */
    public function daftarEvent(Request $request)
    {
        $eventId = $request->event_id;
        
        $alreadyRegistered = DB::table('event_warga')
            ->where('id_warga', Auth::id())
            ->where('id_event', $eventId)
            ->exists();
        
        if (!$alreadyRegistered) {
            DB::table('event_warga')->insert([
                'id_warga' => Auth::id(),
                'id_event' => $eventId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('warga.eventsaya');
    }

    /**
     * Menampilkan daftar event yang telah didaftarkan oleh warga
     */
    public function eventSaya()
    {
        try {
            $warga = Auth::user();

            $events = DB::table('event_warga')
                ->join('list_event', 'event_warga.id_event', '=', 'list_event.id')
                ->where('event_warga.id_warga', $warga->id)
                ->select('list_event.*', 'event_warga.created_at as tanggal_daftar')
                ->get();

            return view('warga.eventsaya', compact('warga', 'events'));
        } catch (\Exception $e) {
            Log::error('Error in eventSaya: ' . $e->getMessage());

            session()->flash('error', 'Terjadi kesalahan saat mengakses event: ' . $e->getMessage());
            return redirect()->route('warga.dashboard');
        }
    }

    /**
     * Membatalkan pendaftaran warga pada sebuah event
     */
    public function cancelEvent($eventId)
    {
        try {
            $deleted = DB::table('event_warga')
                ->where('id_warga', Auth::id())
                ->where('id_event', $eventId)
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Pendaftaran event berhasil dibatalkan'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Pendaftaran event tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error canceling event: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan pendaftaran event'], 500);
        }
    }
}
