<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ACController;
use App\Http\Controllers\BubutController;
use App\Http\Controllers\BBMController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\OperasionalController;
use App\Http\Controllers\PolesKacaMobilController;
use App\Http\Controllers\SembakoController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\TripController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login-auth', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout']);

Route::group(['middleware' => ['auth', 'check.role.user:0,1']], function () {
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // AC
    Route::get('/ac', [ACController::class, 'index']);
    Route::post('ac-store', [ACController::class, 'store']);
    Route::post('ac-update', [ACController::class, 'update']);
    Route::post('ac-delete', [ACController::class, 'delete']);
    
    // BBM
    Route::get('/bbm', [BBMController::class, 'index']);
    Route::post('bbm-store', [BBMController::class, 'store']);
    Route::post('bbm-update', [BBMController::class, 'update']);
    Route::post('bbm-delete', [BBMController::class, 'delete']);
    
    // Bubut
    Route::get('/bubut', [BubutController::class, 'index']);
    Route::post('bubut-store', [BubutController::class, 'store']);
    Route::post('bubut-update', [BubutController::class, 'update']);
    Route::post('bubut-delete', [BubutController::class, 'delete']);
    
    // Cat
    Route::get('/cat', [CatController::class, 'index']);
    Route::post('cat-store', [CatController::class, 'store']);
    Route::post('cat-update', [CatController::class, 'update']);
    Route::post('cat-delete', [CatController::class, 'delete']);
    
    // Operasional
    Route::get('/operasional', [OperasionalController::class, 'index']);
    Route::post('operasional-store', [OperasionalController::class, 'store']);
    Route::post('operasional-update', [OperasionalController::class, 'update']);
    Route::post('operasional-delete', [OperasionalController::class, 'delete']);
    
    // Poles Kaca Mobil
    Route::get('/poles', [PolesKacaMobilController::class, 'index']);
    Route::post('poles-store', [PolesKacaMobilController::class, 'store']);
    Route::post('poles-update', [PolesKacaMobilController::class, 'update']);
    Route::post('poles-delete', [PolesKacaMobilController::class, 'delete']);
    
    // Sembako
    Route::get('/sembako', [SembakoController::class, 'index']);
    Route::post('sembako-store', [SembakoController::class, 'store']);
    Route::post('sembako-update', [SembakoController::class, 'update']);
    Route::post('sembako-delete', [SembakoController::class, 'delete']);
    
    // Sparepart AMB
    Route::get('/sparepartamb', [SparepartController::class, 'index']);
    Route::post('sparepartamb-store', [SparepartController::class, 'store']);
    Route::post('sparepartamb-update', [SparepartController::class, 'update']);
    Route::post('sparepartamb-delete', [SparepartController::class, 'delete']);
    
    // Trip
    Route::get('/trip', [TripController::class, 'index']);
    Route::post('trip-store', [TripController::class, 'store']);
    Route::post('trip-update', [TripController::class, 'update']);
    Route::post('trip-delete', [TripController::class, 'delete']);
});