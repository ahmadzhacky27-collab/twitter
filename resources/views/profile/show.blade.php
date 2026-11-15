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
                class="text-xl font-bold text-blue-400 flex items-center gap-2 hover:opacity-80 transition">
                <i class="fa-brands fa-twitter"></i> ConnectSpace
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                        alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-700">
                    <span class="font-medium text-sm hidden sm:inline text-gray-200">{{ Auth::user()->name }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?')">
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
            {{ $user->name }}
        </h3>

        <!-- Daftar Feed Status / Tweets -->
        <div class="space-y-4">
            @forelse($posts as $post)
            <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-4" id="post-card-{{ $post->id }}">
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

                <!-- Tampilan Postingan Normal (Bisa diklik ke halaman detail) -->
                <div id="post-view-{{ $post->id }}">
                    @if ($post->content)
                    <a href="{{ route('posts.show', $post->id) }}" class="block group">
                        <p class="text-gray-200 text-sm leading-relaxed mb-3 whitespace-pre-line group-hover:text-blue-300 transition">
                            {{ $post->content }}
                        </p>
                    </a>
                    @endif

                    @if ($post->image)
                    <a href="{{ route('posts.show', $post->id) }}" class="block rounded-lg overflow-hidden border border-gray-800 mb-3 max-h-96 hover:opacity-95 transition">
                        <img src="{{ asset('storage/' . $post->image) }}" class="w-full object-cover">
                    </a>
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

                <!-- Action Bar (Like & Tombol Komentar Toggle) -->
                <div class="flex items-center gap-6 border-t border-b border-gray-800 py-2 my-3 text-sm text-gray-400">
                    <form action="{{ route('posts.like', $post->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1.5 hover:text-red-400 transition font-medium {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500' : '' }}">
                            <i class="{{ $post->likes->contains('user_id', Auth::id()) ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart"></i>
                            <span>{{ $post->likes->count() }}</span>
                        </button>
                    </form>

                    <button type="button" onclick="toggleComments({{ $post->id }})" class="flex items-center gap-1.5 hover:text-blue-400 transition">
                        <i class="fa-regular fa-comment"></i>
                        <span>{{ $post->comments->count() }} Komentar</span>
                    </button>
                </div>

                <!-- Kontainer Komentar & Form (Disembunyikan secara Default dengan class 'hidden') -->
                <div id="comments-container-{{ $post->id }}" class="hidden space-y-3 mt-3 pt-2 border-t border-gray-800/60">
                    <!-- List Komentar -->
                    <div class="space-y-3 pl-2">
                        @forelse ($post->comments->whereNull('parent_id') as $comment)
                        @include('posts.partials.comment-item', ['comment' => $comment])
                        @empty
                        <p class="text-center text-gray-500 text-xs py-2">Belum ada komentar.</p>
                        @endforelse
                    </div>

                    <!-- Form Input Komentar Baru -->
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

        function toggleComments(postId) {
            const container = document.getElementById(`comments-container-${postId}`);
            if (container) {
                container.classList.toggle('hidden');
            }
        }

        // Auto-scroll dan buka kembali card serta komentar yang baru saja di-submit
        document.addEventListener("DOMContentLoaded", function() {
            const activePostId = "{{ session('active_post_id') }}";
            if (activePostId && activePostId !== "") {
                const commentsContainer = document.getElementById(`comments-container-${activePostId}`);
                if (commentsContainer) {
                    commentsContainer.classList.remove('hidden');
                }
                const postCard = document.getElementById(`post-card-${activePostId}`);
                if (postCard) {
                    postCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
</body>

</html>