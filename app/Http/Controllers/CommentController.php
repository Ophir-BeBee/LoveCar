<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentDeleteRequest;
use App\Http\Requests\CommentUpdateRequest;

class CommentController extends Controller
{

    protected $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    //create comment
    public function store(CommentRequest $request){
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
            'data' => $comment,
            'message' => "You've commented on this post",
            'status' => 200
        ]);
    }

    //delete comment
    public function destroy(Request $request){
        //get comment
        $comment = $this->model->find($request->comment_id);

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

    //update comment
    public function update(CommentRequest $request){
        //get comment
        $comment = $this->model->find($request->comment_id);

        //check comment
        if(!$comment){
            return response()->json([
                'message' => 'Comment not found',
                'status' => 404
            ]);
        }

        //update comment
        $comment = $comment->update([
            'text' => $request->text
        ]);
        return response()->json([
            'data' => $comment,
            'message' => 'Comment has been updated',
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
