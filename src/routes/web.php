<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'odyssey-s3'], function () {


    Route::group(['prefix' => 'sync-object'], static function () {
        Route::get('/', 'S3SyncController@index');
        Route::post('/', 'S3SyncController@store');
        Route::put('/{syncObject}', 'S3SyncController@update');
        Route::get('/{syncObject}', 'S3SyncController@show');
        Route::delete('/{syncObject}', 'S3SyncController@destroy');
        Route::get('/check/sync', 'S3SyncController@check');
        Route::post('/resend', 'S3SyncController@resend');
    });


    Route::group(['prefix' => 'credentials'], static function () {
        Route::post('/', 'S3AuthController@store');
        Route::get('/', 'S3AuthController@show');

    });
    Route::group(['prefix' => 'sync-object-tag'], static function () {
        Route::get('/', 'S3SyncObjectTagController@index');
    });

    Route::group(['prefix' => 'sync-history'], static function () {
        Route::get('/', 'S3HistoryController@index');
    });

});
