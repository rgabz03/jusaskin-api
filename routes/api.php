<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\API\V1\TestController;
=======
>>>>>>> a36d0bc15f997fcb4de9781c3ad27683772fa045

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

<<<<<<< HEAD

// Public  Access
Route::group(['prefix' => 'v1', 'middleware' => ['no_throttle']], function() {

});

Route::get('test/info', [TestController::class, 'getInfo']);

// Authentication Access
Route::middleware(['jwt.verify:api'])->group(function() {
    Route::prefix('v1')->group(function() {
        Route::get('test/info', [TestController::class, 'getInfo']);
    });
});

=======
>>>>>>> a36d0bc15f997fcb4de9781c3ad27683772fa045
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
