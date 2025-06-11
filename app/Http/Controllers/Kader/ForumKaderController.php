<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ForumKaderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $posts = ForumPost::with(['kader', 'comments.kader'])
            ->whereNull('parent_id')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('topik', 'like', "%{$search}%")
                      ->orWhere('pesan', 'like', "%{$search}%");
                });
            })
            ->orderBy('dibuat_pada', 'desc') // Changed from created_at to dibuat_pada
            ->paginate(10);

        return view('kader.forum', compact('posts', 'search'));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'topik' => 'required|string|max:255',
            'pesan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $imagePath = $this->storeImage($request->file('gambar'));
        }

        ForumPost::create([
            'kader_id' => auth('kader')->id(),
            'topik' => $request->topik,
            'pesan' => $request->pesan,
            'gambar' => $imagePath,
        ]);

        return redirect()->route('kader.forum')->with('success', 'Diskusi berhasil diposting!');
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:forum_post,id', // Fixed table name
            'pesan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $imagePath = $this->storeImage($request->file('gambar'));
        }

        ForumPost::create([
            'kader_id' => auth('kader')->id(),
            'parent_id' => $request->parent_id,
            'pesan' => $request->pesan,
            'gambar' => $imagePath,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    private function storeImage($image)
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('forum/kader', $filename, 'public');
        return $path;
    }
}