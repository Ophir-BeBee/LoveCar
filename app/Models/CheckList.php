<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    use HasFactory;

    protected $fillable = ['car_id','category_id','part','condition'];

    //connect with car
    public function car(){
        return $this->belongsTo(Car::class);
    }

    //connect with category
    public function check_list_category(){
        return $this->belongsTo(CheckListCategory::class);
    }
}
