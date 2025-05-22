<?php

namespace App\Http\Controllers\Warga;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\TrackingHarian;

class ProfileController extends Controller
{
    public function index()
    {
        $warga =  Auth::guard('warga')->user();
        // Get latest home condition status from tracking_harian
        $homeCondition = TrackingHarian::where('warga_id', $warga->id)
            ->latest('tanggal_pantau')
            ->first();
            
        // Determine status display
        $statusDisplay = [
            'Aman' => 'Aman',
            'Tidak Aman' => 'Tidak Aman',
            'Belum Dicek' => 'Belum Dicek'
        ];
        
        return view('warga.profile', [
            'user' => $warga,
            'home_condition' => $homeCondition,
            'status_display' => $statusDisplay
        ]);
    }
}