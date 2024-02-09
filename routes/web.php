<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [TicketController::class, 'index']);

Route::get('/purchase/{ticket:ulid}', [OrderController::class, 'create'])->name('purchase');

Route::post('/purchase/{ticket:ulid}', [OrderController::class, 'store'])->name('order.store');

Route::get('/orders/{order:code}', [OrderController::class, 'show'])->name('orders.show')->middleware('signed');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
