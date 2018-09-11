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
    Route::post('/register','AuthController@register');



});

Route::post('tags/create_new_tag','TagController@create_new_tag');
Route::get('tags/show_all_tags','TagController@show_all_tags');
Route::get('tags/show_confirmed_tags','TagController@show_confirmed_tags');
Route::get('tags/show_not_confirmed_tags','TagController@show_not_confirmed_tags');
Route::delete('tags/delete_tag/{id}','TagController@delete_tag');


Route::get('items/show_confirmed_items','ItemController@show_confirmed_items');
Route::get('items/show_not_confirmed_items','ItemController@show_not_confirmed_items');
Route::apiresource('items','ItemController'); //  Items admin

Route::group(['prefix'=>'items'],function (){


    Route::apiresource('/{item}/tags','TagController'); // logged in tags for specific item
    Route::apiresource('/{item}/images','ImageController'); // images for specific item

    Route::put('/{item}/tags','TagController@update_tags_for_specific_item');
    Route::get('/search/{string}','ItemController@search');
    Route::get('/searchbytag/{string}','ItemController@searchbytag');







});

Route::post('/send','AuthController@send');
