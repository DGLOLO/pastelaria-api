<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailPreviewController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas para preview do email
Route::get('/email-preview', [EmailPreviewController::class, 'index'])->name('email.preview.index');
Route::get('/email-preview/preview', [EmailPreviewController::class, 'preview'])->name('email.preview');
Route::get('/email-preview/order/{order_id}', [EmailPreviewController::class, 'previewOrder'])->name('email.preview.order');
