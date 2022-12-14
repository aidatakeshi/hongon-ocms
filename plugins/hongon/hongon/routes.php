<?php

//Special APIs
Route::controller(Hongon\Hongon\Controllers\StationController::class)->group(function () {

    Route::get('api/hongon/station--name-similar', 'getSimilarStationNames');

});

//General APIs
Route::controller(Hongon\Hongon\Controllers\ItemGetController::class)->group(function () {

    Route::get('api/hongon/{type}', 'getMultipleItems');
    Route::get('api/hongon/{type}/{id}', 'getOneItem');

});

Route::controller(Hongon\Hongon\Controllers\ItemChangeController::class)
->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken')->group(function () {

    Route::post('api/hongon/{type}', 'newItem');
    Route::post('api/hongon/{type}/{id}', 'duplicateItem');
    Route::patch('api/hongon/{type}/{id}', 'updateItem');
    Route::patch('api/hongon/{type}', 'updateItems');
    Route::put('api/hongon/{type}', 'reorderItems');
    Route::delete('api/hongon/{type}/{id}', 'removeItem');

});