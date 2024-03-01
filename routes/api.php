<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ArticleLikeController;
use App\Http\Controllers\FavoriteShopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShopController;
use App\Models\Notification;

Route::controller(UserController::class)->group(function(){
    Route::post('/login','login');
});

Route::middleware('auth:sanctum')->group(function () {

    //feed
    Route::group(["prefix" => "posts", "controller" => PostController::class], function() {
        //posts
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/show','show');
        Route::post('/update','update');
        Route::delete('/','destroy');

        //likes
        Route::group(["prefix" => "likes", "controller" => PostLikeController::class],function(){
            Route::post('/','store');
            Route::delete('/','destroy');
        });

        //comments
        Route::group(["prefix" => "comments", "controller" => CommentController::class],function(){
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });

        //saves
        Route::group(["prefix" => "saves", "controller" => SaveController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::delete('/','destroy');
        });
    });

    //news
    Route::group(["prefix" => "articles", "controller" => ArticleController::class],function(){
        //articles
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/show','show');
        Route::post('/update','update');
        Route::delete('/','destroy');

        //likes
        Route::group(["prefix" => "likes", "controller" => ArticleLikeController::class],function(){
            Route::post('/','store');
            Route::delete('/','destroy');
        });
    });

    //shops
    Route::group(["prefix" => "shops", "controller" => ShopController::class],function(){
        //shops
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/show','show');
        Route::delete('/','destroy');
        Route::post('/update','update');

        //favorites
        Route::group(["prefix" => "favorites", "controller" => FavoriteShopController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::delete('/','destroy');
        });
    });

    //notifications
    Route::group(["prefix" => "notifications", "controller" => NotificationController::class],function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::delete('/','destroy');
        Route::post('/show','show');
        Route::post('/update','update');
        Route::post('/read','read');
        Route::post('/hide','hide');
    });

});
