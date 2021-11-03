<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\PostController;
use App\Http\Controllers\API\V1\TestController;
use App\Http\Controllers\API\V1\SkillController;

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
    
    Route::prefix('users')->group(function() {
        Route::post('register', [UserController::class, 'create']);
        Route::post('forgot-password', [UserController::class, 'forgotPassword']);
    });
    
    Route::get('posts', [PostController::class, 'list']);
    Route::prefix('posts')->group(function() {
        Route::post('{id}/like', [PostController::class, 'likePost']);
        Route::get('{id}/like/count', [PostController::class, 'getCountPostLike']);
        Route::get('{id}/comments', [PostController::class, 'getPostComment']);
        Route::get('{id}/comments/count', [PostController::class, 'countPostComment']);
    });

    Route::prefix('skills')->group(function() {
        Route::get('test', [SkillController::class, 'list']);
    });
});

Route::get('test/info', [TestController::class, 'getInfo']);
Route::post('test/elastic', [TestController::class, 'testElasticMail']);
Route::post('test/subscribe', [TestController::class, 'testNotifyMe']);
Route::post('test/mail', [TestController::class, 'testNotifyMe']);
// Route::middleware(['cors'])->group(function () {
//     Route::post('test/subscribe', [TestController::class, 'testNotifyMe']);
// });


// Authentication Access
Route::middleware(['jwt.verify:api'])->group(function() {
    Route::prefix('v1')->group(function() {

        // For getting user profile
        Route::prefix('users')->group(function() {
            Route::get('profile/{id}', [UserController::class, 'getUserProfile']);
            Route::prefix('coins')->group(function() {
                Route::get('balance/{id}', [UserController::class, 'getCoinsBalance']);
            });
            Route::prefix('{id}')->group(function() {
                Route::get('interest', [UserController::class, 'getUserInterest']);
                
                Route::prefix('posts')->group(function() {
                    Route::get('saved', [UserController::class, 'getUserPostSave']);
                });

                Route::prefix('update')->group(function() {
                    Route::put('profile', [UserController::class, 'updateProfile']);
                    Route::put('receive-notification', [UserController::class, 'recieveNotification']);
                    Route::put('description', [UserController::class, 'updateDescription']);
                    Route::put('profession', [UserController::class, 'updateProfession']);
                });

                Route::prefix('skills')->group(function() {
                    Route::get('/', [SkillController::class, 'getUserSkills']);
                    Route::put('update', [SkillController::class, 'updateUserSkills']);

                    Route::prefix('{skill_id}')->group(function() {
                        Route::delete('delete', [SkillController::class, 'deleteUserSkills']);
                    });
                });

            });

            Route::get('followers/{id}/count', [UserController::class, 'getUserCountFollower']);
            
        });


        Route::prefix('skills')->group(function() {
            Route::get('/', [SkillController::class, 'list']);
        });

    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
