<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryUserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//category part
Route::get('/category', [CategoryUserManagementController::class, 'getAllCategories']);

//Auth Part
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

//event part
Route::get('/event', [EventController::class, 'getAllEvent']);
Route::get('/event/{eventId}', [EventController::class, 'getEventById']);

Route::get('/event', [EventController::class, 'getAllEvent']);
Route::resource('/payment_method', PaymentMethodController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/booking', BookingController::class);
    Route::resource('/ticket', TicketController::class);
    Route::resource('/payment', PaymentController::class);
    Route::get('refund', [RefundController::class, 'indexByUser']);
});

Route::get('/user', [ProfileController::class, 'index'])->middleware('auth:sanctum');
//Admin role
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get("/booking", [BookingController::class, 'adminIndex']);
    Route::post('/event', [EventController::class, 'createEventByAdmin']);
    Route::put('/event/{eventId}', [EventController::class, 'updateEventByAdmin']);
    Route::delete('/event/{eventId}', [EventController::class, 'deleteEventByAdmin']);

    Route::post('/category', [CategoryUserManagementController::class, 'addCategory']);
    Route::get('/user', [UserManagementController::class, 'getAllUser']);
    Route::delete('/user/{id}', [UserManagementController::class, 'deleteUserById']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('/refund', RefundController::class);
    Route::post('/reports', [ReportController::class, 'generate']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports_list', [ReportController::class, 'ListIndex']);
});





