<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Models\CommentLike;
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
        $posts = Post::with([
            'user',
            'likes',
            'comments.user',
            'comments.likes',
            'comments.replies.user',
            'comments.replies.likes' // Pastikan relasi likes untuk replies dimuat
        ])
            ->latest()
            ->get();

        return view('feed.index', compact('posts'));
    }

    /**
     * Menyimpan postingan baru (Teks dan/atau Gambar).
     */
    public function storePost(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => ['required_without:image', 'nullable', 'string', 'max:500'],
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
     * Memperbarui postingan (Teks, Ganti Gambar, atau Hapus Gambar).
     */
    public function updatePost(Request $request, Post $post): RedirectResponse
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'content' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $hasImage = $request->hasFile('image') || ($post->image && !$request->boolean('remove_image'));
        if (empty(trim($request->content)) && !$hasImage) {
            return back()->withErrors(['content' => 'Postingan tidak boleh kosong.']);
        }

        $data = [
            'content' => $request->content,
        ];

        if ($request->boolean('remove_image') && $post->image) {
            if (Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return back()->with('success', 'Postingan berhasil diperbarui.');
    }

    /**
     * Menghapus postingan.
     */
    public function destroyPost(Post $post): RedirectResponse
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }

    /**
     * Logika Toggle Like Postingan.
     */
    public function toggleLike(Post $post): RedirectResponse
    {
        $userId = Auth::id();
        $existingLike = Like::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
        }

        return back();
    }

    /**
     * Menambahkan komentar pada postingan.
     */
    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'comment' => ['required_without:image', 'nullable', 'string', 'max:500'],
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

    /**
     * Menyimpan balasan komentar (Reply).
     */
    public function storeCommentReply(Request $request, Comment $comment): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'max:500'],
        ]);

        Comment::create([
            'post_id' => $comment->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Balasan dikirim.');
    }

    /**
     * Toggle Like pada Komentar.
     */
    public function toggleCommentLike(Comment $comment): RedirectResponse
    {
        $userId = Auth::id();
        $existingLike = CommentLike::where('user_id', $userId)->where('comment_id', $comment->id)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            CommentLike::create([
                'user_id' => $userId,
                'comment_id' => $comment->id,
            ]);
        }

        return back();
    }

    /**
     * Perbarui isi komentar.
     */
    public function updateComment(Request $request, Comment $comment): RedirectResponse
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        $request->validate([
            'comment' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $hasImage = $request->hasFile('image') || ($comment->image && !$request->boolean('remove_image'));
        if (empty(trim($request->comment)) && !$hasImage) {
            return back()->withErrors(['comment' => 'Komentar tidak boleh kosong.']);
        }

        $data = [
            'comment' => $request->comment,
        ];

        if ($request->boolean('remove_image') && $comment->image) {
            if (Storage::disk('public')->exists($comment->image)) {
                Storage::disk('public')->delete($comment->image);
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($comment->image && Storage::disk('public')->exists($comment->image)) {
                Storage::disk('public')->delete($comment->image);
            }
            $data['image'] = $request->file('image')->store('comments', 'public');
        }

        $comment->update($data);

        return back()->with('success', 'Komentar berhasil diperbarui.');
    }

    /**
     * Hapus komentar.
     */
    public function destroyComment(Comment $comment): RedirectResponse
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        if ($comment->image && Storage::disk('public')->exists($comment->image)) {
            Storage::disk('public')->delete($comment->image);
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
