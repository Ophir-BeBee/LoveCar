<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentDeleteRequest;

class CommentController extends Controller
{

    protected $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    //create comment
    public function store(CommentCreateRequest $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ]);
        }

        //create comment
        $data = $this->changeCreateCommentDataToArray($request);
        $comment = $this->model->create($data);
        return response()->json([
            'comment' => $comment,
            'message' => "You've commented on this post",
            'status' => 200
        ]);
    }

    //delete comment
    public function destroy(CommentDeleteRequest $request){
        //check comment
        $comment = $this->model->find($request->comment_id);
        if(!$comment){
            return response()->json([
                'message' => 'Comment not found',
                'status' => 404
            ]);
        }

        //user authorization
        if(Gate::denies('auth-comment', $comment)){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        $comment->delete();
        return response()->json([
            'message' => 'You deleted this comment',
            'status' => 200
        ]);
    }

    //change comment create data to array
    private function changeCreateCommentDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id,
            'text' => $request->text
        ];
    }

}
