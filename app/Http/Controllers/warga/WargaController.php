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
        $warga = Auth::guard('warga')->user();
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
     * Mendaftarkan warga ke sebuah event melalui form biasa
     */
    public function daftarEvent(Request $request)
    {
        $eventId = $request->event_id;

        $alreadyRegistered = DB::table('event_warga')
            ->where('id_warga', Auth::guard('warga')->id())
            ->where('id_event', $eventId)
            ->exists();

        if (!$alreadyRegistered) {
            DB::table('event_warga')->insert([
                'id_warga' => Auth::guard('warga')->id(),
                'id_event' => $eventId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('warga.eventsaya');
    }

    /**
     * Mendaftarkan warga ke event via JSON (API-like)
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

    /**
     * Menampilkan daftar event yang telah didaftarkan oleh warga
     */
    public function eventSaya()
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
            Log::error('Error in eventSaya: ' . $e->getMessage());

            session()->flash('error', 'Terjadi kesalahan saat mengakses event: ' . $e->getMessage());
            return redirect()->route('warga.dashboard');
        }
    }

    /**
     * Membatalkan pendaftaran warga pada sebuah event
     */
    
     public function cancelEvent($id)
     {
         try {
             $id_warga = Auth::guard('warga')->id(); // Autentikasi warga
             $deleted = DB::table('event_warga')
                 ->where('id_warga', $id_warga)
                 ->where('id_event', $id)
                 ->delete();
     
             if ($deleted) {
                 return response()->json(['success' => true]);
             } else {
                 return response()->json(['success' => false, 'message' => 'Pendaftaran tidak ditemukan'], 404);
             }
         } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server'], 500);
         }
     }
     
         }
