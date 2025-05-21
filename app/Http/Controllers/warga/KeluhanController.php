<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KeluhanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KeluhanController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $today = Carbon::today()->toDateString();
            
            // Check if user already submitted today's report
            // Cek apakah user sudah mengisi keluhan hari ini
            $existingKeluhan = KeluhanHarian::where('id_warga', $user->id)
                ->whereDate('tanggal', $today)
                ->first();
                
            if ($existingKeluhan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah mengisi keluhan hari ini.'
                ]);
            }
            
            // Calculate DBD probability score
            // Hitung kemungkinan DBD berdasarkan gejala
            $scoreFactor = [
                'suhu' => ($request->suhu >= 38) ? 20 : 0,
                'ruam' => $this->getRuamScore($request->ruam),
                'nyeri_otot' => ($request->nyeri_otot == 'Ya') ? 15 : 0,
                'mual' => ($request->mual == 'Ya') ? 10 : 0,
                'nyeri_belakang_mata' => ($request->nyeri_belakang_mata == 'Ya') ? 15 : 0,
                'pendarahan' => ($request->pendarahan == 'Ya') ? 20 : 0
            ];
            
            $dbdScore = array_sum($scoreFactor);
            
            // Get the recommendation based on the score
            // Tentukan anjuran berdasarkan skor
            $anjuran = $this->getAnjuran($dbdScore);
            
            // Save the report
            // Simpan keluhan
            KeluhanHarian::create([
                'id_warga' => $user->id,
                'tanggal' => $today,
                'suhu' => $request->suhu,
                'ruam' => $request->ruam,
                'nyeri_otot' => $request->nyeri_otot == 'Ya' ? true : false,
                'mual' => $request->mual == 'Ya' ? true : false,
                'nyeri_belakang_mata' => $request->nyeri_belakang_mata == 'Ya' ? true : false,
                'pendarahan' => $request->pendarahan == 'Ya' ? true : false,
                'gejala_lain' => $request->gejala_lain, // Fixed field name mapping
                'akurasi_dbd' => $dbdScore,
                'anjuran' => $anjuran
            ]);
            
            $message = "Terima kasih telah melaporkan kondisi kesehatan Anda!<br><br>";
            
            // Add the analysis result to the message
            // Tambahkan hasil analisis ke pesan
            if ($dbdScore >= 70) {
                $message .= "Gejala Anda memiliki kemiripan {$dbdScore}% dengan gejala DBD. {$anjuran}";
            } else if ($dbdScore >= 40) {
                $message .= "Gejala Anda memiliki kemiripan {$dbdScore}% dengan gejala DBD. {$anjuran}";
            } else {
                $message .= "Gejala Anda memiliki kemiripan rendah ({$dbdScore}%) dengan gejala DBD. {$anjuran}";
            }
            
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the score for ruam based on the given input
     *
     * @param  string  $ruam
     * @return int
     */
    private function getRuamScore($ruam)
    {
        switch ($ruam) {
            case 'Tidak Ada':
                return 0;
            case '1 hari':
                return 5;
            case '2-3 hari':
                return 15;
            case '4-6 hari':
                return 10;
            case '7+ hari':
                return 5;
            default:
                return 0;
        }
    }
    
    /**
     * Get recommendation based on the score
     *
     * @param  int  $score
     * @return string
     */
    private function getAnjuran($score)
    {
        if ($score >= 80) {
            return 'Segera periksa ke fasilitas kesehatan terdekat karena gejala sangat mirip DBD.';
        } elseif ($score >= 60) {
            return 'Periksakan diri ke dokter atau puskesmas untuk pemeriksaan lebih lanjut.';
        } elseif ($score >= 40) {
            return 'Istirahat yang cukup dan pantau kondisi. Jika gejala memburuk, segera ke fasilitas kesehatan.';
        } else {
            return 'Tetap jaga kesehatan dan pantau kondisi Anda.';
        }
    }
}