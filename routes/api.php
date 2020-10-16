<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register','UsersController@register');
Route::post('/login','UsersController@login');

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/prueba','UsersController@index');
});

Route::get('logout', 'UsersController@logout')->middleware('auth:api');
Route::get('user', 'UsersController@user')->middleware('auth:api');
