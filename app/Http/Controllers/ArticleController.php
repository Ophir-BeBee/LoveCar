<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ArticleUpateRequest;
use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleDeleteRequest;

class ArticleController extends Controller
{

    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    //get all articles
    public function index(){
        return response()->json([
            'data' => $this->model->withCount('article_likes')->orderBy('id','desc')->get(),
            'status' => 200
        ]);
    }

    //article show
    public function show($id){
        //get article data
        $article = $this->model
        ->where('id',$id)
        ->withCount('article_likes')
        ->first();

        //increase view
        ArticleView::create([
            'user_id' => Auth::user()->id,
            'article_id' => $id
        ]);

        return response()->json([
            'data' => $article,
            'status' => 200
        ]);
    }

    //create article
    public function store(ArticleRequest $request){
        //user authorization
        if(Gate::denies('auth-post')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
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
                return response()->json([
                    'message' => "Can't upload more than 4 photos",
                    'status' => 405
                ]);
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

        return response()->json([
            'data' => $article
            ->where('id',$article->id)
            ->withCount('article_likes')
            ->first(),
            'message' => 'Article has been created',
            'status' => 200
        ]);
    }

    //update article
    public function update(ArticleRequest $request){

        //user authorization
        if(Gate::denies('auth-post')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        $article = $this->model->find($request->article_id);

        if(!$article){
            return response()->json([
                'message' => 'Article not found',
                'status' => 404
            ]);
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
                return response()->json([
                    'message' => "Can't upload more than 4 photos",
                    'status' => 405
                ]);
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

        return response()->json([
            'data' => $this->model
            ->where('id',$request->id)
            ->withCount('article_likes')
            ->first(),
            'message' => 'Article has beeen updated',
            'status' => 200
        ]);

    }

        //delete posts
        public function destroy(Request $request){

            // user authorization
            if(Gate::denies('auth-post')){
                return response()->json([
                    'message' => 'Not allowed',
                    'status' => 401
                ]);
            }

            //image manual delete
            $images = ArticleImage::where('article_id',$request->article_id)->get();
            foreach($images as $image){
                Storage::delete('public/'.$image->name);
                $image->delete();
            }

            $this->model->find($request->article_id)->delete();
            return response()->json([
                'data' => null,
                'message' => 'Article has been deleted',
                'status' => 200
            ]);
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
