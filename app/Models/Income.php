<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['car_id','income_date_id','from','to','price'];

    //connect with incoem date
    public function income_date(){
        return $this->belongsTo(IncomeDate::class);
    }
}
