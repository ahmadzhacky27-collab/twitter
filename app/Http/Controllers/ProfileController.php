<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Memperbarui informasi profil dan avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validasi input profil
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // Maksimal 2MB
        ]);

        // Ambil model User yang sedang login secara eksplisit
        $user = User::find(Auth::id());

        // 2. Tangani upload file avatar jika ada
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada dan bukan URL eksternal
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan avatar baru ke folder storage/app/public/avatars
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 3. Update data user
        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Memperbarui password dengan verifikasi password lama.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Ambil model User yang sedang login secara eksplisit
        $user = \App\Models\User::find(Auth::id());

        // Update password baru yang sudah di-hash
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}