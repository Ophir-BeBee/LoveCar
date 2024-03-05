<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ArticleLikeController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\FavoriteShopController;
use App\Http\Controllers\FuelCostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ShopAdController;
use App\Http\Controllers\ShopCategoryController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopServiceController;

Route::controller(UserController::class)->group(function(){
    Route::post('/login','login');
});

Route::middleware('auth:sanctum')->group(function () {

    //feed
    Route::group(["prefix" => "posts", "controller" => PostController::class], function() {
        //posts
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/view','view');
        Route::post('/update','update');
        Route::delete('/','destroy');

        //likes
        Route::group(["prefix" => "likes", "controller" => PostLikeController::class],function(){
            Route::post('/','toggle');
        });

        //comments
        Route::group(["prefix" => "comments", "controller" => CommentController::class],function(){
            Route::post('/','store');
            Route::post('/update','update');
            Route::get('/{post_id}','show');
            Route::delete('/','destroy');
        });

        //saves
        Route::group(["prefix" => "saves", "controller" => SaveController::class],function(){
            Route::get('/','index');
            Route::post('/','toggle');
        });
    });

    //news
    Route::group(["prefix" => "articles", "controller" => ArticleController::class],function(){
        //articles
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/view','view');
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
        Route::get('/show/{id}','show');
        Route::delete('/','destroy');
        Route::post('/update','update');

        //favorites
        Route::group(["prefix" => "favorites", "controller" => FavoriteShopController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::delete('/','destroy');
        });

        //ads
        Route::group(["prefix" => "ads", "controller" => ShopAdController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });

        //ratings
        Route::group(["prefix" => "ratings", "controller" => RatingController::class],function(){
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });

        //category
        Route::group(["prefix" => "categories", "controller" => ShopCategoryController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });

        //service
        Route::group(["prefix" => "services", "controller" => ShopServiceController::class],function(){
            Route::get('/','index');
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });
    });

    //cars
    Route::group(["prefix" => "cars", "controller" => CarController::class],function(){
        Route::post('/','store');
        Route::get('/','index');
        Route::post('/update','update');
        Route::get('/all','all');
        Route::delete('/','destroy');

        //fuel costs
        Route::group(["prefix" => "fuel_costs", "controller" => FuelCostController::class],function(){
            Route::get('/{carId}/{month}/{year}','index');
            Route::post('/','store');
            Route::post('/update','update');
            Route::delete('/','destroy');
        });
    });

    //notifications
    Route::group(["prefix" => "notifications", "controller" => NotificationController::class],function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::delete('/','destroy');
        Route::get('/{id}','show');
        Route::post('/update','update');
        Route::post('/read','read');
        Route::post('/hide','hide');
    });

});
