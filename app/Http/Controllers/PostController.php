<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use App\Models\PostImage;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{

    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    //get all posts
    public function index(){
        return response()->json([
            'data' => $this->model
            ->with('post_images')
            ->withCount('post_likes')
            ->withCount('comments')
            ->orderBy('id','desc')
            ->get(),
            'status' => 200
        ]);
    }

    //post show
    public function show(Request $request){
        //get post data
        $post = $this->model
        ->where('id',$request->id)
        ->withCount('post_likes')
        ->withCount('comments')
        ->with(['comments' => function($query) {
            $query->with('user:id,name');
            $query->orderBy('id','desc');
        }])
        ->with('post_images')
        ->first();

        //increase view
        PostView::create([
            'user_id' => Auth::user()->id,
            'post_id' => $request->id
        ]);
        return response()->json([
            'data' => $post,
            'status' => 200
        ]);
    }

    //create posts
    public function store(PostRequest $request){
        //user authorization
        if(Gate::denies('auth-post')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
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
                return response()->json([
                    'message' => "Can't upload more than 4 photos",
                    'status' => 405
                ]);
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

        return response()->json([
            'data' => $this->model
            ->where('id',$post->id)
            ->withCount('post_likes')
            ->withCount('comments')
            ->with(['comments' => function($query) {
                $query->with('user:id,name');
                $query->orderBy('id','desc');
            }])
            ->first(),
            'message' => 'Post has been created',
            'status' => 200
        ]);
    }

    //update posts
    public function update(PostRequest $request){

        //user authorization
        if(Gate::denies('auth-post')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        $post = $this->model->find($request->post_id);

        if(!$post){
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ]);
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
                return response()->json([
                    'message' => "Can't upload more than 4 photos",
                    'status' => 405
                ]);
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

        return response()->json([
            'data' => $this->model
            ->where('id',$post->id)
            ->withCount('post_likes')
            ->withCount('comments')
            ->with(['comments' => function($query) {
                $query->with('user:id,name');
                $query->orderBy('id','desc');
            }])
            ->first(),
            'message' => 'Post has beeen updated',
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
        $images = PostImage::where('post_id',$request->post_id)->get();
        foreach($images as $image){
            Storage::delete('public/'.$image->name);
            $image->delete();
        }

        $this->model->find($request->post_id)->delete();
        return response()->json([
            'data' => null,
            'message' => 'Post has been deleted',
            'status' => 200
        ]);
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
