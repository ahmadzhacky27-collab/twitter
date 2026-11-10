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
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                         alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-700">
                    <span class="font-medium text-sm hidden sm:inline text-gray-200">{{ Auth::user()->name }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs bg-red-950/60 text-red-400 border border-red-800/60 px-3 py-1.5 rounded-full hover:bg-red-900 transition font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-2xl mx-auto px-4 py-6">

        <!-- Form Post / Tweet Baru -->
        <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-800 p-4 mb-6">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex gap-3">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                         class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1">
                        <textarea name="content" rows="3" 
                                  placeholder="Apa yang sedang hangat dibicarakan?" 
                                  class="w-full bg-transparent border-none focus:ring-0 text-gray-100 resize-none text-base placeholder-gray-500 focus:outline-none"></textarea>
                        
                        <!-- Preview Gambar Unggulan (opsional upload) -->
                        <div class="flex items-center justify-between border-t border-gray-800 pt-3 mt-2">
                            <label class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-1.5 text-sm font-medium">
                                <i class="fa-regular fa-image text-lg"></i>
                                <span>Lampirkan Foto</span>
                                <input type="file" name="image" accept="image/*" class="hidden">
                            </label>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-1.5 rounded-full font-semibold text-sm shadow-sm transition">
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
                                <h3 class="font-bold text-gray-100 leading-tight">{{ $post->user->name }}</h3>
                                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Otoritas Hapus Status (Hanya Pemilik Sah) -->
                        @if(Auth::id() === $post->user_id)
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')" 
                                        class="text-gray-500 hover:text-red-400 text-sm transition" title="Hapus Status">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Isi Teks Status -->
                    @if($post->content)
                        <p class="text-gray-200 text-sm leading-relaxed mb-3 whitespace-pre-line">{{ $post->content }}</p>
                    @endif

                    <!-- Gambar Status (Jika Ada) -->
                    @if($post->image)
                        <div class="rounded-lg overflow-hidden border border-gray-800 mb-3 max-h-96">
                            <img src="{{ asset('storage/' . $post->image) }}" class="w-full object-cover">
                        </div>
                    @endif

                    <!-- Action Bar (Like & Info Komentar) -->
                    <div class="flex items-center gap-6 border-t border-b border-gray-800 py-2 my-3 text-sm text-gray-400">
                        <!-- Toggle Like -->
                        <form action="{{ route('posts.like', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 hover:text-red-400 transition font-medium {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500' : '' }}">
                                <i class="{{ $post->likes->contains('user_id', Auth::id()) ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart"></i>
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
                        @foreach($post->comments as $comment)
                            <div class="flex items-start gap-2.5 bg-gray-950/50 p-2.5 rounded-lg border border-gray-800/60">
                                <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}" 
                                     class="w-7 h-7 rounded-full object-cover mt-0.5">
                                <div class="flex-1 text-xs">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-bold text-gray-200">{{ $comment->user->name }}</span>
                                        <span class="text-gray-500 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($comment->comment)
                                        <p class="text-gray-300">{{ $comment->comment }}</p>
                                    @endif
                                    @if($comment->image)
                                        <img src="{{ asset('storage/' . $comment->image) }}" class="mt-2 rounded-md max-h-32 border border-gray-800">
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Form Input Komentar Baru -->
                        <form action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input type="text" name="comment" placeholder="Tulis balasan..." 
                                       class="flex-1 bg-gray-950 border border-gray-800 text-gray-100 rounded-full px-3 py-1.5 text-xs focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder-gray-500">
                                
                                <label class="cursor-pointer text-gray-500 hover:text-blue-400" title="Lampirkan foto komentar">
                                    <i class="fa-regular fa-image text-base"></i>
                                    <input type="file" name="image" accept="image/*" class="hidden">
                                </label>

                                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-full text-xs font-semibold">
                                    Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-900 rounded-xl border border-gray-800">
                    <p class="text-gray-400 text-sm">Belum ada postingan status. Jadilah yang pertama membuat postingan!</p>
                </div>
            @endforelse
        </div>

    </div>

</body>
</html>