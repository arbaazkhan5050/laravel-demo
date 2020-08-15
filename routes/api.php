<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('authenticate', 'AuthenticateController@authenticate');

Route::group(['middleware' => 'jwt.auth'], function()
{
    Route::get('user', 'UserController@show');

   //item 
   Route::group(['prefix' => 'item'], function() {
    Route::post('/', 'ItemController@store');
    Route::get('/', 'ItemController@index');
    Route::get('show/{id}', 'ItemController@show');
    Route::post('/update', 'ItemController@update');
    Route::delete('/deleteItem/{id}', 'ItemController@destroy');
    Route::delete('deleteImage/{id}/{type}', 'ItemController@deleteImage');
 });



});
