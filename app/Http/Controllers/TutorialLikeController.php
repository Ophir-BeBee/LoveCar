<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\TutorialLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorialLikeController extends Controller
{

    protected $model;

    public function __construct(TutorialLike $model)
    {
        $this->model = $model;
    }

    //toggle like
    public function toggle(Request $request){
        //check post
        $tutorial = Tutorial::find($request->tutorial_id);
        if(!$tutorial){
            return sendResponse(null,404,'tutorial not found');
        }

        //check liked or not
        $check = $this->model->where('tutorial_id',$request->tutorial_id)->where('user_id',Auth::user()->id)->first();
        if($check){
            //delete like
            $this->model->where('tutorial_id',$request->tutorial_id)->where('user_id',Auth::user()->id)->delete();
            //get post data to return
            $data = Tutorial::where('id',$request->tutorial_id)
            ->select('id')
            ->withCount('tutorial_likes')
            ->withCount(['tutorial_likes as is_liked' => function($query){
                $query->where('tutorial_likes.user_id',Auth::user()->id);
            }])
            ->first();
            return sendResponse($data,200,'You unliked this tutorial');
        }

         //create like
         $data = $this->model->create([
            'user_id' => Auth::user()->id,
            'tutorial_id' => $request->tutorial_id
        ]);

        $data = $this->model
        ->where('tutorial_id',$request->tutorial_id)
        ->select('id','tutorial_id')
        ->with(['tutorial' => function($query){
            $query->select('id')
            ->withCount('tutorial_likes')
            ->withCount(['tutorial_likes as is_liked' => function($query){
                $query->where('tutorial_likes.user_id',Auth::user()->id);
            }]);
        }])
        ->first();
        return sendResponse($data->tutorial,200,'You liked this tutorial');
    }

}
