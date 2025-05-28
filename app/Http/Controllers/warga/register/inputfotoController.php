<?php
// app/Http/Controllers/warga/register/inputfotoController.php

namespace App\Http\Controllers\warga\register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Warga;
use Illuminate\Support\Facades\Log;

class inputfotoController extends Controller
{
    public function showUploadFotoForm()
    {
        if (!Session::has('data_diri')) {
            return redirect()->route('register.data_diri')->with('error', 'Data belum lengkap, silakan isi data diri dahulu.');
        }

        return view('register.input_foto');
    }

    public function storeUploadFoto(Request $request)
{
    $request->validate([
        'ktp' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        'foto_diri' => 'required|image|mimes:jpg,jpeg,png|max:10240',
    ]);

    try {
        // Ambil data dari session
        $data = Session::get('data_diri');

        // Debug: Cek data yang diambil dari session
        Log::info('Data dari session untuk insert:', $data);

            // Pastikan semua data yang required ada
            if (!isset($data['nik']) || !isset($data['nama_lengkap']) || !isset($data['telepon']) || !isset($data['password'])) {
                throw new \Exception('Data tidak lengkap. NIK, nama lengkap, telepon, atau password tidak ditemukan.');
            }

            // Generasi nama file untuk foto
            $ktpFilename = $this->generateFilename('ktp', $data['nama_lengkap'], $data['kecamatan'], $data['kelurahan'], $data['rt_rw']);
            $fotoDiriFilename = $this->generateFilename('diri', $data['nama_lengkap'], $data['kecamatan'], $data['kelurahan'], $data['rt_rw']);

            // Simpan foto
            $ktpPath = $request->file('ktp')->storeAs('foto_ktp', $ktpFilename, 'public');
            $fotoDiriPath = $request->file('foto_diri')->storeAs('foto_diri_ktp', $fotoDiriFilename, 'public');

            // Siapkan data untuk insert ke database
            $wargaData = [
                'nik' => $data['nik'],
                'nama_lengkap' => $data['nama_lengkap'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'alamat_lengkap' => $data['alamat_lengkap'],
                'rt_id' => $data['rt_id'],
                'telepon' => $data['telepon'],
                'password' => Hash::make($data['password']),
                'foto_ktp' => $ktpPath,
                'foto_diri_ktp' => $fotoDiriPath,
            ];

            // Debug: Cek data yang akan diinsert
            Log::info('Data warga yang akan diinsert:', $wargaData);

            // Simpan warga baru ke database
            $warga = Warga::create($wargaData);

            // Hapus data session setelah sukses
            Session::flush();

            return redirect()->route('warga.login')->with('success', 'Pendaftaran berhasil! Silakan login.');
            
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan warga:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => Session::get('data_diri')
            ]);
            
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function generateFilename($prefix, $nama, $kecamatan, $kelurahan, $rt_rw)
    {
        $timestamp = now()->format('His_dmY');
        return "{$prefix}_{$timestamp}_" . Str::slug($nama, '_') . "_" .
               Str::slug($kecamatan, '_') . "_" .
               Str::slug($kelurahan, '_') . "_" .
               Str::slug($rt_rw, '_') . ".png";
    }
}