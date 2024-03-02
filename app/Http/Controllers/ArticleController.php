<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    //get all articles
    public function index(){
        $data = $this->model
        ->withCount('article_likes')
        ->withCount(['article_likes as is_liked' => function($query){
            $query->where('article_likes.user_id',Auth::user()->id);
        }])
        ->with('article_images')
        ->orderBy('id','desc')->get();
        return sendResponse($data,200);
    }

    //article show
    // public function show($id){
    //     //get article data
    //     $article = $this->model
    //     ->where('id',$id)
    //     ->withCount('article_likes')
    //     ->first();

    //     //increase view
    //     ArticleView::create([
    //         'user_id' => Auth::user()->id,
    //         'article_id' => $id
    //     ]);

    //     return sendResponse($article,200);
    // }

    //create article
    public function store(ArticleRequest $request){
        //user authorization
        if(Gate::denies('auth-post')){
            return sendResponse(null,401,"Not allowed");
        }

        //create data or article
        $data = $this->changeArticleCreateDataToArray($request);
        $article = $this->model->create($data);

        //check photos inclued or not
        if($request->file('image')){

            $imageFile = $request->file('image');

            //four photos validation
            $imageCount = count($imageFile);
            if($imageCount>4){
                return sendResponse(null,405,"Can't upload more than 4 photos");
            }

            //store photos
            for($i=0;$i<$imageCount;$i++){
                $imageName = uniqid() . '_' . time() . '.' . $imageFile[$i]->getClientOriginalExtension();
                $imageFile[$i]->storeAs('public',$imageName);
                ArticleImage::create([
                    'article_id' => $article->id,
                    'name' => $imageName
                ]);
            }
        }
        $data = $article->where('id',$article->id)->withCount('article_likes')->first();
        return sendResponse($data,200,'Article has been created');
    }

    //view article
    public function view(Request $request){
        ArticleView::create([
            'user_id' => Auth::user()->id,
            'article_id' => $request->article_id
        ]);
        return sendResponse(null,200,'You viewed this article');
    }

    //update article
    public function update(ArticleRequest $request){

        //user authorization
        if(Gate::denies('auth-post')){
            return sendResponse(null,401,'Not allowed');
        }

        $article = $this->model->find($request->article_id);

        if(!$article){
            return sendResponse(null,404,'Article not found');
        }

        //update data
        $data = $this->changeArticleUpdateDataToArray($request);
        $article->update($data);

        //delete all images which belongs to this post from project folder and database
        $images_from_db = ArticleImage::where('article_id',$request->article_id)->get();

        foreach($images_from_db as $image){
            Storage::delete('public/'.$image->name);
            $image->delete();
        }

        //update new images
        if($request->file('image')){

            $imageFile = $request->file('image');

            //four images validation
            $imageCount = count($imageFile);
            if($imageCount>4){
                return sendResponse(null,405,"Can't upload more than 4 photos");
            }

            //update images
            for($i=0;$i<$imageCount;$i++){
                $imageName = uniqid() . '_' . time() . '.' . $imageFile[$i]->getClientOriginalExtension();
                $imageFile[$i]->storeAs('public',$imageName);
                ArticleImage::create([
                    'article_id' => $request->article_id,
                    'name' => $imageName
                ]);
            }

        }

        $data = $this->model->where('id',$request->id)->withCount('article_likes')->first();
        return sendResponse($data,200,'Article has beeen updated');
    }

        //delete posts
        public function destroy(Request $request){

            // user authorization
            if(Gate::denies('auth-post')){
                return sendResponse(null,401,'Not allowed');
            }

            //image manual delete
            $images = ArticleImage::where('article_id',$request->article_id)->get();
            foreach($images as $image){
                Storage::delete('public/'.$image->name);
                $image->delete();
            }

            $this->model->find($request->article_id)->delete();
            return sendResponse(null,200,'Article has beeen deleted');
        }

    //change article data to array
    private function changeArticleUpdateDataToArray($request){
        return [
            'title' => $request->title,
            'description' => $request->description
        ];
    }

    //change article data to array
    private function changeArticleCreateDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'description' => $request->description
        ];
    }
}
