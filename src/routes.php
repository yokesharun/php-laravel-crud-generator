<?php

/*
|--------------------------------------------------------------------------
| CRUD Routes
|--------------------------------------------------------------------------
*/

Route::get('laravel/generator', 
	'yokesharun\laravelcrud\Controllers\CRUDController@index')->name('laravelcrud.index');
Route::post('laravel/generator', 
	'yokesharun\laravelcrud\Controllers\CRUDController@generateFiles')->name('laravelcrud.submit');
