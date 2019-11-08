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

// Home routes
Route::get('/', 'HomeController@index')->name('home');
Route::get('/privacy', 'HomeController@privacy')->name('home.privacy');
Route::post('/', 'HomeController@scroll')->name('home.scroll');

// Video routes
Route::get('video/search', 'VideosController@search')->name('video.search');
Route::post('video/vote', 'VideosController@vote')->name('video.vote')->middleware('auth');
Route::resource('video', 'VideosController', ['except' => ['index','show', 'edit', 'update']])->middleware('auth');
Route::resource('video', 'VideosController', ['only' => ['index','show', 'edit', 'update']]);
Route::resource('video', 'VideosController', ['only' => ['show']])->middleware('viewsCounter');

// Comment routes
Route::resource('comment', 'CommentsController', ['except' => ['index','show']])->middleware('auth');
Route::resource('comment', 'CommentsController', ['only' => ['index','show']]);

// Channel routes
Route::get('channel/{userId}', 'ChannelController@index')->name('channel.index');
Route::post('channel/subscribe', 'ChannelController@subscribe')->name('channel.subscribe')->middleware('auth');

// category routes
Route::get('category/{name}', 'CategoryController@index')->name('category.index');

Auth::routes();
