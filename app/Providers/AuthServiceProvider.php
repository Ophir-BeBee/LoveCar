<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Models\Save;
use App\Models\Shop;
use App\Models\User;
use App\Models\Rating;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //post authorization
        Gate::define('auth-post',function(User $user){
            return $user->type ===  'admin';
        });

        //comment authorization
        Gate::define('auth-comment',function(User $user,Comment $comment){
            return ($user->type === 'admin' || $comment->user_id === $user->id) ? true : false;
        });

        //shop autorization
        Gate::define('auth-shop',function(User $user){
            return ($user->type === 'admin' || $user->type === 'bussiness_ower') ? true : false;
        });

        //notification authorization
        Gate::define('auth-noti',function(User $user){
            return $user->type === 'admin';
        });

        //shop ads authorization
        Gate::define('auth-shopAds',function(User $user){
            return $user->type === 'admin';
        });

        //shop rating update authorization
        Gate::define('auth-rating-update',function(Rating $rating){
            return $rating->user_id == Auth::user()->id;
        });

        //shop rating delete
        Gate::define('auth-rating-delete',function(User $user,Shop $shop){
            return ($user->type === 'admin' || $shop->user_id === Auth::user()->id) ? true : false;
        });

    }
}
