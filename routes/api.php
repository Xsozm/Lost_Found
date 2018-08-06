<?php

use Illuminate\Http\Request;



//middleware auth:Api check if the token refer to existing user



Route::group([

    'prefix' => 'auth'

], function () {

    Route::post('/login', 'AuthController@login'); //guest return token to him
    Route::post('/logout', 'AuthController@logout'); // logged in with token
    Route::post('/refresh', 'AuthController@refresh'); // logged in with token
    Route::post('/me', 'AuthController@me'); // logged in with token



});



Route::apiresource('items','ItemController'); //  Items admin

Route::group(['prefix'=>'items'],function (){
    Route::apiresource('/{item}/tags','TagController'); // logged in tags for specific item
    Route::apiresource('/{item}/images','ImageController'); // images for specific item


});

