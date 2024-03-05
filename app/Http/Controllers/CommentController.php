<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CommentRequest;

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
            return sendResponse(404,'Post not found');
        }

        //create comment
        $data = $this->changeCreateCommentDataToArray($request);
        $comment = $this->model->create($data);
        $data = $this->model->where('id',$comment->id)->with('user:id,name')->first();
        return sendResponse($data,200,"You've commented on this post");
    }

    //delete comment
    public function destroy(Request $request){
        //get comment
        $comment = $this->model->find($request->comment_id);

        //user authorization
        if(Gate::denies('auth-comment', $comment)){
            return sendResponse(401,'Not allowed');
        }

        $comment->delete();
        return sendResponse(200,'You deleted this comment');
    }

    //update comment
    public function update(CommentRequest $request){
        //get comment
        $comment = $this->model->find($request->comment_id);

        //check comment
        if(!$comment){
            return sendResponse(404,'Comment not found');
        }

        //update comment
        $comment->update([
            'text' => $request->text
        ]);
        $data = $this->model->where('id',$comment->id)->with('user:id,name')->first();
        return sendResponse($data,200,'Comment has been updated');
    }

    //show comment
    public function show($post_id){
        $data = $this->model->where('post_id',$post_id)->with('user:id,name')->orderBy('id','desc')->get();
        return sendResponse($data,200);
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
