<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::get('dashboard', fn () => Inertia::render('Dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/messages', [ChatController::class, 'fetch'])->name('chat.fetch');
Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.store');

Route::middleware('auth')->group(function (): void {
    Route::patch('/user/name', function () {
        request()->validate(['name' => 'required|string|max:255']);
        auth()->user()->update(['name' => request('name')]);
        return response()->json(['success' => true]);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
