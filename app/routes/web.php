<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Public site
Route::get('/', [MenuController::class, 'home'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/meals/{meal:slug}', [MenuController::class, 'show'])->name('meals.show');
Route::view('/how-it-works', 'how-it-works')->name('how-it-works');

// Plan builder (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/plan/create', [PlanController::class, 'create'])->name('plan.create');
    Route::post('/plan/quote', [PlanController::class, 'quote'])->name('plan.quote');
    Route::post('/plan', [PlanController::class, 'store'])->name('plan.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/schedule/{schedule}/skip', [ScheduleController::class, 'skip'])->name('schedule.skip');
    Route::post('/schedule/{schedule}/unskip', [ScheduleController::class, 'unskip'])->name('schedule.unskip');
    Route::post('/schedule/skip-day', [ScheduleController::class, 'skipDay'])->name('schedule.skip-day');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::delete('/profile/address/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');
});

require __DIR__.'/auth.php';
