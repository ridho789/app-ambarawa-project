<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\ACController;
use App\Http\Controllers\BubutController;
use App\Http\Controllers\BBMController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\OperasionalController;
use App\Http\Controllers\PolesKacaMobilController;
use App\Http\Controllers\SembakoController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\TripController;

// Kontruksi
// use App\Http\Controllers\BatuController;
use App\Http\Controllers\BesiController;
use App\Http\Controllers\MaterialController;
// use App\Http\Controllers\PasirController;

// Pembangunan
use App\Http\Controllers\PenguruganController;

// Master data
use App\Http\Controllers\KategoriMaterialController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SatuanController;

// History
use App\Http\Controllers\ActivityController;

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

Route::group(['middleware' => ['auth', 'check.role.user:0']], function () {
    // History
    Route::get('activity', [ActivityController::class, 'index']);
});

Route::group(['middleware' => ['auth', 'check.role.user:0,1']], function () {
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Permintaan barang
    Route::get('/permintaan_barang', [PermintaanBarangController::class, 'index']);
    Route::post('permintaan_barang-store', [PermintaanBarangController::class, 'store']);
    Route::post('permintaan_barang-update', [PermintaanBarangController::class, 'update']);
    Route::post('permintaan_barang-delete', [PermintaanBarangController::class, 'delete']);
    Route::post('permintaan_barang-status_pending', [PermintaanBarangController::class, 'pending']);
    Route::post('permintaan_barang-status_waiting', [PermintaanBarangController::class, 'waiting']);
    Route::post('permintaan_barang-status_approved', [PermintaanBarangController::class, 'approved']);

    // AC
    Route::get('/ac', [ACController::class, 'index']);
    Route::post('ac-store', [ACController::class, 'store']);
    Route::post('ac-update', [ACController::class, 'update']);
    Route::post('ac-delete', [ACController::class, 'delete']);
    Route::post('ac-export', [ACController::class, 'export']);
    Route::post('ac-status_pending', [ACController::class, 'pending']);
    Route::post('ac-status_process', [ACController::class, 'process']);
    Route::post('ac-status_paid', [ACController::class, 'paid']);
    
    // BBM
    Route::get('/bbm', [BBMController::class, 'index']);
    Route::post('bbm-store', [BBMController::class, 'store']);
    Route::post('bbm-update', [BBMController::class, 'update']);
    Route::post('bbm-delete', [BBMController::class, 'delete']);
    Route::post('bbm-export', [BBMController::class, 'export']);
    Route::post('bbm-status_pending', [BBMController::class, 'pending']);
    Route::post('bbm-status_process', [BBMController::class, 'process']);
    Route::post('bbm-status_paid', [BBMController::class, 'paid']);
    
    // Bubut
    Route::get('/bubut', [BubutController::class, 'index']);
    Route::post('bubut-store', [BubutController::class, 'store']);
    Route::post('bubut-update', [BubutController::class, 'update']);
    Route::post('bubut-delete', [BubutController::class, 'delete']);
    Route::post('bubut-export', [BubutController::class, 'export']);
    Route::post('bubut-status_pending', [BubutController::class, 'pending']);
    Route::post('bubut-status_process', [BubutController::class, 'process']);
    Route::post('bubut-status_paid', [BubutController::class, 'paid']);
    
    // Cat
    Route::get('/cat', [CatController::class, 'index']);
    Route::post('cat-store', [CatController::class, 'store']);
    Route::post('cat-update', [CatController::class, 'update']);
    Route::post('cat-delete', [CatController::class, 'delete']);
    Route::post('cat-export', [CatController::class, 'export']);
    Route::post('cat-status_pending', [CatController::class, 'pending']);
    Route::post('cat-status_process', [CatController::class, 'process']);
    Route::post('cat-status_paid', [CatController::class, 'paid']);

    // Pengurugan
    Route::get('/pengurugan', [PenguruganController::class, 'index']);
    Route::post('pengurugan-store', [PenguruganController::class, 'store']);
    Route::post('pengurugan-update', [PenguruganController::class, 'update']);
    Route::post('pengurugan-delete', [PenguruganController::class, 'delete']);
    Route::post('pengurugan-export', [PenguruganController::class, 'export']);
    Route::post('pengurugan-status_pending', [PenguruganController::class, 'pending']);
    Route::post('pengurugan-status_process', [PenguruganController::class, 'process']);
    Route::post('pengurugan-status_paid', [PenguruganController::class, 'paid']);
    
    // Operasional
    Route::get('/operasional', [OperasionalController::class, 'index']);
    Route::post('operasional-store', [OperasionalController::class, 'store']);
    Route::post('operasional-update', [OperasionalController::class, 'update']);
    Route::post('operasional-delete', [OperasionalController::class, 'delete']);
    Route::post('operasional-export', [OperasionalController::class, 'export']);
    Route::post('operasional-status_pending', [OperasionalController::class, 'pending']);
    Route::post('operasional-status_process', [OperasionalController::class, 'process']);
    Route::post('operasional-status_paid', [OperasionalController::class, 'paid']);
    
    // Poles Kaca Mobil
    Route::get('/poles', [PolesKacaMobilController::class, 'index']);
    Route::post('poles-store', [PolesKacaMobilController::class, 'store']);
    Route::post('poles-update', [PolesKacaMobilController::class, 'update']);
    Route::post('poles-delete', [PolesKacaMobilController::class, 'delete']);
    Route::post('poles-export', [PolesKacaMobilController::class, 'export']);
    Route::post('poles-status_pending', [PolesKacaMobilController::class, 'pending']);
    Route::post('poles-status_process', [PolesKacaMobilController::class, 'process']);
    Route::post('poles-status_paid', [PolesKacaMobilController::class, 'paid']);
    
    // Sembako
    Route::get('/sembako', [SembakoController::class, 'index']);
    Route::post('sembako-store', [SembakoController::class, 'store']);
    Route::post('sembako-update', [SembakoController::class, 'update']);
    Route::post('sembako-delete', [SembakoController::class, 'delete']);
    Route::post('sembako-export', [SembakoController::class, 'export']);
    Route::post('sembako-import', [SembakoController::class, 'import']);
    Route::post('sembako-status_pending', [SembakoController::class, 'pending']);
    Route::post('sembako-status_process', [SembakoController::class, 'process']);
    Route::post('sembako-status_paid', [SembakoController::class, 'paid']);
    
    // Sparepart AMB
    Route::get('/sparepartamb', [SparepartController::class, 'index']);
    Route::post('sparepartamb-store', [SparepartController::class, 'store']);
    Route::post('sparepartamb-update', [SparepartController::class, 'update']);
    Route::post('sparepartamb-delete', [SparepartController::class, 'delete']);
    Route::post('sparepartamb-export', [SparepartController::class, 'export']);
    Route::post('sparepartamb-status_pending', [SparepartController::class, 'pending']);
    Route::post('sparepartamb-status_process', [SparepartController::class, 'process']);
    Route::post('sparepartamb-status_paid', [SparepartController::class, 'paid']);
    
    // Trip
    Route::get('/trip', [TripController::class, 'index']);
    Route::post('trip-store', [TripController::class, 'store']);
    Route::post('trip-update', [TripController::class, 'update']);
    Route::post('trip-delete', [TripController::class, 'delete']);
    Route::post('trip-export', [TripController::class, 'export']);
    Route::post('trip-status_pending', [TripController::class, 'pending']);
    Route::post('trip-status_process', [TripController::class, 'process']);
    Route::post('trip-status_paid', [TripController::class, 'paid']);

    // Kontruksi - Batu
    // Route::get('/batu', [BatuController::class, 'index']);
    // Route::post('batu-store', [BatuController::class, 'store']);
    // Route::post('batu-update', [BatuController::class, 'update']);
    // Route::post('batu-delete', [BatuController::class, 'delete']);
    // Route::post('batu-export', [BatuController::class, 'export']);

    // Kontruksi - Besi
    Route::get('/besi', [BesiController::class, 'index']);
    Route::post('besi-store', [BesiController::class, 'store']);
    Route::post('besi-update', [BesiController::class, 'update']);
    Route::post('besi-delete', [BesiController::class, 'delete']);
    Route::post('besi-export', [BesiController::class, 'export']);
    Route::post('besi-status_pending', [BesiController::class, 'pending']);
    Route::post('besi-status_process', [BesiController::class, 'process']);
    Route::post('besi-status_paid', [BesiController::class, 'paid']);

    // Kontruksi - Material
    Route::get('/material', [MaterialController::class, 'index']);
    Route::post('material-store', [MaterialController::class, 'store']);
    Route::post('material-update', [MaterialController::class, 'update']);
    Route::post('material-delete', [MaterialController::class, 'delete']);
    Route::post('material-export', [MaterialController::class, 'export']);
    Route::post('material-status_pending', [MaterialController::class, 'pending']);
    Route::post('material-status_process', [MaterialController::class, 'process']);
    Route::post('material-status_paid', [MaterialController::class, 'paid']);

    // Kontruksi - Pasir
    // Route::get('/pasir', [PasirController::class, 'index']);
    // Route::post('pasir-store', [PasirController::class, 'store']);
    // Route::post('pasir-update', [PasirController::class, 'update']);
    // Route::post('pasir-delete', [PasirController::class, 'delete']);
    // Route::post('pasir-export', [PasirController::class, 'export']);

    // Master data - Kategori Material
    Route::get('/kategori_material', [KategoriMaterialController::class, 'index']);
    Route::post('kategori_material-store', [KategoriMaterialController::class, 'store']);
    Route::post('kategori_material-update', [KategoriMaterialController::class, 'update']);
    Route::post('kategori_material-delete', [KategoriMaterialController::class, 'delete']);

    // Master data - Kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::post('kendaraan-store', [KendaraanController::class, 'store']);
    Route::post('kendaraan-update', [KendaraanController::class, 'update']);
    Route::post('kendaraan-delete', [KendaraanController::class, 'delete']);

    // Master data - Proyek
    Route::get('/proyek', [ProyekController::class, 'index']);
    Route::post('proyek-store', [ProyekController::class, 'store']);
    Route::post('proyek-update', [ProyekController::class, 'update']);
    Route::post('proyek-delete', [ProyekController::class, 'delete']);

    // Master data - Satuan
    Route::get('/satuan', [SatuanController::class, 'index']);
    Route::post('satuan-store', [SatuanController::class, 'store']);
    Route::post('satuan-update', [SatuanController::class, 'update']);
    Route::post('satuan-delete', [SatuanController::class, 'delete']);
});