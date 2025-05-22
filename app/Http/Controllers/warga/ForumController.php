<?php

namespace App\Http\Controllers\Warga;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForumController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search', '');

    // Ambil postingan utama
    $posts = DB::table('forum_post')
        ->leftJoin('warga', 'forum_post.warga_id', '=', 'warga.id')
        ->whereNull('forum_post.parent_id')
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('forum_post.topik', 'like', "%{$search}%")
                  ->orWhere('forum_post.pesan', 'like', "%{$search}%");
            });
        })
        ->select(
            'forum_post.*',
            'warga.nama_lengkap as warga_nama'
        )
        ->orderBy('forum_post.dibuat_pada', 'desc')
        ->get();

    // Hitung jumlah komentar
    $commentsCount = DB::table('forum_post')
        ->select('parent_id', DB::raw('COUNT(*) as total'))
        ->whereNotNull('parent_id')
        ->groupBy('parent_id')
        ->pluck('total', 'parent_id');

    foreach ($posts as $post) {
        $post->comments_count = $commentsCount[$post->id] ?? 0;
    }

    // Ambil komentar
    $comments = DB::table('forum_post')
        ->leftJoin('warga', 'forum_post.warga_id', '=', 'warga.id')
        ->whereNotNull('forum_post.parent_id')
        ->orderBy('forum_post.dibuat_pada')
        ->select(
            'forum_post.*',
            'warga.nama_lengkap as warga_nama'
        )
        ->get()
        ->groupBy('parent_id');

    return view('warga.forum', compact('posts', 'search', 'comments'));
}


    public function store(Request $request)
    {
        $request->validate([
            'topik' => 'nullable|string|max:255',
            'pesan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'parent_id' => 'nullable|exists:forum_post,id', // untuk komentar
        ]);
    
        $gambar_path = null;
    
        if ($request->hasFile('gambar')) {
            $gambar_path = $request->file('gambar')->store('forum', 'public');
        }
    
        $postData = [
            'warga_id'    => Auth::id(),
            'topik'       => $request->input('parent_id') ? null : $request->input('topik'), // hanya post utama yang punya topik
            'pesan'       => $request->input('pesan'),
            'gambar'      => $gambar_path,
            'dibuat_pada' => now(),
            'parent_id'   => $request->input('parent_id'), // null jika post utama, berisi ID jika komentar
        ];
    
        DB::table('forum_post')->insert($postData);
    
        return redirect()->route('warga.forum.index')->with('success', 'Postingan berhasil disimpan.');
    }

    
    
}
