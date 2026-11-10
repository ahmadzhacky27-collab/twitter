<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class FeedController extends Controller
{
    /**
     * Menampilkan halaman Beranda / Feed beserta postingan dan komentarnya.
     */
    public function index()
    {
        // Ambil postingan diurutkan dari yang terbaru (descending), beserta relasi user, likes, dan comments
        $posts = Post::with(['user', 'likes', 'comments.user'])
                    ->latest()
                    ->get();

        return view('feed.index', compact('posts'));
    }

    /**
     * Menyimpan postingan baru (Teks dan/atau Gambar).
     */
    public function storePost(Request $request): RedirectResponse
    {
        // Validasi: Teks atau gambar wajib ada (tidak boleh kosong keduanya)
        $request->validate([
            'content' => ['required_without:image', 'nullable', 'string'],
            'image' => ['required_without:content', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Status berhasil dipublikasikan.');
    }

    /**
     * Menghapus postingan (Hanya bisa dilakukan oleh pemilik sah).
     */
    public function destroyPost(Post $post): RedirectResponse
    {
        // Verifikasi kepemilikan postingan (Otoritas Hapus)
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Hapus file gambar post jika ada
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }

    /**
     * Logika Toggle Like (Like / Unlike).
     */
    public function toggleLike(Post $post): RedirectResponse
    {
        $userId = Auth::id();

        $existingLike = Like::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($existingLike) {
            $existingLike->delete(); // Batalkan like (unlike)
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]); // Berikan like baru
        }

        return back();
    }

    /**
     * Menambahkan komentar pada postingan.
     */
    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'comment' => ['required_without:image', 'nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Komentar dikirim.');
    }
}