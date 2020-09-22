<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'buckets'], static function () {
    Route::get('/', 'BucketController@index');
    Route::post('/', 'BucketController@store');
});


Route::group(['prefix' => 'credentials'], static function () {
    Route::post('/', 'S3AuthController@store');
    Route::put('/', 'BucketController@update');
});
