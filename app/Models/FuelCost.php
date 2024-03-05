<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelCost extends Model
{
    use HasFactory;

    protected $fillable = ['car_id','date','price','liter','cost','mileage','fuel_type','station_name','city'];

    //connect with car
    public function car(){
        return $this->belongsTo(Car::class);
    }
}
