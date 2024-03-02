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

    //give like
    public function store(Request $request){
        //check article
        $article = Article::find($request->article_id);
        if(!$article){
            return sendResponse(null,404,'Article not found');
        }

        //check liked or not
        $check = $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            return sendResponse(null,405,'You already liked this article');
        }

        //create like
        $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'article_id' => $request->article_id
        ]);
        return sendResponse($data,200,'You liked this article');
    }

    //unlike article
    public function destroy(Request $request){
        //check article
        $article = Article::find($request->article_id);
        if(!$article){
            return sendResponse(null,404,'Article not found');
        }

        //delete like
        $this->model->where('article_id',$request->article_id)->where('user_id',Auth::user()->id)->delete();
        return sendResponse(null,200,'You unliked this article');
    }
}
