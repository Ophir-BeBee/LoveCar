<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ArticleLikeCreateRequest;

class ArticleLikeController extends Controller
{

    protected $model;

    public function __construct(ArticleLike $model)
    {
        $this->model = $model;
    }

    //give like
    public function store(Request $request){
        //check article
        $article = Article::find($request->article_id);
        if(!$article){
            return response()->json([
                'message' => 'Article not found',
                'status' => 404
            ]);
        }

        //check liked or not
        $check = $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            return response()->json([
                'message' => 'You already liked this article',
                'status' => 405
            ]);
        }

        //create like
        $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'article_id' => $request->article_id
        ]);
        return response()->json([
            'data' => $data,
            'message' => 'You liked this article',
            'status' => 200
        ]);
    }

    //unlike article
    public function destroy(Request $request){
        //check article
        $article = Article::find($request->article_id);
        if(!$article){
            return response()->json([
                'message'=> 'Article not found',
                'status' => 404
            ]);
        }

        //delete like
        $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->delete();
        return response()->json([
            'data' => null,
            'message' => 'You unliked this article',
            'status' => 200
        ]);
    }
}
