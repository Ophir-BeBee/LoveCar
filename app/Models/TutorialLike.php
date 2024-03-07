<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialLike extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','tutorial_id'];

    //connect with tutorial
    public function tutorial(){
        return $this->belongsTo(Tutorial::class);
    }
}
