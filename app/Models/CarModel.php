<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id','name','image'];

    //connect with brand
    public function car_brand(){
        return $this->belongsTo(CarBrand::class);
    }

    //connect with car
    public function cars(){
        return $this->hasMany(Car::class);
    }
}
