<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleLikeController extends Controller
{

    protected $model;

    public function __construct(ArticleLike $model)
    {
        $this->model = $model;
    }

    public function toggle(Request $request){
        //check post
        $article = Article::find($request->article_id);
        if(!$article){
            return sendResponse(null,404,'Article not found');
        }

        //check liked or not
        $check = $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            //delete like
            $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->delete();
            //get post data to return
            $data = Article::where('id',$request->article_id)
            ->select('id')
            ->withCount('article_likes')
            ->withCount(['article_likes as is_liked' => function($query){
                $query->where('article_likes.user_id',Auth::user()->id);
            }])
            ->first();
            return sendResponse($data,200,'You unliked this article');
        }

         //create like
         $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'article_id' => $request->article_id
        ]);

        $data = $this->model
        ->where('article_id',$request->article_id)
        ->select('id','article_id')
        ->with(['article' => function($query){
            $query->select('id')
            ->withCount('article_likes')
            ->withCount(['article_likes as is_liked' => function($query){
                $query->where('article_likes.user_id',Auth::user()->id);
            }]);
        }])
        ->first();
        return sendResponse($data->article,200,'You liked this article');
    }
}
