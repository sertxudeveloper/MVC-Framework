<?php

require_once 'bootstrap/Route.php';

Route::get('/', 'MainController@index');
Route::get('/foo', 'MainController@foo');

Route::get('/user/([0-9]*)', 'UserController@show');
Route::get('/user/([0-9]*)/edit', 'UserController@edit');
Route::post('/user/([0-9]*)/edit', 'UserController@postEdit');

Route::run('/');
