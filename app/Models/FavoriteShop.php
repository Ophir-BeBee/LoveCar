<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteShop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shop_id'];

    //connect with user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
}
