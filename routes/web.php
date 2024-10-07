<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CensusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('upload');
});
// Rotas do desafio.
Route::get('/upload', function () {
    return view('upload');
})->name('upload.page');
Route::post('/upload', [CensusController::class, 'upload'])->name('upload.census');
Route::get('/review', [CensusController::class, 'review'])->name('review.page');
Route::post('/save', [CensusController::class, 'save'])->name('save.census');
Route::get('/patients', [CensusController::class, 'listPatients'])->name('patients.page');
