<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{

    protected $model;

    public function __construct(PostLike $model)
    {
        $this->model  = $model;
    }

    //give like
    public function store(Request $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ]);
        }

        //check liked or not
        $check = $this->model->where('post_id',$request->post_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            return response()->json([
                'message' => 'You already liked this post',
                'status' => 405
            ]);
        }

        //create like
        $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ]);

        return response()->json([
            'data' => $data,
            'message' => 'You liked this post',
            'status' => 200
        ]);

    }

    //ungive like
    public function destroy(Request $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return response()->json([
                'message'=> 'Post not found',
                'status' => 404
            ]);
        }

        //delete like
        $this->model->where('post_id',$request->post_id)->where('user_id',Auth::user()->id)->delete();
        return response()->json([
            'data' => null,
            'message' => 'You unliked this post',
            'status' => 200
        ]);
    }

}
