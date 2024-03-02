<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
            return sendResponse(null,404,'You have no save post');
        }

        return sendResponse($saves,200);
    }

    //save post
    public function store(Request $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return sendResponse(null,404,'Post not found');
        }

        $savePost = $this->model->where('user_id',Auth::user()->id)->where('post_id')->first();
        if($savePost){
            return sendResponse(null,405,'You already saved this post');
        }

        //create save
        $data = $this->changeCreateSaveDataToArray($request);
        $data = $this->model->create($data);
        return sendResponse($data,200,'You saved this post');
    }

    //delete save post
    public function destroy(Request $request){
        //check saved or not
        $save = $this->model->find($request->save_id);
        if(!$save){
            return sendResponse(null,405,'You already unsaved this post');
        }

        //user authorization
        if(Gate::denies('auth-unsave',$save)){
            return sendResponse(null,401,'Not allowed');
        }

        $save->delete();
        return sendResponse(null,200,'You unsaved this post');
    }


    //change create save data to array
    private function changeCreateSaveDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ];
    }

}
