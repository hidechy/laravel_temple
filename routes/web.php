<?php

Route::get('/', 'Temple\TempleController@index');
Route::post('/callphoto', 'Temple\TempleController@callphoto');
Route::get('/{getdate}/output', 'Temple\TempleController@output');

Route::get('/{getdate}/templelistapi', 'Temple\TempleController@templelistapi');
Route::get('/{getdate}/templephotoapi', 'Temple\TempleController@templephotoapi');
Route::get('/{getdate}/templelatlngapi', 'Temple\TempleController@templelatlngapi');
Route::get('/{getdate}/templelatlnglistapi', 'Temple\TempleController@templelatlnglistapi');

Route::get('/templecreate', 'Temple\TempleController@templecreate');
Route::post('/templestore', 'Temple\TempleController@templestore');

Route::get('/templeaddress', 'Temple\TempleController@templeaddress');
Route::post('/templeaddressinput', 'Temple\TempleController@templeaddressinput');

Route::get('/{getdate}/templemap', 'Temple\TempleController@templemap');
