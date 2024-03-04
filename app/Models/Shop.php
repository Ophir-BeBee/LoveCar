<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shop_category_id','name','latitude','longitude','phone','address','ratings'];

    //connect with user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //connect with shop category
    public function shop_to_categories(){
        return $this->hasMany(ShopToCategory::class);
    }

    //connect with services
    public function shop_to_services(){
        return $this->hasMany(ShopToService::class);
    }

    public function shop_service_items(){
        return $this->hasMany(ShopService::class);
    }

    //connect with images
    public function shop_images(){
        return $this->hasMany(ShopImage::class);
    }

    //connect with favorites
    public function favorite_shops(){
        return $this->hasMany(FavoriteShop::class);
    }

    //connect with ads
    public function shop_ads(){
        return $this->hasMany(ShopAd::class);
    }

    //connect with ratings
    public function rating(){
        return $this->hasMany(Rating::class);
    }
}
