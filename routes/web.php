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
use Illuminate\Support\Facades\Auth;

Route::get('/deeplink', function() {
    return redirect('deeplink');
});

Route::get('/', function() {
    // return redirect('admin/dashboard');
    return redirect('admin/users');
});
Route::get('/admin/', function() {
    // return redirect('admin/dashboard');
    return redirect('admin/users');
});



Route::group(['namespace' => ''], function () {
    Route::get('/deeplink', '\App\Http\Controllers\Auth\RegisterController@deeplinking');
});

/**
 * Authentication URIs
 */
Auth::routes();
Route::group(['namespace' => 'Auth'], function () {
    Route::get('/auth/logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::get('/register/verify/{confirmation_code}', 'RegisterController@confirm');
});


/**
 * Admin Panel URIs
 */
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth']], function () {

    /* Dashboard */
    Route::get('/dashboard', 'DashboardController@index');
    Route::get('/dashboard/index', 'DashboardController@index');

    /* User Management */
    Route::get('/users', 'UserController@index');
    Route::get('/users/new', 'UserController@get_user');
    Route::post('/users/update', 'UserController@update_user');
    Route::get('/users/{user_id}/videos', 'UserController@get_videos');
    Route::get('/users/{user_id}/payments', 'UserController@get_payments');
    Route::get('/users/{user_id}/achievements', 'UserController@get_achievements');
    Route::get('/users/{user_id}/edit', 'UserController@get_user');
    Route::get('/users/{user_id}/active', 'UserController@active_user');
    Route::get('/users/{user_id}/inactive', 'UserController@inactive_user');
    Route::get('/users/{user_id}/delete', 'UserController@delete_user');
    /* Video */
    Route::get('/users/{video_id}/edit_video', 'UserController@edit_video');
    Route::get('/users/{video_id}/delete_video', 'UserController@delete_video');
    Route::post('/users/{video_id}/update_video', 'UserController@update_video');
    
    /* Categories */
    Route::get('/categories', 'CategoryController@index');
    Route::get('/categories/index', 'CategoryController@index');
    Route::get('/categories/add', 'CategoryController@create_category');
//    Route::post('/categories/create', 'CategoryController@create_category');
//    Route::get('/categories/edit', 'CategoryController@edit_category');
//    Route::post('/categories/update', 'CategoryController@edit_category');
//    Route::post('/categories/delete', 'CategoryController@delete_category');

    /* Tags */
    Route::get('/tags', 'CategoryController@getTags');
    Route::get('/tags/add', 'CategoryController@goto_add_tag');
    Route::post('/tags/create', 'CategoryController@create_tag');
    Route::get('/tags/{tag_id}/edit', 'CategoryController@goto_edit_tag');
    Route::post('/tags/{tag_id}/update', 'CategoryController@update_tag');
    Route::get('/tags/{tag_id}/delete', 'CategoryController@delete_tag');

    /* Ajax */
    Route::get('/ajax/categories/get', 'CategoryController@ajax_get_category');
    Route::get('/ajax/categories/next_id', 'CategoryController@ajax_next_id');
    Route::get('/ajax/tags/next_id', 'CategoryController@ajax_next_tag_id');
    Route::get('/ajax/tags/get', 'CategoryController@ajax_get_tag');
    Route::get('/users/{user_id}/ajax/update_connection', 'UserController@ajax_update_connection');

    /* Tricks Management */
    Route::get('/tricks', 'TrickController@index');
    Route::get('/tricks/add', 'TrickController@getTrick');
    Route::get('/tricks/{id}', 'TrickController@getTrick');
    Route::get('/tricks/{id}/delete', 'TrickController@deleteTrick');
    Route::post('/tricks', 'TrickController@postTrick');
    Route::post('/tricks/updateOrAdd', 'TrickController@updateOrAddTrick');
    /* Payment Management */
    Route::get('/transactions', 'PaymentController@index');
    Route::get('/transactions/index', 'PaymentController@index');

    /* Unlock Management */
    Route::get('/unlock_rule', 'UnlockController@index');
    Route::post('/unlock_rule/update_rule', 'UnlockController@update_unlock_rule');

    /* Account Setting */
    Route::get('/setting', 'SettingController@index');
    Route::get('/setting/index', 'SettingController@index');
});


