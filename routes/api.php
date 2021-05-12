<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\TestController;

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


// Public  Access
Route::group(['prefix' => 'v1', 'middleware' => ['api']], function() {
    Route::post('auth/users', [UserController::class, 'login']);
    Route::post('users/register', [UserController::class, 'create']);
});

Route::get('test/info', [TestController::class, 'getInfo']);

// Authentication Access
Route::middleware(['jwt.verify:api'])->group(function() {
    Route::prefix('v1')->group(function() {
        Route::get('test/info', [TestController::class, 'getInfo']);
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
