<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Intervention\Image\Facades\Image;

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

Route::prefix('/dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [Dashboard\DashboardController::class, 'index'])
        ->name('index');

    Route::get('/tickets/create', [Dashboard\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [Dashboard\TicketController::class, 'store'])->name('tickets.store');

    Route::get('/tickets/edit/{ticket:ulid}', [Dashboard\TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/edit/{ticket:ulid}', [Dashboard\TicketController::class, 'update'])->name('tickets.update');

    Route::prefix('/published-tickets')->name('published_tickets.')->group(function () {
        Route::post('/', [Dashboard\PublishedTicketController::class, 'store'])->name('store');
        Route::get('/{ticket:ulid}/orders', [Dashboard\PublishedTicketOrdersController::class, 'index'])->name('orders');

        Route::get('/{ticket:ulid}/attendee-message', [Dashboard\AttendeeMessageController::class, 'create'])->name('attendee_message.create');
        Route::post('/{ticket:ulid}/attendee-message', [Dashboard\AttendeeMessageController::class, 'store'])->name('attendee_message.store');
    });
});

require __DIR__ . '/auth.php';
