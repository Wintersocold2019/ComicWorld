<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'PageController@getComicTypes');

Route::prefix('/admin')->group(function() {
        Route::get('/', 'PageController@toAdmin');
        Route::get('/delete-all', 'AdminController@deleteData')->name('admin.delete-all');
    }
);

Route::get('/{rootUrl}',  'ScraperController@getData');