<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Kader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class EditProfileKaderController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil (edit profil)
     */
    public function edit(): View
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
        
        return view('kader.edit_profile', compact('kader'));
    }

    /**
     * Memperbarui data profil kader
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
       
        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:100',
                'min:3',
                'regex:/^[a-zA-Z\s]+$/' // Hanya huruf dan spasi
            ],
            'telepon' => [
                'required',
                'string',
                'max:15',
                'min:10',
                'regex:/^[0-9+\-\s]+$/', // Format nomor telepon
                'unique:kader,telepon,'.$kader->id
            ],
            'current_password' => 'required_with:new_password',
            'new_password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
            'profil_pict' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048' // 2MB max
            ]
        ], [
            // Custom error messages
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.min' => 'Nomor telepon minimal 10 digit.',
            'telepon.regex' => 'Format nomor telepon tidak valid.',
            'telepon.unique' => 'Nomor telepon sudah digunakan oleh kader lain.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.letters' => 'Password harus mengandung huruf.',
            'new_password.mixed_case' => 'Password harus mengandung huruf besar dan kecil.',
            'new_password.numbers' => 'Password harus mengandung angka.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'current_password.required_with' => 'Password saat ini diperlukan untuk mengubah password.',
            'profil_pict.image' => 'File harus berupa gambar.',
            'profil_pict.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'profil_pict.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        // Verify current password if changing password
        if ($request->filled('new_password') && !Hash::check($validated['current_password'], $kader->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak valid']);
        }

        try {
            // Handle profile picture upload
            if ($request->hasFile('profil_pict')) {
                // Delete old picture if exists
                if ($kader->profil_pict && Storage::exists('public/' . $kader->profil_pict)) {
                    Storage::delete('public/' . $kader->profil_pict);
                }
                
                // Upload new picture
                $path = $request->file('profil_pict')->store('profile_pictures', 'public');
                $kader->profil_pict = $path;
            }

            // Update basic info
            $kader->nama_lengkap = trim($validated['nama_lengkap']);
            $kader->telepon = $this->formatPhoneNumber($validated['telepon']);

            // Update password if provided
            if (!empty($validated['new_password'])) {
                $kader->password = Hash::make($validated['new_password']);
            }

            $kader->save();
            
            // Log activity
            \Log::info('Kader profile updated', [
                'kader_id' => $kader->id,
                'updated_fields' => array_keys($validated)
            ]);

            return redirect()->route('kader.profile')
                   ->with('success', 'Profil berhasil diperbarui! 🎉');
                   
        } catch (\Exception $e) {
            \Log::error('Failed to update kader profile', [
                'kader_id' => $kader->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                   ->with('error', 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.')
                   ->withInput();
        }
    }

    /**
     * Hapus foto profil kader
     */
    public function deletePhoto(): RedirectResponse
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
        
        try {
            if ($kader->profil_pict) {
                // Hapus file foto
                if (Storage::exists('public/' . $kader->profil_pict)) {
                    Storage::delete('public/' . $kader->profil_pict);
                }
                
                // Update database
                $kader->profil_pict = null;
                $kader->save();
                
                return redirect()->route('kader.profile')
                       ->with('success', 'Foto profil berhasil dihapus.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete profile photo', [
                'kader_id' => $kader->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                   ->with('error', 'Gagal menghapus foto profil.');
        }

        return redirect()->back()
               ->with('info', 'Tidak ada foto profil untuk dihapus.');
    }

    /**
     * Format nomor telepon ke format standar
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-digit kecuali +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Jika dimulai dengan 08, ganti dengan +62
        if (str_starts_with($phone, '08')) {
            $phone = '+62' . substr($phone, 1);
        }
        
        // Jika dimulai dengan 8 (tanpa 0), tambahkan +62
        if (str_starts_with($phone, '8') && !str_starts_with($phone, '+')) {
            $phone = '+62' . $phone;
        }
        
        return $phone;
    }
}