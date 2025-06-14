<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminEducationController extends Controller
{
    public function index()
    {
        $educations = Edukasi::latest()->paginate(10);
        return view('admin.educations.index', compact('educations'));
    }

    public function create()
    {
        return view('admin.educations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:Video,Artikel',
            'kategori_pengguna' => 'required|in:Warga,Kader',
            'tautan' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'durasi' => 'nullable|string|max:10',
            'kategori' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail_url'] = $request->file('thumbnail')->store('edukasi-thumbnails', 'public');
        }

        Edukasi::create($validated);

        return redirect()->route('admin.educations.index')
            ->with('success', 'Materi edukasi berhasil ditambahkan');
    }

    public function show(Edukasi $education)
    {
        return view('admin.educations.show', compact('education'));
    }

    public function edit(Edukasi $education)
    {
        return view('admin.educations.edit', compact('education'));
    }

    public function update(Request $request, Edukasi $education)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:Video,Artikel',
            'kategori_pengguna' => 'required|in:Warga,Kader',
            'tautan' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'durasi' => 'nullable|string|max:10',
            'kategori' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($education->thumbnail_url) {
                Storage::disk('public')->delete($education->thumbnail_url);
            }
            $validated['thumbnail_url'] = $request->file('thumbnail')->store('edukasi-thumbnails', 'public');
        }

        $education->update($validated);

        return redirect()->route('admin.educations.index')
            ->with('success', 'Materi edukasi berhasil diperbarui');
    }

    public function destroy(Edukasi $education)
    {
        // Delete thumbnail if exists
        if ($education->thumbnail_url) {
            Storage::disk('public')->delete($education->thumbnail_url);
        }

        $education->delete();

        return redirect()->route('admin.educations.index')
            ->with('success', 'Materi edukasi berhasil dihapus');
    }
}