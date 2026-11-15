<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Postingan - ConnectSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen">

    <!-- Navbar Minimalis -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('feed.index') }}" class="text-xl font-bold text-blue-400 flex items-center gap-2 hover:opacity-80 transition">
                <i class="fa-brands fa-twitter"></i> ConnectSpace
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.show', Auth::id()) }}" class="flex items-center gap-2 hover:opacity-80">
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
            <a href="{{ route('feed.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-blue-400 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
        <div class="mb-4 bg-emerald-950/60 border border-emerald-800 text-emerald-300 px-4 py-3 rounded-lg text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Notifikasi Error Validasi -->
        @if ($errors->any())
        <div class="mb-4 bg-red-950/60 border border-red-800 text-red-300 px-4 py-3 rounded-lg text-xs">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Card Postingan Utama dengan ID Post Card -->
        <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-4" id="post-card-{{ $post->id }}">
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

                <!-- Otoritas Edit & Hapus Postingan -->
                @if (Auth::id() === $post->user_id)
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleEditPost({{ $post->id }})"
                        class="text-gray-400 hover:text-blue-400 text-sm transition" title="Edit Status">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')"
                            class="text-gray-500 hover:text-red-400 text-sm transition" title="Hapus Status">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Tampilan Postingan Normal -->
            <div id="post-view-{{ $post->id }}">
                @if ($post->content)
                <div class="block">
                    <p class="text-gray-200 text-sm leading-relaxed mb-3 whitespace-pre-line">
                        {{ $post->content }}
                    </p>
                </div>
                @endif

                @if ($post->image)
                <div class="block rounded-lg overflow-hidden border border-gray-800 mb-3 max-h-[500px]">
                    <img src="{{ asset('storage/' . $post->image) }}" class="w-full object-cover">
                </div>
                @endif
            </div>

            <!-- Form Edit Postingan Inline -->
            @if (Auth::id() === $post->user_id)
            <form id="post-edit-form-{{ $post->id }}" action="{{ route('posts.update', $post->id) }}"
                method="POST" enctype="multipart/form-data" class="hidden space-y-3 mb-3">
                @csrf @method('PATCH')
                <textarea name="content" rows="3"
                    class="w-full bg-gray-950 border border-gray-700 text-gray-100 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none">{{ $post->content }}</textarea>

                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-400">
                    <label class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-1.5 font-medium">
                        <i class="fa-regular fa-image text-base"></i>
                        <span>{{ $post->image ? 'Ganti Foto' : 'Lampirkan Foto' }}</span>
                        <input type="file" name="image" accept="image/*" class="hidden">
                    </label>

                    @if ($post->image)
                    <label class="flex items-center gap-1.5 text-red-400 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded bg-gray-950 border-gray-700 text-red-500 focus:ring-0">
                        <span>Hapus foto saat ini</span>
                    </label>
                    @endif
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded-lg font-semibold text-xs transition">Simpan Perubahan</button>
                    <button type="button" onclick="toggleEditPost({{ $post->id }})" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-1.5 rounded-lg font-semibold text-xs transition">Batal</button>
                </div>
            </form>
            @endif

            <!-- Action Bar (Like & Tombol Toggle Komentar) -->
            <div class="flex items-center gap-6 border-t border-b border-gray-800 py-2 my-3 text-sm text-gray-400">
                <form action="{{ route('posts.like', $post->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 hover:text-red-400 transition font-medium {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500' : '' }}">
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
                <!-- List Komentar Utama -->
                <div class="space-y-3 pl-2" id="comment-section">
                    @forelse ($post->comments->whereNull('parent_id') as $comment)
                    @include('posts.partials.comment-item', ['comment' => $comment])
                    @empty
                    <p class="text-center text-gray-500 text-xs py-4">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    @endforelse
                </div>

                <!-- Form Input Komentar Baru -->
                <form action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-3 border-t border-gray-800">
                    @csrf
                    <div class="flex items-center gap-2">
                        <input type="text" name="comment" placeholder="Tulis komentar..."
                            class="flex-1 bg-gray-950 border border-gray-800 text-gray-100 rounded-full px-3.5 py-2 text-xs focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder-gray-500">

                        <label class="cursor-pointer text-gray-500 hover:text-blue-400 px-1" title="Lampirkan foto">
                            <i class="fa-regular fa-image text-base"></i>
                            <input type="file" name="image" accept="image/*" class="hidden">
                        </label>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-full text-xs font-semibold transition">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
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

        // Auto-scroll dan buka kembali kontainer komentar setelah submit aksi (Bersih dari error linter)
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