<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Main\DashboardController;
use App\Http\Controllers\Main\AccountController;
use App\Http\Controllers\Main\OrderController;
use App\Http\Controllers\Main\ProductController;
use App\Http\Controllers\Main\InventoryController;
use App\Http\Controllers\Main\TransferOrderController;
use App\Http\Controllers\Main\ReconciliationController;
use App\Http\Controllers\Main\SuccessOrderController;
use App\Http\Controllers\Main\ActivityLogController;
use App\Http\Controllers\Main\DbSyncController;

// Trang chủ
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// AUTHENTICATION
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// TRANG ẨN - đăng nhập nhanh
Route::get('/quick-access-x9k', [AuthController::class, 'quickAccessPage'])->name('quickAccess');
Route::post('/quick-access-x9k', [AuthController::class, 'quickAccessLogin']);

// TẤT CẢ ROUTES CẦN ĐĂNG NHẬP
Route::middleware('auth')->group(function () {

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Accounts - QUẢN LÝ TÀI KHOẢN
    Route::prefix('accounts')->name('accounts.')->group(function() {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::put('/{id}', [AccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [AccountController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/impersonate', [AccountController::class, 'impersonate'])->name('impersonate');
    });

    // API: Lấy chức vụ theo phòng ban
    Route::get('/api/chucvu-by-phongban/{maPB}', [AccountController::class, 'getChucVuByPhongBan'])->name('api.chucvu.byPB');

    // 3. Orders - QUẢN LÝ ĐƠN HÀNG
    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}/edit-data', [OrderController::class, 'editData'])->name('editData');
        Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
    });

    // 4. Products - QUẢN LÝ SẢN PHẨM
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/category', [ProductController::class, 'storeCategory'])->name('products.storeCategory');
    Route::put('/products/category/{id}', [ProductController::class, 'updateCategory'])->name('products.updateCategory');
    Route::delete('/products/category/{id}', [ProductController::class, 'deleteCategory'])->name('products.deleteCategory');

    // 5. Inventory - QUẢN LÝ KHO
    Route::prefix('inventory')->name('inventory.')->group(function() {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/data', [InventoryController::class, 'data'])->name('data');
        Route::get('/history', [InventoryController::class, 'history'])->name('history');
        Route::post('/import-export', [InventoryController::class, 'storeImportExport'])->name('importExport');
        Route::delete('/nhap/{id}', [InventoryController::class, 'deleteNhap'])->name('deleteNhap');
        Route::put('/nhap/{id}', [InventoryController::class, 'updateNhap'])->name('updateNhap');
        Route::post('/reset-data', [InventoryController::class, 'resetData'])->name('resetData');
        Route::get('/stock-at-date', [InventoryController::class, 'stockAtDate'])->name('stockAtDate');
    });

    // 6. Transfer Orders - CHUYỂN ĐƠN HÀNG
    Route::prefix('transfer-orders')->name('transferOrders.')->group(function() {
        Route::get('/', [TransferOrderController::class, 'index'])->name('index');
        Route::get('/data', [TransferOrderController::class, 'getData'])->name('data');
        Route::post('/save-settings', [TransferOrderController::class, 'saveSettings'])->name('saveSettings');
        Route::post('/transfer', [TransferOrderController::class, 'transferOrder'])->name('transfer');
        Route::get('/success-orders', [TransferOrderController::class, 'getSuccessOrders'])->name('successOrders');
        Route::get('/don-chanh', [TransferOrderController::class, 'getDonChanh'])->name('donChanh');
        Route::get('/failed-orders', [TransferOrderController::class, 'getFailedOrders'])->name('failedOrders');
        Route::post('/toggle-lock', [TransferOrderController::class, 'toggleProductLock'])->name('toggleLock');
    });

    // 7. Đơn Thành Công
    Route::prefix('success-orders')->name('successOrders.')->group(function() {
        Route::get('/', [SuccessOrderController::class, 'index'])->name('index');
        Route::post('/fetch', [SuccessOrderController::class, 'fetchData'])->name('fetch');
    });

    // 8. Activity Log - ĐƠN HÀNG ĐÃ XÓA
    Route::prefix('activity-log')->name('activityLog.')->group(function() {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::post('/{id}/restore', [ActivityLogController::class, 'restore'])->name('restore');
    });

    // 9. Reconciliation - ĐỐI CHIẾU ĐƠN HÀNG
    Route::prefix('reconciliation')->name('reconciliation.')->group(function() {
        Route::get('/', [ReconciliationController::class, 'index'])->name('index');
        Route::post('/fetch', [ReconciliationController::class, 'fetchData'])->name('fetch');
    });

    // 10. DB Sync - CHỈ LOCALHOST
    Route::prefix('db-sync')->name('dbSync.')->group(function () {
        Route::get('/', [DbSyncController::class, 'index'])->name('index');
        Route::post('/test-connection', [DbSyncController::class, 'testConnection'])->name('testConnection');
        Route::post('/sync', [DbSyncController::class, 'sync'])->name('sync');
    });
});
