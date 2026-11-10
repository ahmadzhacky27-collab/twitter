<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan form registrasi (opsional jika menggunakan view terpisah).
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Menyimpan data registrasi dan langsung melakukan Auto-Login.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input sesuai ketentuan UJK
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:150', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // 2. Simpan user ke database dengan password yang di-hash
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Aturan Alur Khusus: Langsung buat sesi aktif (Auto-Login)[cite: 3]
        Auth::login($user);

        // 4. Redirect langsung ke halaman Beranda / Feed[cite: 3]
        return redirect()->route('feed.index');
    }
}