<?php

$namespace = "Hongon\\Hongon\\Controllers";

Route::get('api/hongon/{type}', ['uses' => "$namespace\GeneralCRUDController@getItems"]);
Route::get('api/hongon/{type}/{id}', ['uses' => "$namespace\GeneralCRUDController@getItem"]);