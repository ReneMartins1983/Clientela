<?php

use App\Http\Controllers\AttachmentController;
use App\Livewire\ClientShow;
use App\Livewire\Clients;
use App\Livewire\Pipeline;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    // após o login o Breeze envia para "dashboard"
    Route::get('dashboard', fn () => redirect()->route('clients.index'))->name('dashboard');

    Route::get('clients', Clients::class)->name('clients.index');
    Route::get('funil', Pipeline::class)->name('pipeline');
    Route::get('clients/{client}', ClientShow::class)->name('clients.show');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
