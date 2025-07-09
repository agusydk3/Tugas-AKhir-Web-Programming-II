<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\QRISPaymentController;

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
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware(['guest'])->group(function () {
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User & Reseller Dashboard Routes
Route::middleware(['auth', 'role:customer,reseller'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/product', [DashboardController::class, 'product'])->name('user.product');
    Route::get('/deposit', [DepositController::class, 'index'])->name('user.deposit');
    Route::post('/deposit/process', [DepositController::class, 'processDeposit'])->name('user.deposit.process');
    Route::post('/deposit/check-status', [DepositController::class, 'checkStatus'])->name('user.deposit.check-status');
    Route::post('/deposit/cancel', [DepositController::class, 'cancelDeposit'])->name('user.deposit.cancel');
    Route::get('/deposit/qris-image', [DepositController::class, 'getQrisImage'])->name('user.deposit.qris-image');
    Route::get('/history', [DashboardController::class, 'history'])->name('user.history');
    Route::get('/contact', [DashboardController::class, 'contact'])->name('user.contact');
    Route::post('/contact/send', [DashboardController::class, 'sendContact'])->name('user.contact.send');
    
    // Checkout flow routes
    Route::get('/order/{product}', [DashboardController::class, 'order'])->name('user.order');
    Route::post('/order/checkout', [DashboardController::class, 'processCheckout'])->name('user.checkout');
    
    // QRIS Payment routes
    Route::get('/order/pending/{transaction}', [QRISPaymentController::class, 'showPayment'])->name('user.order.pending');
    Route::post('/order/check-status', [QRISPaymentController::class, 'checkStatus'])->name('user.order.check-status');
    Route::post('/order/cancel/{transaction}', [QRISPaymentController::class, 'cancelPayment'])->name('user.order.cancel');
    Route::get('/order/qris-image/{transaction}', [QRISPaymentController::class, 'getQrisImage'])->name('user.order.qris-image');
    
    Route::get('/order/success/{transaction}', [DashboardController::class, 'successPayment'])->name('user.order.success');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Product routes
    Route::resource('products', ProductController::class);
    
    // Stock routes
    Route::resource('stocks', StockController::class);
    Route::get('stocks/product/{product}', [StockController::class, 'showByProduct'])->name('stocks.product');
    
    // User routes
    Route::resource('users', UserController::class);
    Route::post('users/{user}/balance', [UserController::class, 'updateBalance'])->name('users.balance');
    
    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.status');
    Route::get('/transactions/stats/summary', [TransactionController::class, 'getStats'])->name('transactions.stats');
});

// Auth routes
require __DIR__.'/auth.php';
