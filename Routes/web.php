<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'backend'], function () {
    Voyager::routes();
});

Route::get(config('imagecache.route').'/{template}/{filename}', [
    'uses' => 'Modules/Admin/Http/Controllers/Intervention/ImageCacheController@getResponse',
    'as'   => 'imagecache'
])->where('filename', '[ \w\\.\\/\\-\\@]+');