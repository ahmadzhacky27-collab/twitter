<div class="bg-gray-950/50 p-3 rounded-lg border border-gray-800/60 space-y-2" id="comment-{{ $comment->id }}">
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
                    <span class="text-gray-500 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                </div>

                <!-- Aksi Komentar / Reply (Like, Edit, Hapus) -->
                <div class="flex items-center gap-2">
                    <form action="{{ route('comments.like', $comment->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1 text-[11px] {{ $comment->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                            <i class="{{ $comment->likes->contains('user_id', Auth::id()) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                            <span>{{ $comment->likes->count() }}</span>
                        </button>
                    </form>

                    @if (Auth::id() === $comment->user_id)
                        <button type="button" onclick="toggleEditComment({{ $comment->id }})"
                            class="text-gray-400 hover:text-blue-400 transition" title="Edit">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST"
                            onsubmit="return confirm('Hapus komentar/balasan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-400 transition" title="Hapus">
                                <i class="fa-regular fa-trash-can text-[10px]"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Teks & Gambar Komentar Normal -->
            <div id="comment-view-{{ $comment->id }}">
                @if ($comment->comment)
                    <p class="text-gray-300 leading-relaxed">{{ $comment->comment }}</p>
                @endif
                @if ($comment->image)
                    <img src="{{ asset('storage/' . $comment->image) }}" class="mt-2 rounded-md max-h-32 border border-gray-800 object-cover">
                @endif
            </div>

            <!-- Form Edit Komentar Inline -->
            @if (Auth::id() === $comment->user_id)
                <form id="comment-edit-form-{{ $comment->id }}" action="{{ route('comments.update', $comment->id) }}" method="POST" enctype="multipart/form-data" class="hidden mt-2 space-y-2">
                    @csrf @method('PATCH')
                    <textarea name="comment" rows="2" class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-lg p-2 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $comment->comment }}</textarea>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded-md font-semibold text-[11px]">Simpan</button>
                        <button type="button" onclick="toggleEditComment({{ $comment->id }})" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-3 py-1 rounded-md font-semibold text-[11px]">Batal</button>
                    </div>
                </form>
            @endif

            <!-- Tombol Balas (Reply) -->
            <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-[11px] text-blue-400 hover:underline mt-1 font-medium">Balas</button>
        </div>
    </div>

    <!-- Form Balas Komentar (Tersembunyi) dengan Anchor ke Komentar Terkait -->
    <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.reply', $comment->id) }}" method="POST" class="hidden pl-8 mt-2 space-y-2">
        @csrf
        <div class="flex items-center gap-2">
            <input type="text" name="comment" placeholder="Tulis balasan untuk {{ $comment->user->name }}..." required
                class="flex-1 bg-gray-900 border border-gray-800 text-gray-100 rounded-full px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-full text-xs font-semibold">Kirim</button>
        </div>
    </form>

    <!-- Menampilkan Daftar Balasan Turunan (Replies) Secara Rekursif -->
    @if ($comment->replies && $comment->replies->count() > 0)
        <div class="pl-6 space-y-2 border-l border-gray-800 mt-2">
            @foreach ($comment->replies as $reply)
                @include('posts.partials.comment-item', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>