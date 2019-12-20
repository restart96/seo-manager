<?php
/**
 * @author SJ
 * @date   2019.10.19
 */

Route::group([
    'prefix' => config('seo-manager.route'),
    'middleware' => config('seo-manager.middleware'),
    'as' => 'seo-manager.',
    'namespace' => 'Restart\SeoManager\App\Http\Controllers',
], function() {

    // Main
    Route::get('/', [
        'as' => 'index',
        'uses' => 'SeoManagerController@index',
    ]);

    // Locales
    Route::group(['prefix' => 'locales'], function() {
        Route::get('/', [
            'uses' => 'LocalesController@index',
        ]);

        Route::post('/', [
            'uses' => 'LocalesController@store',
        ]);
    });

    // Routes
    Route::group(['prefix' => 'routes'], function() {
        Route::get('/', [
            'uses' => 'RoutesController@index',
        ]);

        Route::get('/{id}/edit', [
            'uses' => 'RoutesController@edit',
        ])->where('id', '[0-9]+');

        Route::post('/', [
            'uses' => 'RoutesController@store',
        ]);

        Route::patch('/{id}', [
            'uses' => 'RoutesController@update'
        ])->where('id', '[0-9]+');

        Route::delete('/{id}', [
            'uses' => 'RoutesController@destroy'
        ])->where('id', '[0-9]+');

        Route::patch('/reorder', [
            'uses' => 'RoutesController@reOrder',
        ]);
    });

    // Models
    Route::group(['prefix' => 'models'], function() {
        Route::get('/', [
            'uses' => 'ModelsController@getModels',
        ]);
    });
});