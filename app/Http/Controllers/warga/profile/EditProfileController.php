<?php

namespace App\Http\Controllers\Warga\profile;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class EditProfileController extends Controller
{
    public function edit(): View
    {
        /** @var Warga $warga */
        $warga = Auth::guard('warga')->user();
        
        return view('warga.edit-profile', compact('warga'));
    }

    /**
     * Memperbarui data profil warga
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Warga $warga */
        $warga = Auth::guard('warga')->user();
       
        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:100',
                'min:3',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'telepon' => [
                'required',
                'string',
                'max:15',
                'min:10',
                'regex:/^[0-9+\-\s]+$/',
                'unique:warga,telepon,'.$warga->id
            ],
            'alamat_lengkap' => [
                'required',
                'string',
                'max:255'
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
            'profile_pict' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ]
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.min' => 'Nomor telepon minimal 10 digit.',
            'telepon.regex' => 'Format nomor telepon tidak valid.',
            'telepon.unique' => 'Nomor telepon sudah digunakan oleh warga lain.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'alamat_lengkap.max' => 'Alamat maksimal 255 karakter.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.letters' => 'Password harus mengandung huruf.',
            'new_password.mixed_case' => 'Password harus mengandung huruf besar dan kecil.',
            'new_password.numbers' => 'Password harus mengandung angka.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'current_password.required_with' => 'Password saat ini diperlukan untuk mengubah password.',
            'profile_pict.image' => 'File harus berupa gambar.',
            'profile_pict.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'profile_pict.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        // Verify current password if changing password
        if ($request->filled('new_password') && !Hash::check($validated['current_password'], $warga->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak valid']);
        }

        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_pict')) {
                // Delete old picture if exists
                if ($warga->profile_pict && Storage::exists('public/' . $warga->profile_pict)) {
                    Storage::delete('public/' . $warga->profile_pict);
                }
                
                // Upload new picture with custom naming
                $fileName = 'warga_' . $warga->id . '_' . time() . '.' . $request->file('profile_pict')->extension();
                $path = $request->file('profile_pict')->storeAs(
                    'profile_pictures/warga', 
                    $fileName,
                    'public'
                );
                $warga->profile_pict = $path;
            }

            // Update basic info
            $warga->nama_lengkap = trim($validated['nama_lengkap']);
            $warga->telepon = $this->formatPhoneNumber($validated['telepon']);
            $warga->alamat_lengkap = $validated['alamat_lengkap'];

            // Update password if provided
            if (!empty($validated['new_password'])) {
                $warga->password = Hash::make($validated['new_password']);
            }

            $warga->save();
            
            // Log activity
            \Log::info('Warga profile updated', [
                'warga_id' => $warga->id,
                'updated_fields' => array_keys($validated)
            ]);

            return redirect()->route('warga.profile')
                   ->with('success', 'Profil berhasil diperbarui! 🎉');
                   
        } catch (\Exception $e) {
            \Log::error('Failed to update warga profile', [
                'warga_id' => $warga->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                   ->with('error', 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.')
                   ->withInput();
        }
    }

    /**
     * Hapus foto profil warga
     */
    public function deletePhoto(): RedirectResponse
    {
        /** @var Warga $warga */
        $warga = Auth::guard('warga')->user();
        
        try {
            if ($warga->profile_pict) {
                // Hapus file foto
                if (Storage::exists('public/' . $warga->profile_pict)) {
                    Storage::delete('public/' . $warga->profile_pict);
                }
                
                // Update database
                $warga->profile_pict = null;
                $warga->save();
                
                return redirect()->route('warga.profile')
                       ->with('success', 'Foto profil berhasil dihapus.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete profile photo', [
                'warga_id' => $warga->id,
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