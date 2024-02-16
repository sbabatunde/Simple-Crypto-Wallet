<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register_user', [UserController::class, 'createNewUser'])->name('user.register');
Route::post('/login', [UserController::class, 'login'])->name('user.login');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('user.logout');
Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/wallet-dashboard', [WalletController::class, 'walletDashboard'])->name('wallet.dashboard');
    // Route::get('/dashboard', [UserController::class, 'dashboard'])->name('wallet.dashboard');
    Route::post('/createWallet', [WalletController::class, 'createUserWallet'])->name('wallet.create');
    Route::post('/deposit', [WalletController::class, 'fundWallet'])->name('wallet.fund');
    Route::post('/transfer', [WalletController::class, 'transferFund'])->name('wallet.transfer');


    Route::get('/user/transaction/history', [TransactionController::class, 'transactionHistory'])->name('transaction.history');
});
