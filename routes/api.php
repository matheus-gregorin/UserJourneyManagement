<?php

use App\Http\Controllers\UsersControllers;
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

Route::post('/login', [UsersControllers::class, 'login']);

Route::post('/create-user', [UsersControllers::class, 'createUser']);
Route::get('/all-users', [UsersControllers::class, 'getAllUsers']);