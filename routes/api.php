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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get(
    '/posts/listado',
    [\App\Http\Controllers\PostsController::class, 'list']
);
Route::get(
    '/posts/firmar/{id}',
    [\App\Http\Controllers\PostsController::class, 'firmar']
);
Route::put(
    '/posts/estado/{id}',
    [\App\Http\Controllers\PostsController::class, 'cambiarEstado']
);
Route::get(
    '/misposts/',
    [\App\Http\Controllers\PostsController::class, 'listMine']
);
Route::get('/users/firmas', [
    \App\Http\Controllers\UsersController::class,
    'postsFirmadas'
]);
Route::resource(
    'posts',
    \App\Http\Controllers\PostsController::class
);