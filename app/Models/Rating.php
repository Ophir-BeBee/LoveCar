<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shop_id','star','feedback'];

    //connect with user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
}
