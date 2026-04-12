<?php

use App\Http\Controllers\API\BarlistController;
use App\Http\Controllers\API\BillingsController;
use App\Http\Controllers\API\MembersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\POWASController;
use App\Http\Controllers\API\ReadingsController;
use App\Http\Controllers\API\RegisterController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('powas', POWASController::class);
    Route::resource('members', MembersController::class);
    Route::resource('readings', ReadingsController::class);
    Route::resource('billings', BillingsController::class);
});

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::get('/bar-list', [BarlistController::class, 'index']);
