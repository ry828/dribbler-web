<?php

use Illuminate\Http\Request;
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

Route::group(['namespace' => 'Admin'], function () {
    Route::post('/ajax_categories/id', 'CategoryController@ajax_next_id');
});


/* Restful API Version 1 */

Route::group(['prefix' => 'v1'], function () {

    Route::post('/users/me', 'RestfulAPIController@login');
    Route::post('/users/register', 'RestfulAPIController@register');
    Route::post('/password/forgot', 'RestfulAPIController@forgot_password');


    /**
     * Routes base on JWT Token
     */
    Route::group(['middleware' => ['jwt.auth', 'jwt.refresh']], function() {

        /* user module */
        Route::post('/users/me/profile', 'RestfulAPIController@postProfile');
        Route::post('users/me/logout', 'RestfulAPIController@logout');
        Route::delete('/users/me', 'RestfulAPIController@deleteProfile');
        Route::get('/users/{user_id}/status', 'RestfulAPIController@getProfileStatus');
        Route::get('/users/{user_id}/profile', 'RestfulAPIController@getOtherUserProfile');

        Route::get('/users/update_profile', 'RestfulAPIController@updateProfiles');
        Route::get('/users/{user_id}/get_achievements', 'RestfulAPIController@getAchievements');
        Route::get('/users/{user_id}/users', 'RestfulAPIController@get_users');

        // follow user or get Follower list
        Route::post('/users/{user_id}/follower', 'RestfulAPIController@follow_user');
        Route::get('/users/{user_id}/followings', 'RestfulAPIController@get_following_list');
        Route::get('/users/{user_id}/followers', 'RestfulAPIController@get_follower_list');

        /* others Profile */
        Route::get('/profiles/{user_id}', 'RestfulAPIController@get_profile');

        /* Categories */
        Route::get('/categories', 'RestfulAPIController@get_categories');
        Route::get('/{category_id}/unlock_category', 'RestfulAPIController@unlock_category');
        /* Tags */
        Route::get('/tags', 'RestfulAPIController@get_tags');

        /* Tricks */
        Route::get('/tricks/{trick_id}/statistics', 'RestfulAPIController@get_trick_statistics');
        Route::get('tricks/{trick_id}/users', 'RestfulAPIController@get_trick_users');
        Route::get('tricks/{trick_id}/videos', 'RestfulAPIController@get_trick_videos');
        Route::get('tricks/{category_id}/get_tricks_by_category', 'RestfulAPIController@get_tricks_by_category');

        /* Dribbler */
        Route::post('/dribblers', 'RestfulAPIController@post_dribbler');

        /* Video */
        Route::get('/videos', 'RestfulAPIController@get_my_video');
        Route::get('/videos/{video_id}', 'RestfulAPIController@get_video');
        Route::post('/videos', 'RestfulAPIController@post_video');
        Route::post('/videos/{video_id}/like', 'RestfulAPIController@like_video');
        Route::post('/videos/{video_id}/view', 'RestfulAPIController@view_video');
        Route::get('/videos/{video_id}/followers', 'RestfulAPIController@get_video_followers');

        /* Comment of video */
        Route::get('/videos/{video_id}/comments', 'RestfulAPIController@get_comments');
        Route::post('/videos/{video_id}/comments', 'RestfulAPIController@post_comment');

        /* Reply of comment */
        Route::get('/videos/{video_id}/comments/{comment_id}/reply', 'RestfulAPIController@get_reply');
        Route::post('/videos/{video_id}/comments/{comment_id}/reply', 'RestfulAPIController@get_reply');

        /* Feed */
        Route::get('/feeds/global', 'RestfulAPIController@get_global_feeds');
        Route::get('/feeds/follower', 'RestfulAPIController@get_follower_feeds');
    });
});
