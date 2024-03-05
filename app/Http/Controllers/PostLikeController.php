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

    //toggle like
    public function toggle(Request $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return sendResponse(404,'Post not found');
        }

        //check liked or not
        $check = $this->model->where('post_id',$request->post_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            //delete like
            $this->model->where('post_id',$request->post_id)->where('user_id',Auth::user()->id)->delete();
            //get post data to return
            $data = Post::where('id',$request->post_id)
            ->select('id')
            ->withCount('post_likes')
            ->withCount(['post_likes as is_liked' => function($query){
                $query->where('post_likes.user_id',Auth::user()->id);
            }])
            ->withCount('comments')
            ->first();
            return sendResponse($data,200,'You unliked this post');
        }

         //create like
         $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ]);

        $data = $this->model
        ->where('post_id',$request->post_id)
        ->select('id','post_id')
        ->with(['post' => function($query){
            $query->select('id')
            ->withCount('post_likes')
            ->withCount('comments')
            ->withCount(['post_likes as is_liked' => function($query){
                $query->where('post_likes.user_id',Auth::user()->id);
            }]);
        }])
        ->first();
        return sendResponse($data->post,200,'You liked this post');
    }

}
