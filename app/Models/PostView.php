<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','post_id'];

    //connect with post
    public function post(){
        return $this->belongsTo(Post::class);
    }
}
