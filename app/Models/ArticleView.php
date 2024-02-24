<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','article_id'];

    //connect with article
    public function article(){
        return $this->belongsTo(Article::class);
    }
}
