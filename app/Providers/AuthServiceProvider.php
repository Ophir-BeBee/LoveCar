<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Car;
use App\Models\CarServicingCost;
use App\Models\CheckList;
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

        //tutorial authorization
        Gate::define('auth-tutorial',function(User $user){
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

        //shop rating delete authorization
        Gate::define('auth-rating-delete',function(User $user,Shop $shop){
            return ($user->type === 'admin' || $shop->user_id === Auth::user()->id) ? true : false;
        });

        //shop category authorization
        Gate::define('auth-shop-category',function(User $user){
            return $user->type === 'admin';
        });

        //shop category service
        Gate::define('auth-shop-service',function(User $user){
            return ($user->type === 'admin' || $user->type === 'bussiness_owner') ? true : false;
        });

        //car update authorization
        Gate::define('auth-car-update',function(User $user,Car $car){
            return $car->user_id === $user->id;
        });

        //car delete authorization
        Gate::define('auth-car-delete',function(User $user,Car $car){
            return ($car->user_id === $user->id || $user->type === 'admin') ? true : false;
        });

        //fuel cost authorization
        Gate::define('auth-car-fuel_cost',function(User $user,Car $car){
            return $user->id === $car->user_id;
        });

        //fuel cost delete authorization
        Gate::define('auth-car-fuel_cost-delete',function(User $user,Car $car){
            return ($user->type === 'admin' || $car->user_id === $user->id) ? true : false;
        });

        //servicing cost authorization
        Gate::define('auth-car-servicing_cost-update',function(User $user,Car $car){
            return $car->user_id === $user->id;
        });

        //servicing cost authorization
        Gate::define('auth-car-servicing_cost-delete',function(User $user,Car $car){
            return ($car->user_id === $user->id || $user->type === 'admin');
        });

        //check list update authorization
        Gate::define('auth-checkList-update',function(User $user,Car $car){
            return $car->user_id === $user->id;
        });

        //check list delete authorization
        Gate::define('auth-checkList-delete',function(User $user,Car $car){
            return ($user->type === 'admin' || $car->user_id === $user->id);
        });

        //rental cost update authorization
        Gate::define('auth-rental_cost-update',function(User $user,Car $car){
            return $car->user_id === $user->id;
        });

        //rental cost delete authorization
        Gate::define('auth-rental_cost-delete',function(User $user,Car $car){
            return ($car->user_id === $user->id || $user->type === 'admin');
        });

        //income update authorization
        Gate::define('auth-income-update',function(User $user,Car $car){
            return $car->user_id === $user->id;
        });

        //income delete authorization
        Gate::define('auth-income-delete',function(User $user,Car $car){
            return ($car->user_id === $user->id || $user->type === 'admin');
        });

    }
}
