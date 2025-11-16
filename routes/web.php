<?php

use App\Http\Controllers\WorkedHourController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WorkedHourController::class, 'index'])->name('worked-hours.index');
Route::get('/worked-hours/create', [WorkedHourController::class, 'create'])->name('worked-hours.create');
Route::post('/worked-hours', [WorkedHourController::class, 'store'])->name('worked-hours.store');
Route::get('/worked-hours/{id}/edit', [WorkedHourController::class, 'edit'])->name('worked-hours.edit');
Route::put('/worked-hours/{id}', [WorkedHourController::class, 'update'])->name('worked-hours.update');
Route::delete('/worked-hours/{id}', [WorkedHourController::class, 'destroy'])->name('worked-hours.destroy');
Route::get('/worked-hours/export', [WorkedHourController::class, 'export'])->name('worked-hours.export');
