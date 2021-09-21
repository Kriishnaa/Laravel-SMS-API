<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClientServiceController;
use App\Http\Controllers\SmsOperationController;



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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//For CRUD
Route::apiResource('clients', ClientController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('client-services', ClientServiceController::class);

//For SMS Sending
Route::post('/sms-sending',[SmsOperationController::class,'sms_sending']);

//Example : Create API Resource
/*
Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('clients/{user}', [ClientController::class, 'show'])->name('clients.show');
Route::match(['put', 'patch'], 'clients/{user}', [ClientController::class, 'update'])->name('clients.update');
Route::delete('clients/{user}', [ClientController::class, 'destroy'])->name('clients.destroy');
*/


