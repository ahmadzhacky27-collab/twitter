<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - ConnectSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen pb-12">

    <!-- Navigasi Sederhana -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex justify-between items-center">
        <a href="{{ route('feed.index') }}" class="text-blue-400 font-bold flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>
        <span class="text-sm font-semibold text-gray-300">Pengaturan Akun</span>
    </nav>

    <div class="max-w-2xl mx-auto mt-8 px-4 space-y-6">

        <!-- Notifikasi Berhasil -->
        @if (session('status') === 'profile-updated' || session('success'))
            <div
                class="bg-emerald-950/60 border border-emerald-800 text-emerald-300 px-4 py-3 rounded-lg text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                {{ session('success') ?? 'Profil berhasil diperbarui.' }}
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div
                class="bg-emerald-950/60 border border-emerald-800 text-emerald-300 px-4 py-3 rounded-lg text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i> Password berhasil diubah.
            </div>
        @endif

        <!-- Error Validasi Umum -->
        @if ($errors->any())
            <div class="bg-red-950/50 border border-red-800 text-red-300 px-4 py-3 rounded-lg text-xs">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form 1: Informasi Profil, Bio, Lokasi & Avatar -->
        <div class="bg-gray-900 rounded-2xl shadow-xl border border-gray-800 p-6">
            <h2 class="text-lg font-bold text-gray-100 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-400"></i> Informasi Profil
            </h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Avatar Preview & Input -->
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-800 border border-gray-700 overflow-hidden flex items-center justify-center">
                        @if (auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user text-gray-500 text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">Foto Profil (Avatar)</label>
                        <input type="file" name="avatar"
                            class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Input Bio Singkat -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Bio Singkat</label>
                    <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..."
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('bio', auth()->user()->bio) }}</textarea>
                </div>

                <!-- Input Lokasi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', auth()->user()->location) }}"
                        placeholder="Contoh: Jakarta Selatan"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold text-xs transition">
                    Simpan Perubahan Profil
                </button>
            </form>
        </div>

        <!-- Form 2: Ubah Password -->
        <div class="bg-gray-900 rounded-2xl shadow-xl border border-gray-800 p-6">
            <h2 class="text-lg font-bold text-gray-100 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-lock text-blue-400"></i> Perbarui Password
            </h2>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Password Baru</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold text-xs transition">
                    Ubah Password
                </button>
            </form>
        </div>

    </div>

</body>

</html>
