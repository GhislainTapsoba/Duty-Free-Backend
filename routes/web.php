<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// ✅ Ajoute ceci pour forcer la présence de la route CSRF de Sanctum
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['csrf_token_set' => true]);
});
