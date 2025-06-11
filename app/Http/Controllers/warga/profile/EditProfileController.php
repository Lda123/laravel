<?php

namespace App\Http\Controllers\Warga\profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Warga;

class EditProfileController extends Controller
{
    public function edit()
    {
        $warga = auth('warga')->user();
        return view('warga.edit-profile', compact('warga'));
    }

    public function update(Request $request)
    {
        $warga = Warga::find(auth('warga')->id());

        if (!$warga) {
            return redirect()->route('warga.profile')
                ->with('error', 'Warga tidak ditemukan!');
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'telepon' => 'required|string|max:15|regex:/^[0-9]{10,15}$/',
            'alamat_lengkap' => 'required|string',
            'profile_pictures' => 'nullable|image|mimes:jpeg,jpg,jpg,gif|max:2048'
        ], [
            'telepon.regex' => 'Nomor telepon harus 10-15 digit angka',
            'profile_pictures.max' => 'Ukuran file terlalu besar (maksimal 2MB)'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Proses upload gambar
        $profilePicPath = $warga->profile_pictures;

        if ($request->hasFile('profile_pictures')) {
            try {
                // Metode 1: Menggunakan Storage (jika symbolic link sudah ada)
                if (file_exists(public_path('storage')) && is_link(public_path('storage'))) {
                    // Hapus gambar lama
                    if ($warga->profile_pictures && 
                        $warga->profile_pictures !== 'assets/img/default-profile.jpg' &&
                        Storage::disk('public')->exists($warga->profile_pictures)) {
                        Storage::disk('public')->delete($warga->profile_pictures);
                    }

                    // Simpan gambar baru
                    $fileName = time() . '_' . $request->file('profile_pictures')->getClientOriginalName();
                    $path = $request->file('profile_pictures')->storeAs('profile_pictures', $fileName, 'public');
                    $profilePicPath = $path;
                } 
                // Metode 2: Menggunakan publicpath (alternatif jika symbolic link bermasalah)
                else {
                    $fileName = time() . '' . $request->file('profile_pictures')->getClientOriginalName();
                    $uploadPath = public_path('storage/profile_pictures');

                    // Buat folder jika belum ada
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    // Hapus gambar lama
                    if ($warga->profile_pictures && 
                        $warga->profile_pictures !== 'images/default-profile.jpg' &&
                        file_exists(public_path($warga->profile_pictures))) {
                        unlink(public_path($warga->profile_pictures));
                    }

                    // Pindahkan file
                    $request->file('profile_pictures')->move($uploadPath, $fileName);
                    $profilePicPath = 'storage/profile_pictures/' . $fileName;
                }

            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage())
                    ->withInput();
            }
        }

        try {
            // Update data
            $warga->nama_lengkap = $request->nama_lengkap;
            $warga->telepon = $request->telepon;
            $warga->alamat_lengkap = $request->alamat_lengkap;
            $warga->profile_pictures = $profilePicPath;

            $result = $warga->save();

            if ($result) {
                return redirect()->route('warga.profile')
                    ->with('success', 'Profil berhasil diperbarui!');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menyimpan perubahan profil!')
                    ->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage())
                ->withInput();
        }
    }
}