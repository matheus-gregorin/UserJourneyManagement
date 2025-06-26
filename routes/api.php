<?php

use App\Http\Controllers\CompaniesControllers;
use App\Http\Controllers\UsersControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;;

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

// Core | User |
Route::group(['middleware' => 'jwt'], function () {
    Route::get('/all-users', [UsersControllers::class, 'getAllUsers']);
    Route::put('/change-role-user/{uuid}', [UsersControllers::class, 'changeRoleUser']);
    Route::post('/create-user', [UsersControllers::class, 'createUser']);
});

// Core | Company |
Route::group(['middleware' => 'jwt'], function () {
    Route::get('/all-companies', [CompaniesControllers::class, 'getAllCompanies']);
    Route::post('/create-company', [CompaniesControllers::class, 'createCompany']);
});

// Webhook
Route::post('/whatsapp-receive', [UsersControllers::class, 'webhookReceiveMessage']);

// Automations
Route::group(['middleware' => 'automations'], function () {
    Route::post('/validate-users-off', [UsersControllers::class, 'validateUsersOff']);
});
