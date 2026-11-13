<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil publik beserta postingan milik user tersebut.
     */
    public function show(User $user)
    {
        $posts = $user->posts()
            ->with(['user', 'likes', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes'])
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'posts'));
    }

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
     * Memperbarui informasi profil, bio, lokasi, dan avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        // Ambil model User yang sedang login secara eksplisit
        $user = User::find(Auth::id());

        // 1. Validasi input profil (termasuk validasi email unik dan location)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:100'], // Validasi input lokasi
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // Maksimal 2MB
        ]);

        // 2. Tangani upload file avatar jika ada
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada di storage
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan avatar baru ke folder storage/app/public/avatars
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 3. Update data user ke database (termasuk name, email, bio, location, dan avatar jika diubah)
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
        $user = User::find(Auth::id());

        // Update password baru yang sudah di-hash
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
