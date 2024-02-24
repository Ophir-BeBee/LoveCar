<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Models\Save;
use App\Models\User;
use App\Models\Comment;
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

        //unsave authorization
        Gate::define('auth-unsave',function(User $user,Save $save){
            return $save->user_id === $user->id;
        });

        //shop autorization
        Gate::define('auth-shop',function(User $user){
            return ($user->type === 'admin' || $user->type === 'bussiness_ower') ? true : false;
        });

    }
}
