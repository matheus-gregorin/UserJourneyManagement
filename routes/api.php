<?php

use App\Http\Controllers\UsersControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Login
Route::post('/login', [UsersControllers::class, 'login']);

// Core
Route::group(['middleware' => 'jwt'], function () {
    Route::post('/create-user', [UsersControllers::class, 'createUser']);
    Route::get('/all-users', [UsersControllers::class, 'getAllUsers']);
    Route::put('/change-role-user/{uuid}', [UsersControllers::class, 'changeRoleUser']);
});

// Webhook
Route::post('/whatsapp-receive', [UsersControllers::class, 'webhookReceiveMessage']);

Route::post('/teste', function (Request $request) {
    Log::info(" Requisição de teste", [
        "request" => $request->all(),
        "date" => now()
    ]);

    return response()->json([
        "message" => "Success",
        "date" => now()
    ]);
});