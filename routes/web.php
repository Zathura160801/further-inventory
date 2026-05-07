<?php

use App\Http\Controllers\BoxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [BoxController::class, 'home'])->name('home');

Route::post('/locale', function (Request $request) {
    $validated = $request->validate([
        'locale' => ['required', 'in:en,zh_TW'],
    ]);

    $request->session()->put('locale', $validated['locale']);

    return back();
})->name('locale.update');

Route::get('/scan', [BoxController::class, 'scan'])->name('boxes.scan');
Route::get('/qr/{qrUuid}', [BoxController::class, 'showByQr'])->name('boxes.qr.show');
Route::post('/boxes/{box}/images', [BoxController::class, 'storeImagesForBox'])->name('boxes.images.store');
Route::patch('/boxes/{box}/notes', [BoxController::class, 'updateNotes'])->name('boxes.notes.update');
Route::delete('/box-images/{image}', [BoxController::class, 'destroyImage'])->name('boxes.images.destroy');
Route::resource('boxes', BoxController::class);
