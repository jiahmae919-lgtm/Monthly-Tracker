<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/planner-entries', [DashboardController::class, 'storePlannerEntry'])->name('planner-entries.store');
    Route::patch('/planner-entries/{entry}', [DashboardController::class, 'updatePlannerEntry'])->name('planner-entries.update');
    Route::delete('/planner-entries/{entry}', [DashboardController::class, 'destroyPlannerEntry'])->name('planner-entries.destroy');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    Route::resource('incomes', IncomeController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('debts', DebtController::class);
    Route::resource('payments', PaymentController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
