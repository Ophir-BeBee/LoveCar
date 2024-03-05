<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','brand_id','model_id','mileage','usage','plate_no','fuel_type','color'];

    //connect with users
    public function users(){
        return $this->hasMany(User::class);
    }

    //connect with brand
    public function brand(){
        return $this->belongsTo(CarBrand::class);
    }

    //public function model
    public function model(){
        return $this->belongsTo(CarModel::class);
    }

    //connect with fuel costs
    public function fuel_costs(){
        return $this->hasMany(FuelCost::class);
    }
}
