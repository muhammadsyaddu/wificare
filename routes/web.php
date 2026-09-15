<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Teknisi;
use Illuminate\Support\Facades\Route;

// ============================================
// Public / Guest
// ============================================
Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ============================================
// Admin Routes
// ============================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Pelanggan
        Route::resource('customers', Admin\CustomerController::class);

        // Teknisi
        Route::get('/technicians', [Admin\TechnicianController::class, 'index'])->name('technicians.index');
        Route::get('/technicians/{id}', [Admin\TechnicianController::class, 'show'])->name('technicians.show');
        Route::put('/technicians/{id}/toggle-availability', [Admin\TechnicianController::class, 'toggleAvailability'])->name('technicians.toggle-availability');
        Route::post('/technicians/{id}/verify', [Admin\TechnicianController::class, 'verify'])->name('technicians.verify');

        // Pekerjaan / Order
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [Admin\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [Admin\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{id}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/assign', [Admin\OrderController::class, 'assign'])->name('orders.assign');
        Route::post('/orders/{id}/cancel', [Admin\OrderController::class, 'cancel'])->name('orders.cancel');

        // Gangguan / Issues
        Route::get('/issues', [Admin\IssueController::class, 'index'])->name('issues.index');

        // Laporan
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');

        // API: Addresses for customer (AJAX)
        Route::get('/api/customers/{id}/addresses', function (int $id) {
            $addresses = \App\Models\Address::where('user_id', $id)->get(['id', 'label', 'address_line', 'district', 'city']);
            return response()->json($addresses);
        })->name('api.customer-addresses');
    });

// ============================================
// Teknisi Routes
// ============================================
Route::middleware(['auth', 'role:technician'])
    ->prefix('teknisi')
    ->name('teknisi.')
    ->group(function () {

        Route::get('/dashboard', [Teknisi\DashboardController::class, 'index'])->name('dashboard');

        // Pekerjaan
        Route::get('/orders', [Teknisi\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/history', [Teknisi\OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{id}', [Teknisi\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}/accept', [Teknisi\OrderController::class, 'accept'])->name('orders.accept');
        Route::put('/orders/{id}/status', [Teknisi\OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Laporan Pekerjaan
        Route::get('/orders/{id}/report', [Teknisi\OrderController::class, 'createReport'])->name('orders.report');
        Route::post('/orders/{id}/report', [Teknisi\OrderController::class, 'storeReport'])->name('orders.store-report');
    });
