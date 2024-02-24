<?php

namespace App\Models;

use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','description','likes'];

    //connect with user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //connect with images
    public function post_images(){
        return $this->hasMany(PostImage::class);
    }

    //connect with likes
    public function post_likes(){
        return $this->hasMany(PostLike::class);
    }

    //connect with comments
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    //connect with saves
    public function saves(){
        return $this->hasMany(Save::class);
    }

    //connect with views
    public function post_views(){
        return $this->hasMany(PostView::class);
    }

}
