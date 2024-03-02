<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use App\Models\PostImage;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    //get all posts
    public function index(){
        $data = $this->model
        ->with('post_images')
        ->withCount('post_likes')
        ->withCount(['post_likes as is_liked' => function($query){
            $query->where('post_likes.user_id',Auth::user()->id);
        }])
        ->withCount(['saves as is_saved' => function($query){
            $query->where('saves.user_id',Auth::user()->id);
        }])
        ->withCount('comments')
        ->orderBy('id','desc')
        ->get();
        return sendResponse(PostResource::collection($data),200);
    }

    //create posts
    public function store(PostRequest $request){
        //user authorization
        if(Gate::denies('auth-post')){
            return sendResponse(null,401,'Not allowed');
        }

        //create data of posts
        $data = $this->changePostCreateDataToArray($request);
        $post = $this->model->create($data);

        //check photos inclued or not
        if($request->file('image')){

            $imageFile = $request->file('image');

            //four images validation
            $imageCount = count($imageFile);
            if($imageCount>4){
                return sendResponse(null,405,"Can't upload more than 4 photos");
            }

            //store images
            for($i=0;$i<$imageCount;$i++){
                $imageName = uniqid() . '_' . time() . '.' . $imageFile[$i]->getClientOriginalExtension();
                $imageFile[$i]->storeAs('public',$imageName);
                PostImage::create([
                    'post_id' => $post->id,
                    'name' => $imageName
                ]);
            }
        }
        $data = $this->model
            ->where('id',$post->id)
            ->withCount('post_likes')
            ->withCount('comments')
            ->with(['comments' => function($query) {
                $query->with('user:id,name');
                $query->orderBy('id','desc');
            }])
            ->withCount(['saves as is_saved' => function($query){
                $query->where('saves.user_id',Auth::user()->id);
            }])
            ->with('post_images')
            ->first();
        return sendResponse(new PostResource($data),200);
    }

    //view posts
    public function view(Request $request){
        PostView::create([
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ]);
        return sendResponse(null,200,'You viewed this post');
    }

    //update posts
    public function update(PostRequest $request){

        //user authorization
        if(Gate::denies('auth-post')){
            return sendResponse(null,401,'Not allowed');
        }

        $post = $this->model->find($request->post_id);

        if(!$post){
            return sendResponse(null,404,'Post not found');
        }

        //update data
        $data = $this->changePostUpdateDataToArray($request);
        $post->update($data);

        //delete all images which belongs to this post from project folder and database
        $images_from_db = PostImage::where('post_id',$request->post_id)->get();

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
                PostImage::create([
                    'post_id' => $request->post_id,
                    'name' => $imageName
                ]);
            }

        }

        $data = $this->model
        ->where('id',$post->id)
        ->withCount('post_likes')
        ->withCount('comments')
        ->with(['comments' => function($query) {
            $query->with('user:id,name');
            $query->orderBy('id','desc');
        }])
        ->withCount(['saves as is_saved' => function($query){
            $query->where('saves.user_id',Auth::user()->id);
        }])
        ->with('post_images')
        ->first();
        return sendResponse(new PostResource($data),200);
    }

    //delete posts
    public function destroy(Request $request){

        // user authorization
        if(Gate::denies('auth-post')){
            return sendResponse(null,401,'Not allowed');
        }

        //image manual delete
        $images = PostImage::where('post_id',$request->post_id)->get();
        foreach($images as $image){
            Storage::delete('public/'.$image->name);
            $image->delete();
        }

        $this->model->find($request->post_id)->delete();
        return sendResponse(null,200,'Post has been deleted');
    }

    //change post data to array
    private function changePostUpdateDataToArray($request){
        return [
            'title' => $request->title,
            'description' => $request->description
        ];
    }

    //change post data to array
    private function changePostCreateDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'description' => $request->description
        ];
    }

}
