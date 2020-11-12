<?php

use App\Application\Http\V1\Controllers\MailController;
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

Route::name('v1.')->prefix('v1')->group(function() {
    Route::get('/doc', function() { return view('doc'); });
    Route::post('/mail', [MailController::class, 'send'])->name('mail');
});
