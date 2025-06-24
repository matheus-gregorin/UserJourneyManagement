<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Add middleware to the route group
Route::get('/login', function (Request $request){
    return view('index');
});
Route::get('/dash', function (Request $request) {
    return view('dash');
});
//Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
//Route::get('/linkedin/callback', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
