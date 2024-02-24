<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shop_category_id','name','latitude','longitude','phone','address'];

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

    //connect with images
    public function shop_images(){
        return $this->hasMany(ShopImage::class);
    }
}
