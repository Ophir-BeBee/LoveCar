<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaveResource;
use App\Models\Post;
use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveController extends Controller
{

    protected $model;

    public function __construct(Save $model)
    {
        $this->model = $model;
    }

    //get save posts
    public function index(){
        $saves = $this->model
        ->with(['post' => function($query){
            $query->withCount(['saves as is_saved' => function($query){
                $query->where('saves.user_id',Auth::user()->id);
            }]);
        }])
        ->orderBy('id','desc')
        ->get();

        if(count($saves) == 0){
            return sendResponse($saves,404,'You have no save post');
        }

        return sendResponse(SaveResource::collection($saves),200);
    }

    //toggle
    public function toggle(Request $request){
        //check post
        $post = Post::find($request->post_id);
        if(!$post){
            return sendResponse(null,404,'Post not found');
        }

        //check saved or not
        $save = $this->model
        ->where('post_id',$request->post_id)
        ->where('user_id',Auth::user()->id)
        ->with('post')
        ->first();

        //check saved or not
        if($save){
            $save->post->is_saved = 0;
            $saveData = $save;
            $save->delete();
            return sendResponse(new SaveResource($saveData),200,'You unsaved this post');
        }

        //create save
        $data = $this->changeCreateSaveDataToArray($request);
        $data = $this->model->create($data);
        $data = $this->model->where('id',$data->id)->with(['post' => function($query){
            $query->select('id','title','description');
            $query->with('post_images');
        }])->first();
        $data->post->is_saved = 1;
        return sendResponse(new SaveResource($data),200,'You saved this post');
    }

    //change create save data to array
    private function changeCreateSaveDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id
        ];
    }

}
