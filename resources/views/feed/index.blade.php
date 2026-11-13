<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - ConnectSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen">

    <!-- Navbar Minimalis -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-400 flex items-center gap-2">
                <i class="fa-brands fa-twitter"></i> ConnectSpace
            </h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.show', Auth::id()) }}" class="flex items-center gap-2 hover:opacity-80">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                        alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-700">
                    <span class="font-medium text-sm hidden sm:inline text-gray-200">{{ Auth::user()->name }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-xs bg-red-950/60 text-red-400 border border-red-800/60 px-3 py-1.5 rounded-full hover:bg-red-900 transition font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-2xl mx-auto px-4 py-6">

        <!-- Notifikasi Error Validasi Global -->
        @if ($errors->any())
            <div class="mb-4 bg-red-950/60 border border-red-800 text-red-300 px-4 py-3 rounded-lg text-xs">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Post / Tweet Baru -->
        <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-4 mb-6">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex gap-3">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                        class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1">
                        <!-- Textarea dengan ID untuk batasan karakter -->
                        <textarea id="post-content" name="content" rows="3" placeholder="Apa yang sedang hangat dibicarakan?"
                            class="w-full bg-transparent border-none focus:ring-0 text-gray-100 resize-none text-base placeholder-gray-500 focus:outline-none"></textarea>

                        <!-- Indikator Penghitung Karakter -->
                        <div class="flex justify-between items-center text-xs text-gray-500 mt-1 mb-2">
                            <span id="char-count">0 / 500 karakter</span>
                            <span id="char-warning" class="text-red-400 hidden">Maksimal 500 karakter!</span>
                        </div>

                        <!-- Preview Gambar Unggulan & Tombol -->
                        <div class="flex items-center justify-between border-t border-gray-800 pt-3 mt-2">
                            <label
                                class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-1.5 text-sm font-medium">
                                <i class="fa-regular fa-image text-lg"></i>
                                <span>Lampirkan Foto</span>
                                <input type="file" name="image" accept="image/*" class="hidden">
                            </label>

                            <button type="submit" id="post-submit-btn"
                                class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-1.5 rounded-full font-semibold text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Posting
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Daftar Feed Status / Tweets -->
        <div class="space-y-4">
            @forelse($posts as $post)
                <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-4">
                    <!-- User Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}"
                                class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('profile.show', $post->user->id) }}"
                                        class="font-bold text-gray-100 hover:text-blue-400 transition leading-tight">{{ $post->user->name }}</a>

                                    @if ($post->user->location)
                                        <span class="text-gray-600">•</span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                                            {{ $post->user->location }}
                                        </span>
                                    @endif

                                    <span class="text-gray-600">•</span>
                                    <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Otoritas Edit & Hapus Status (Hanya Pemilik Sah) -->
                        @if (Auth::id() === $post->user_id)
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="toggleEditPost({{ $post->id }})"
                                    class="text-gray-400 hover:text-blue-400 text-sm transition" title="Edit Status">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')"
                                        class="text-gray-500 hover:text-red-400 text-sm transition"
                                        title="Hapus Status">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Tampilan Postingan Normal -->
                    <div id="post-view-{{ $post->id }}">
                        @if ($post->content)
                            <p class="text-gray-200 text-sm leading-relaxed mb-3 whitespace-pre-line">
                                {{ $post->content }}</p>
                        @endif

                        @if ($post->image)
                            <div class="rounded-lg overflow-hidden border border-gray-800 mb-3 max-h-96">
                                <img src="{{ asset('storage/' . $post->image) }}" class="w-full object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- Form Edit Postingan Inline -->
                    @if (Auth::id() === $post->user_id)
                        <form id="post-edit-form-{{ $post->id }}" action="{{ route('posts.update', $post->id) }}"
                            method="POST" enctype="multipart/form-data" class="hidden space-y-3 mb-3">
                            @csrf
                            @method('PATCH')
                            <textarea name="content" rows="3"
                                class="w-full bg-gray-950 border border-gray-700 text-gray-100 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none">{{ $post->content }}</textarea>

                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-400">
                                <label
                                    class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-1.5 font-medium">
                                    <i class="fa-regular fa-image text-base"></i>
                                    <span>{{ $post->image ? 'Ganti Foto' : 'Lampirkan Foto' }}</span>
                                    <input type="file" name="image" accept="image/*" class="hidden">
                                </label>

                                @if ($post->image)
                                    <label class="flex items-center gap-1.5 text-red-400 cursor-pointer">
                                        <input type="checkbox" name="remove_image" value="1"
                                            class="rounded bg-gray-950 border-gray-700 text-red-500 focus:ring-0">
                                        <span>Hapus foto saat ini</span>
                                    </label>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded-lg font-semibold text-xs transition">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="toggleEditPost({{ $post->id }})"
                                    class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-1.5 rounded-lg font-semibold text-xs transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Action Bar (Like & Info Komentar) -->
                    <div
                        class="flex items-center gap-6 border-t border-b border-gray-800 py-2 my-3 text-sm text-gray-400">
                        <!-- Toggle Like -->
                        <form action="{{ route('posts.like', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-1.5 hover:text-red-400 transition font-medium {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500' : '' }}">
                                <i
                                    class="{{ $post->likes->contains('user_id', Auth::id()) ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart"></i>
                                <span>{{ $post->likes->count() }}</span>
                            </button>
                        </form>

                        <!-- Counter Komentar -->
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-comment"></i>
                            <span>{{ $post->comments->count() }} Komentar</span>
                        </div>
                    </div>

                    <!-- List Komentar -->
                    <div class="space-y-3 pl-2 mt-4">
                        @foreach ($post->comments->whereNull('parent_id') as $comment)
                            <div class="bg-gray-950/50 p-2.5 rounded-lg border border-gray-800/60 space-y-2">
                                <div class="flex items-start gap-2.5">
                                    <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}"
                                        class="w-7 h-7 rounded-full object-cover mt-0.5">
                                    <div class="flex-1 text-xs">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <a href="{{ route('profile.show', $comment->user->id) }}"
                                                    class="font-bold text-gray-200 hover:text-blue-400">{{ $comment->user->name }}</a>

                                                @if ($comment->user->location)
                                                    <span class="text-gray-600 text-[10px]">•</span>
                                                    <span class="text-gray-400 text-[10px] flex items-center gap-1">
                                                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                                                        {{ $comment->user->location }}
                                                    </span>
                                                @endif

                                                <span class="text-gray-600 text-[10px]">•</span>
                                                <span
                                                    class="text-gray-500 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            <!-- Tombol Aksi Komentar -->
                                            <div class="flex items-center gap-2">
                                                <!-- Like Komentar -->
                                                <form action="{{ route('comments.like', $comment->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="flex items-center gap-1 text-[11px] {{ $comment->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                                                        <i
                                                            class="{{ $comment->likes->contains('user_id', Auth::id()) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                                        <span>{{ $comment->likes->count() }}</span>
                                                    </button>
                                                </form>

                                                @if (Auth::id() === $comment->user_id)
                                                    <button type="button"
                                                        onclick="toggleEditComment({{ $comment->id }})"
                                                        class="text-gray-400 hover:text-blue-400 transition"
                                                        title="Edit Komentar">
                                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                                    </button>
                                                    <form action="{{ route('comments.destroy', $comment->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus komentar ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-gray-400 hover:text-red-400 transition"
                                                            title="Hapus Komentar">
                                                            <i class="fa-regular fa-trash-can text-[10px]"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Teks Komentar Normal -->
                                        <div id="comment-view-{{ $comment->id }}">
                                            @if ($comment->comment)
                                                <p class="text-gray-300">{{ $comment->comment }}</p>
                                            @endif
                                            @if ($comment->image)
                                                <img src="{{ asset('storage/' . $comment->image) }}"
                                                    class="mt-2 rounded-md max-h-32 border border-gray-800">
                                            @endif
                                        </div>

                                        <!-- Form Edit Komentar Inline -->
                                        @if (Auth::id() === $comment->user_id)
                                            <form id="comment-edit-form-{{ $comment->id }}"
                                                action="{{ route('comments.update', $comment->id) }}" method="POST"
                                                enctype="multipart/form-data" class="hidden mt-2 space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="comment" value="{{ $comment->comment }}"
                                                    placeholder="Edit komentar..."
                                                    class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <div class="flex gap-2">
                                                    <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded-md font-semibold text-[11px]">Simpan</button>
                                                    <button type="button"
                                                        onclick="toggleEditComment({{ $comment->id }})"
                                                        class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-3 py-1 rounded-md font-semibold text-[11px]">Batal</button>
                                                </div>
                                            </form>
                                        @endif

                                        <!-- Tombol Pemicu Balas Komentar -->
                                        <button type="button" onclick="toggleReplyForm({{ $comment->id }})"
                                            class="text-[11px] text-blue-400 hover:underline mt-1">Balas</button>
                                    </div>
                                </div>

                                <!-- Menampilkan Daftar Balasan (Replies) -->
                                @if ($comment->replies->count() > 0)
                                    <div class="pl-6 space-y-2 border-l border-gray-800 mt-2">
                                        @foreach ($comment->replies as $reply)
                                            <div
                                                class="flex items-start gap-2 bg-gray-900/50 p-2 rounded border border-gray-800/40">
                                                <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) }}"
                                                    class="w-6 h-6 rounded-full object-cover">
                                                <div class="flex-1 text-xs">
                                                    <div class="flex justify-between items-center">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <a href="{{ route('profile.show', $reply->user->id) }}"
                                                                class="font-bold text-gray-200 hover:text-blue-400">{{ $reply->user->name }}</a>

                                                            @if ($reply->user->location)
                                                                <span class="text-gray-600 text-[10px]">•</span>
                                                                <span
                                                                    class="text-gray-400 text-[10px] flex items-center gap-1">
                                                                    <i class="fa-solid fa-location-dot text-[9px]"></i>
                                                                    {{ $reply->user->location }}
                                                                </span>
                                                            @endif

                                                            <span class="text-gray-600 text-[10px]">•</span>
                                                            <span
                                                                class="text-gray-500 text-[10px]">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <!-- Tombol Like untuk Reply -->
                                                            <form action="{{ route('comments.like', $reply->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="flex items-center gap-1 text-[10px] {{ $reply->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                                                                    <i
                                                                        class="{{ $reply->likes->contains('user_id', Auth::id()) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                                                    <span>{{ $reply->likes->count() }}</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="text-gray-300 mt-0.5">{{ $reply->comment }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Form Balas Komentar (Tersembunyi) -->
                                <form id="reply-form-{{ $comment->id }}"
                                    action="{{ route('comments.reply', $comment->id) }}" method="POST"
                                    class="hidden pl-6 mt-2">
                                    @csrf
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="comment"
                                            placeholder="Tulis balasan untuk {{ $comment->user->name }}..." required
                                            class="flex-1 bg-gray-900 border border-gray-800 text-gray-100 rounded-full px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-500 text-white px-2.5 py-1 rounded-full text-xs font-semibold">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form Input Komentar Baru -->
                    <form action="{{ route('comments.store', $post->id) }}" method="POST"
                        enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="flex items-center gap-2">
                            <input type="text" name="comment" placeholder="Tulis komentar..."
                                class="flex-1 bg-gray-950 border border-gray-800 text-gray-100 rounded-full px-3 py-1.5 text-xs focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder-gray-500">

                            <label class="cursor-pointer text-gray-500 hover:text-blue-400" title="Lampirkan foto">
                                <i class="fa-regular fa-image text-base"></i>
                                <input type="file" name="image" accept="image/*" class="hidden">
                            </label>

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-full text-xs font-semibold">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-900 rounded-xl border border-gray-800">
                    <p class="text-gray-400 text-sm">Belum ada postingan status. Jadilah yang pertama membuat
                        postingan!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
        // Validasi Maksimal Karakter 500 untuk Postingan Utama
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('post-content');
            const submitBtn = document.getElementById('post-submit-btn');
            const charCount = document.getElementById('char-count');
            const charWarning = document.getElementById('char-warning');
            const maxLength = 500;

            if (textarea && submitBtn) {
                textarea.addEventListener('input', function() {
                    const currentLength = textarea.value.length;
                    charCount.textContent = `${currentLength} / ${maxLength} karakter`;

                    if (currentLength > maxLength) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        charCount.classList.add('text-red-400');
                        charWarning.classList.remove('hidden');
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        charCount.classList.remove('text-red-400');
                        charWarning.classList.add('hidden');
                    }
                });
            }
        });

        function toggleEditPost(postId) {
            document.getElementById(`post-view-${postId}`).classList.toggle('hidden');
            document.getElementById(`post-edit-form-${postId}`).classList.toggle('hidden');
        }

        function toggleEditComment(commentId) {
            document.getElementById(`comment-view-${commentId}`).classList.toggle('hidden');
            document.getElementById(`comment-edit-form-${commentId}`).classList.toggle('hidden');
        }

        function toggleReplyForm(commentId) {
            document.getElementById(`reply-form-${commentId}`).classList.toggle('hidden');
        }
    </script>
</body>

</html>
