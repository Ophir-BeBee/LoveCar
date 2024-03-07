<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','description'];

    //connect with likes
    public function tutorial_likes(){
        return $this->hasMany(TutorialLike::class);
    }

    //connect with steps
    public function tutorial_steps(){
        return $this->hasMany(TutorialStep::class);
    }
}
