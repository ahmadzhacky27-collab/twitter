<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - ConnectSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center py-10">

    <div class="max-w-md w-full bg-gray-900 rounded-2xl shadow-xl border border-gray-800 p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-400 flex items-center justify-center gap-2">
                <i class="fa-brands fa-twitter"></i> ConnectSpace
            </h1>
            <p class="text-sm text-gray-400 mt-1">Buat akun baru (Auto-Login aktif setelah daftar)</p>
        </div>

        <!-- Menampilkan Error Validasi -->
        @if ($errors->any())
            <div class="mb-4 bg-red-950/50 border border-red-800 text-red-300 px-4 py-3 rounded-lg text-xs">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Password (Min. 6 Karakter)</label>
                <input type="password" name="password" required
                       class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 rounded-lg font-semibold text-sm shadow-sm transition">
                Daftar & Masuk Otomatis
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-400 font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>

</body>
</html>