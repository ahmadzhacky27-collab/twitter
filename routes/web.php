<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedController;

// Rute utama: Otomatis ke feed jika sudah login, atau ke login jika belum
Route::get('/', function () {
    return Auth::check() ? redirect()->route('feed.index') : redirect()->route('login');
});

// Rute untuk tamu (yang belum login) - Mencegah user yang sudah login mengakses halaman login/register
Route::middleware('guest')->group(function () {
    // Registrasi
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Rute yang wajib terproteksi login (Middleware Auth)
Route::middleware('auth')->group(function () {
    // Feed & Postingan
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/posts', [FeedController::class, 'storePost'])->name('posts.store');
    Route::patch('/posts/{post}', [FeedController::class, 'updatePost'])->name('posts.update');
    Route::delete('/posts/{post}', [FeedController::class, 'destroyPost'])->name('posts.destroy');

    // Interaksi Postingan (Like & Komentar Utama)
    Route::post('/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');

    // Interaksi Komentar (Like, Reply, Update, Delete)
    Route::post('/comments/{comment}/like', [FeedController::class, 'toggleCommentLike'])->name('comments.like');
    Route::post('/comments/{comment}/reply', [FeedController::class, 'storeCommentReply'])->name('comments.reply');
    Route::patch('/comments/{comment}', [FeedController::class, 'updateComment'])->name('comments.update');
    Route::delete('/comments/{comment}', [FeedController::class, 'destroyComment'])->name('comments.destroy');

    // Pengaturan Profil & Keamanan Akun
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
