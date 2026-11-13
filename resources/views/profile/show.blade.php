<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil {{ $user->name }} - ConnectSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen">

    <!-- Navbar Minimalis -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('feed.index') }}"
                class="text-xl font-bold text-blue-400 flex items-center gap-2 hover:opacity-80">
                <i class="fa-brands fa-twitter"></i> ConnectSpace
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80">
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

        <!-- Tombol Kembali ke Beranda -->
        <div class="mb-4">
            <a href="{{ route('feed.index') }}" class="text-xs text-blue-400 hover:underline flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- Profil Header Card -->
        <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                    alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-blue-500 shadow-md">

                <div class="flex-1 text-center sm:text-left">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-xl font-bold text-gray-100">{{ $user->name }}</h2>

                        @if (Auth::id() === $user->id)
                            <a href="{{ route('profile.edit') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-gray-200 px-4 py-1.5 rounded-full text-xs font-semibold transition border border-gray-700">
                                Edit Profil
                            </a>
                        @endif
                    </div>

                    @if ($user->location)
                        <p class="text-xs text-gray-400 flex items-center justify-center sm:justify-start gap-1 mt-1">
                            <i class="fa-solid fa-location-dot text-blue-400"></i> {{ $user->location }}
                        </p>
                    @endif

                    <p class="text-sm text-gray-300 mt-3 whitespace-pre-line">
                        {{ $user->bio ?? 'Belum ada bio.' }}
                    </p>

                    <div
                        class="flex items-center justify-center sm:justify-start gap-6 mt-4 pt-3 border-t border-gray-800 text-xs text-gray-400">
                        <div>
                            <span class="font-bold text-gray-200">{{ $posts->count() }}</span> Postingan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-gray-400 mb-3 uppercase tracking-wider">Postingan oleh
            {{ $user->name }}</h3>

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
                                    <span class="font-bold text-gray-100 leading-tight">{{ $post->user->name }}</span>

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

                        <!-- Otoritas Edit & Hapus Status -->
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

                    <!-- Action Bar (Like & Komentar) -->
                    <div
                        class="flex items-center gap-6 border-t border-b border-gray-800 py-2 my-3 text-sm text-gray-400">
                        <form action="{{ route('posts.like', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-1.5 hover:text-red-400 transition font-medium {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500' : '' }}">
                                <i
                                    class="{{ $post->likes->contains('user_id', Auth::id()) ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart"></i>
                                <span>{{ $post->likes->count() }}</span>
                            </button>
                        </form>

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
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('profile.show', $comment->user->id) }}"
                                                    class="font-bold text-gray-200 hover:text-blue-400">{{ $comment->user->name }}</a>
                                                <span
                                                    class="text-gray-500 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            <div class="flex items-center gap-2">
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
                                                            title="Hapus">
                                                            <i class="fa-regular fa-trash-can text-[10px]"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        <div id="comment-view-{{ $comment->id }}">
                                            @if ($comment->comment)
                                                <p class="text-gray-300">{{ $comment->comment }}</p>
                                            @endif
                                            @if ($comment->image)
                                                <img src="{{ asset('storage/' . $comment->image) }}"
                                                    class="mt-2 rounded-md max-h-32 border border-gray-800">
                                            @endif
                                        </div>

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

                                        <button type="button" onclick="toggleReplyForm({{ $comment->id }})"
                                            class="text-[11px] text-blue-400 hover:underline mt-1">Balas</button>
                                    </div>
                                </div>

                                <!-- Balasan Komentar (Replies) -->
                                @if ($comment->replies->count() > 0)
                                    <div class="pl-6 space-y-2 border-l border-gray-800 mt-2">
                                        @foreach ($comment->replies as $reply)
                                            <div
                                                class="flex items-start gap-2 bg-gray-900/50 p-2 rounded border border-gray-800/40">
                                                <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) }}"
                                                    class="w-6 h-6 rounded-full object-cover">
                                                <div class="flex-1 text-xs">
                                                    <div class="flex justify-between items-center">
                                                        <a href="{{ route('profile.show', $reply->user->id) }}"
                                                            class="font-bold text-gray-200 hover:text-blue-400">{{ $reply->user->name }}</a>

                                                        <div class="flex items-center gap-2">
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
                                                            <span
                                                                class="text-gray-500 text-[10px]">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                    <p class="text-gray-300 mt-0.5">{{ $reply->comment }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

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

                        <form action="{{ route('comments.store', $post->id) }}" method="POST"
                            enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input type="text" name="comment" placeholder="Tulis komentar..."
                                    class="flex-1 bg-gray-950 border border-gray-800 text-gray-100 rounded-full px-3 py-1.5 text-xs focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder-gray-500">
                                <label class="cursor-pointer text-gray-500 hover:text-blue-400"
                                    title="Lampirkan foto">
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
                </div>
            @empty
                <div class="text-center py-10 bg-gray-900 rounded-xl border border-gray-800">
                    <p class="text-gray-400 text-sm">{{ $user->name }} belum memiliki postingan status.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
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
