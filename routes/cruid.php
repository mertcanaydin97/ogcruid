<?php

use Illuminate\Support\Str;
use Og\Cruid\Events\Routing;
use Og\Cruid\Events\RoutingAdmin;
use Og\Cruid\Events\RoutingAdminAfter;
use Og\Cruid\Events\RoutingAfter;
use Og\Cruid\Facades\Cruid;

/*
|--------------------------------------------------------------------------
| Cruid Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Cruid.
|
*/

Route::group(['as' => 'cruid.'], function () {
    event(new Routing());

    $namespacePrefix = '\\'.config('cruid.controllers.namespace').'\\';

    Route::get('login', ['uses' => $namespacePrefix.'CruidAuthController@login',     'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'CruidAuthController@postLogin', 'as' => 'postlogin']);

    Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        event(new RoutingAdmin());

        // Main Admin and Logout Route
        Route::get('/', ['uses' => $namespacePrefix.'CruidController@index',   'as' => 'dashboard']);
        Route::post('logout', ['uses' => $namespacePrefix.'CruidController@logout',  'as' => 'logout']);
        Route::post('upload', ['uses' => $namespacePrefix.'CruidController@upload',  'as' => 'upload']);

        Route::get('profile', ['uses' => $namespacePrefix.'CruidUserController@profile', 'as' => 'profile']);

        try {
            foreach (Cruid::model('DataType')::all() as $dataType) {
                $breadController = $dataType->controller
                                 ? Str::start($dataType->controller, '\\')
                                 : $namespacePrefix.'CruidBaseController';

                Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.update_order');
                Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                Route::post($dataType->slug.'/remove', $breadController.'@remove_media')->name($dataType->slug.'.media.remove');
                Route::resource($dataType->slug, $breadController, ['parameters' => [$dataType->slug => 'id']]);
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

        // Menu Routes
        Route::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
        ], function () use ($namespacePrefix) {
            Route::get('builder', ['uses' => $namespacePrefix.'CruidMenuController@builder',    'as' => 'builder']);
            Route::post('order', ['uses' => $namespacePrefix.'CruidMenuController@order_item', 'as' => 'order_item']);

            Route::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () use ($namespacePrefix) {
                Route::delete('{id}', ['uses' => $namespacePrefix.'CruidMenuController@delete_menu', 'as' => 'destroy']);
                Route::post('/', ['uses' => $namespacePrefix.'CruidMenuController@add_item',    'as' => 'add']);
                Route::put('/', ['uses' => $namespacePrefix.'CruidMenuController@update_item', 'as' => 'update']);
            });
        });

        // Settings
        Route::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'CruidSettingsController@index',        'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'CruidSettingsController@store',        'as' => 'store']);
            Route::put('/', ['uses' => $namespacePrefix.'CruidSettingsController@update',       'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'CruidSettingsController@delete',       'as' => 'delete']);
            Route::get('{id}/move_up', ['uses' => $namespacePrefix.'CruidSettingsController@move_up',      'as' => 'move_up']);
            Route::get('{id}/move_down', ['uses' => $namespacePrefix.'CruidSettingsController@move_down',    'as' => 'move_down']);
            Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'CruidSettingsController@delete_value', 'as' => 'delete_value']);
        });

        // Admin Media
        Route::group([
            'as'     => 'media.',
            'prefix' => 'media',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'CruidMediaController@index',              'as' => 'index']);
            Route::post('files', ['uses' => $namespacePrefix.'CruidMediaController@files',              'as' => 'files']);
            Route::post('new_folder', ['uses' => $namespacePrefix.'CruidMediaController@new_folder',         'as' => 'new_folder']);
            Route::post('delete_file_folder', ['uses' => $namespacePrefix.'CruidMediaController@delete', 'as' => 'delete']);
            Route::post('move_file', ['uses' => $namespacePrefix.'CruidMediaController@move',          'as' => 'move']);
            Route::post('rename_file', ['uses' => $namespacePrefix.'CruidMediaController@rename',        'as' => 'rename']);
            Route::post('upload', ['uses' => $namespacePrefix.'CruidMediaController@upload',             'as' => 'upload']);
            Route::post('crop', ['uses' => $namespacePrefix.'CruidMediaController@crop',             'as' => 'crop']);
        });

        // BREAD Routes
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'CruidBreadController@index',              'as' => 'index']);
            Route::get('{table}/create', ['uses' => $namespacePrefix.'CruidBreadController@create',     'as' => 'create']);
            Route::post('/', ['uses' => $namespacePrefix.'CruidBreadController@store',   'as' => 'store']);
            Route::get('{table}/edit', ['uses' => $namespacePrefix.'CruidBreadController@edit', 'as' => 'edit']);
            Route::put('{id}', ['uses' => $namespacePrefix.'CruidBreadController@update',  'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'CruidBreadController@destroy',  'as' => 'delete']);
            Route::post('relationship', ['uses' => $namespacePrefix.'CruidBreadController@addRelationship',  'as' => 'relationship']);
            Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'CruidBreadController@deleteRelationship',  'as' => 'delete_relationship']);
        });

        // Database Routes
        Route::resource('database', $namespacePrefix.'CruidDatabaseController');

        // Compass Routes
        Route::group([
            'as'     => 'compass.',
            'prefix' => 'compass',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'CruidCompassController@index',  'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'CruidCompassController@index',  'as' => 'post']);
        });

        event(new RoutingAdminAfter());
    });

    //Asset Routes
    Route::get('cruid-assets', ['uses' => $namespacePrefix.'CruidController@assets', 'as' => 'cruid_assets']);

    event(new RoutingAfter());
});
