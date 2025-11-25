<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaffinderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cafes', [CaffinderController::class, 'index'])->name('cafes.index');

Route::get('/search', [CaffinderController::class, 'index'])->name('search');
// Route::get('/', [CaffinderController::class, 'index'])->name('cafes.index');
// Route::get('/search', [CaffinderController::class, 'index'])->name('cafes.search');
Route::get('/cafes/{id}', [CaffinderController::class, 'show'])->name('cafes.show');
// Route::get('/nlp-search', [CaffinderController::class, 'nlpSearch'])->name('cafes.NLPsearch');