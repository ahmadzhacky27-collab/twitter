<div class="space-y-2 text-xs" id="comment-{{ $comment->id }}">
    <div class="bg-gray-950/40 border border-gray-800/80 rounded-lg p-2.5">
        
        <!-- Header: Avatar & Nama Bisa Diklik -->
        <div class="flex justify-between items-start mb-1.5">
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.show', $comment->user->id) }}" class="hover:opacity-80 transition shrink-0">
                    <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}"
                        class="w-6 h-6 rounded-full object-cover">
                </a>
                <div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('profile.show', $comment->user->id) }}" class="font-bold text-gray-200 hover:text-blue-400 transition">
                            {{ $comment->user->name }}
                        </a>

                        @if ($comment->user->location)
                        <span class="text-gray-600">•</span>
                        <span class="text-[10px] text-gray-400 flex items-center gap-0.5">
                            <i class="fa-solid fa-location-dot text-[8px]"></i>
                            {{ $comment->user->location }}
                        </span>
                        @endif

                        <span class="text-gray-600">•</span>
                        <span class="text-gray-500 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Otoritas Edit & Hapus Komentar -->
            @if (Auth::id() === $comment->user_id)
            <div class="flex items-center gap-2 text-gray-400">
                <button type="button" onclick="toggleEditComment({{ $comment->id }})" class="hover:text-blue-400 transition" title="Edit Komentar">
                    <i class="fa-solid fa-pen text-[10px]"></i>
                </button>
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="hover:text-red-400 transition" title="Hapus Komentar">
                        <i class="fa-regular fa-trash-can text-[10px]"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Tampilan Isi Komentar Normal -->
        <div id="comment-view-{{ $comment->id }}">
            @if ($comment->comment)
            <p class="text-gray-300 text-xs leading-relaxed whitespace-pre-line mb-2 pl-8">
                {{ $comment->comment }}
            </p>
            @endif

            @if ($comment->image)
            <div class="pl-8 mb-2">
                <div class="rounded-lg overflow-hidden border border-gray-800 bg-gray-900 max-w-xs max-h-48">
                    <img src="{{ asset('storage/' . $comment->image) }}" class="w-full h-full object-cover">
                </div>
            </div>
            @endif
        </div>

        <!-- Form Edit Komentar (Inline) -->
        @if (Auth::id() === $comment->user_id)
        <form id="comment-edit-form-{{ $comment->id }}" action="{{ route('comments.update', $comment->id) }}" method="POST" enctype="multipart/form-data" class="hidden space-y-2 mt-2 pl-8">
            @csrf @method('PATCH')
            <textarea name="comment" rows="2" class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-lg p-2 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none">{{ $comment->comment }}</textarea>

            <div class="flex flex-wrap items-center justify-between gap-2 text-[10px] text-gray-400">
                <label class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-1 font-medium">
                    <i class="fa-regular fa-image text-xs"></i>
                    <span>{{ $comment->image ? 'Ganti Foto' : 'Lampirkan Foto' }}</span>
                    <input type="file" name="image" accept="image/*" class="hidden">
                </label>

                @if ($comment->image)
                <label class="flex items-center gap-1 text-red-400 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="rounded bg-gray-900 border-gray-700 text-red-500 focus:ring-0">
                    <span>Hapus foto</span>
                </label>
                @endif
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded text-[10px] font-semibold">Simpan</button>
                <button type="button" onclick="toggleEditComment({{ $comment->id }})" class="bg-gray-800 text-gray-300 px-3 py-1 rounded text-[10px]">Batal</button>
            </div>
        </form>
        @endif

        <!-- Action Bar Komentar (Tombol Like & Tombol Balas) -->
        <div class="flex items-center gap-4 pl-8 mt-1 text-[11px]">
            <!-- Tombol Like Komentar / Reply -->
            <form action="{{ route('comments.like', $comment->id) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-1 hover:text-red-400 transition font-medium {{ $comment->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-400' }}">
                    <i class="{{ $comment->likes->contains('user_id', Auth::id()) ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-[10px]"></i>
                    <span>{{ $comment->likes->count() }}</span>
                </button>
            </form>

            <!-- Tombol Pemicu Form Balas (Reply) -->
            <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-blue-400 hover:underline font-medium">
                Balas
            </button>
        </div>

        <!-- Form Balas Komentar (Support Gambar & Wajib ada input hidden parent_id) -->
        <div id="reply-form-{{ $comment->id }}" class="hidden mt-2 pl-8">
            <form action="{{ route('comments.store', $comment->post_id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="flex items-center gap-2">
                    <input type="text" name="comment" placeholder="Tulis balasan..."
                        class="flex-1 bg-gray-900 border border-gray-800 text-gray-100 rounded-full px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder-gray-500">
                    
                    <label class="cursor-pointer text-gray-400 hover:text-blue-400 px-1" title="Lampirkan foto">
                        <i class="fa-regular fa-image text-sm"></i>
                        <input type="file" name="image" accept="image/*" class="hidden">
                    </label>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-full text-[10px] font-semibold">
                        Kirim
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Balasan (Nested Replies) -->
        @if ($comment->replies && $comment->replies->count() > 0)
        <div class="pl-4 mt-3 space-y-2 border-l border-gray-800 ml-6">
            @foreach ($comment->replies as $reply)
                @include('posts.partials.comment-item', ['comment' => $reply])
            @endforeach
        </div>
        @endif

    </div>
</div>