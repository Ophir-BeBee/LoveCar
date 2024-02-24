<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','description'];

    //connect with user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //connect with images
    public function article_images(){
        return $this->hasMany(ArticleImage::class);
    }

    //connect with likes
    public function article_likes(){
        return $this->hasMany(ArticleLike::class);
    }

    //connect with views
    public function article_views(){
        return $this->hasMany(ArticleView::class);
    }
}
