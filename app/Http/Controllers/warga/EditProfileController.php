<?php

namespace App\Http\Controllers\Warga;

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
        // Debug - hapus setelah berhasil
        // dd([
        //     'request_all' => $request->all(),
        //     'has_file' => $request->hasFile('profile_pict'),
        //     'file_info' => $request->hasFile('profile_pict') ? $request->file('profile_pict') : null,
        //     'storage_exists' => file_exists(public_path('storage')),
        //     'is_link' => is_link(public_path('storage'))
        // ]);

        // Ambil data warga yang sedang login
        $warga = Warga::find(auth('warga')->id());

        if (!$warga) {
            return redirect()->route('warga.profile')
                ->with('error', 'Warga tidak ditemukan!');
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'telepon' => 'required|string|max:15|regex:/^[0-9]{10,15}$/',
            'alamat_lengkap' => 'required|string',
            'profile_pict' => 'nullable|image|mimes:jpeg,jpg,jpg,gif|max:2048'
        ], [
            'telepon.regex' => 'Nomor telepon harus 10-15 digit angka',
            'profile_pict.max' => 'Ukuran file terlalu besar (maksimal 2MB)'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Proses upload gambar
        $profilePicPath = $warga->profile_pict;

        if ($request->hasFile('profile_pict')) {
            try {
                // Metode 1: Menggunakan Storage (jika symbolic link sudah ada)
                if (file_exists(public_path('storage')) && is_link(public_path('storage'))) {
                    // Hapus gambar lama
                    if ($warga->profile_pict && 
                        $warga->profile_pict !== 'assets/img/default-profile.jpg' &&
                        Storage::disk('public')->exists($warga->profile_pict)) {
                        Storage::disk('public')->delete($warga->profile_pict);
                    }

                    // Simpan gambar baru
                    $fileName = time() . '_' . $request->file('profile_pict')->getClientOriginalName();
                    $path = $request->file('profile_pict')->storeAs('profile_picts', $fileName, 'public');
                    $profilePicPath = $path;
                } 
                // Metode 2: Menggunakan publicpath (alternatif jika symbolic link bermasalah)
                else {
                    $fileName = time() . '' . $request->file('profile_pict')->getClientOriginalName();
                    $uploadPath = public_path('storage/uploads/profile_picts');

                    // Buat folder jika belum ada
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    // Hapus gambar lama
                    if ($warga->profile_pict && 
                        $warga->profile_pict !== 'images/default-profile.jpg' &&
                        file_exists(public_path($warga->profile_pict))) {
                        unlink(public_path($warga->profile_pict));
                    }

                    // Pindahkan file
                    $request->file('profile_pict')->move($uploadPath, $fileName);
                    $profilePicPath = 'storage/uploads/profile_picts/' . $fileName;
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
            $warga->profile_pict = $profilePicPath;

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