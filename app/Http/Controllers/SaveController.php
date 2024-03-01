<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\SaveCreateRequest;
use App\Http\Requests\SaveDeleteRequest;

class SaveController extends Controller
{

    protected $model;

    public function __construct(Save $model)
    {
        $this->model = $model;
    }

    //get save posts
    public function index(){
        $saves = $this->model->with('post')->get();

        if(count($saves) == 0){
            return response()->json([
                'message' => 'You have no save post',
                'status' => 404
            ]);
        }

        return response()->json([
            'data' => $saves,
            'status' => 200
        ]);
    }

    //save post
    public function store(SaveCreateRequest $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ]);
        }

        $savePost = $this->model->where('user_id',Auth::user()->id)->where('post_id')->first();
        if($savePost){
            return response()->json([
                'message' => 'You already saved this post',
                'status' => 405
            ]);
        }

        //create save
        $data = $this->changeCreateSaveDataToArray($request);
        $data = $this->model->create($data);
        return response()->json([
            'data' => $data,
            'message' => 'You saved this post',
            'status' => 200
        ]);
    }

    //delete save post
    public function destroy(Request $request){
        //check saved or not
        $save = $this->model->find($request->save_id);
        if(!$save){
            return response()->json([
                'message' => 'You already unsaved this post',
                'status' => 405
            ]);
        }

        //user authorization
        if(Gate::denies('auth-unsave',$save)){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        $save->delete();
        return response()->json([
            'data' => null,
            'message' => 'You unsaved this post',
            'status' => 200
        ]);
    }


    //change create save data to array
    private function changeCreateSaveDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ];
    }

}
