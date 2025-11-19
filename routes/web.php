<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;

Route::get('/summary', [dashboardController::class, 'summary'])->name('summary');
Route::get('/table', [dashboardController::class, 'table'])->name('table');
Route::get('/', [dashboardController::class, 'index'])->name('index');
Route::get('/exportXlsx', [dashboardController::class, 'exportXlsx'])->name('exportXlsx');


