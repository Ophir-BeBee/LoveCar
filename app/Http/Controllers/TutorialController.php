<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\TutorialStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\TutorialRequest;
use App\Http\Resources\TutorialResource;
use Illuminate\Support\Facades\Storage;

class TutorialController extends Controller
{

    protected $model;

    public function __construct(Tutorial $model)
    {
        $this->model = $model;
    }

    //get all tutorials
    public function index(){
        $data = $this->model
        ->with('tutorial_steps:id,tutorial_id,step_title,step_description,step_image')
        ->withCount('tutorial_likes')
        ->withCount(['tutorial_likes as is_liked' => function($query){
            $query->where('tutorial_likes.user_id',Auth::user()->id);
        }])
        ->get();
        return sendResponse(TutorialResource::collection($data),200);
    }

    //create tutorial
    public function store(TutorialRequest $request){
        //user authorization
        if(Gate::denies('auth-tutorial')){
            return sendResponse(null,401,'Not allowed');
        }

        //create tutorial
        $tutorial = $this->model->create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'description' => $request->description
        ]);

        //check image
        if($request->file('image')){

            $imageFile = $request->file('image');

            $imageCount = count($imageFile);

            if($imageCount>1){
                $tutorial->delete();
                return sendResponse(null,405,"Can upload only one photo in tutorial");
            }

            //store in project folder
            $imageName = uniqid() . '_' . time() . '.' . $imageFile[0]->getClientOriginalExtension();
            $imageFile[0]->storeAs('public',$imageName);
            //store in database
            $tutorial->image = $imageName;
            $tutorial->save();
        }

        //create steps
        $tutorial_steps = $request->steps;
        foreach($tutorial_steps as $tutorial_step){
            if(count($tutorial_step['image'])>1){
                $tutorial->delete();
                return sendResponse(null,405,"Can upload only one photo in tutorial step");
            }
            $imageFile = $tutorial_step['image'];
            $imageName = uniqid() . '_' . time() . '.' . $imageFile[0]->getClientOriginalExtension();
            //store step images in project folder
            $imageFile[0]->storeAs('public',$imageName);
            TutorialStep::create([
                'tutorial_id' => $tutorial->id,
                'step_title' => $tutorial_step['title'],
                'step_description' => $tutorial_step['description'],
                'step_image' => $imageName,
            ]);
        }

        $data = $this->model
        ->where('id',$tutorial->id)
        ->with('tutorial_steps:id,tutorial_id,step_title,step_description,step_image')
        ->withCount('tutorial_likes')
        ->withCount(['tutorial_likes as is_liked' => function($query){
            $query->where('tutorial_likes.user_id',Auth::user()->id);
        }])
        ->first();
        return sendResponse(new TutorialResource($data),200,'Tutorial creation success');
    }

    //update tutorial
    public function update(TutorialRequest $request){
        //check tutorial
        $tutorial = $this->model->find($request->tutorial_id);
        if(!$tutorial){
            return sendResponse(null,404,'Tutorial not found');
        }

        //user authorization
        if(Gate::denies('auth-tutorial')){
            return sendResponse(null,401,'Not allowed');
        }

        //update tutorial
        $tutorial->title = $request->title;
        $tutorial->description = $request->description;

        //delete old tutorial image from project folder
        Storage::delete('storage/'.$tutorial->image);

        if($request->file('image')){

            $imageFile = $request->file('image');

            $imageCount = count($imageFile);

            if($imageCount>1){
                return sendResponse(null,405,"Can upload only one photo in tutorial");
            }

            //store in project folder
            $imageName = uniqid() . '_' . time() . '.' . $imageFile[0]->getClientOriginalExtension();
            $imageFile[0]->storeAs('public',$imageName);
            //store in database
            $tutorial->image = $imageName;
            $tutorial->save();
        }

        //delete old tutorial steps
        TutorialStep::where('tutorial_id',$tutorial->id)->delete();

        //store new tutorial steps
        $tutorial_steps = $request->steps;
        foreach($tutorial_steps as $tutorial_step){
            if(count($tutorial_step['image'])>1){
                return sendResponse(null,405,"Can upload only one photo in tutorial step");
            }
            $imageFile = $tutorial_step['image'];
            $imageName = uniqid() . '_' . time() . '.' . $imageFile[0]->getClientOriginalExtension();
            //store step images in project folder
            $imageFile[0]->storeAs('public',$imageName);
            TutorialStep::create([
                'tutorial_id' => $tutorial->id,
                'step_title' => $tutorial_step['title'],
                'step_description' => $tutorial_step['description'],
                'step_image' => $imageName,
            ]);
        }
        $data = $this->model
        ->where('id',$tutorial->id)
        ->with('tutorial_steps:id,tutorial_id,step_title,step_description,step_image')
        ->withCount('tutorial_likes')
        ->withCount(['tutorial_likes as is_liked' => function($query){
            $query->where('tutorial_likes.user_id',Auth::user()->id);
        }])
        ->first();
        return sendResponse(new TutorialResource($data),200,'Tutorial updated success');
    }

    //delete tutorial
    public function destroy(Request $request){
        //check tutorial
        $tutorial = $this->model->find($request->tutorial_id);
        if(!$tutorial){
            return sendResponse(null,404,'Tutorial not found');
        }

        //user authorization
        if(Gate::denies('auth-tutorial')){
            return sendResponse(null,401,'Not allowed');
        }

        TutorialStep::where('tutorial_id',$tutorial->id)->delete();
        $tutorial->delete();
        return sendResponse(null,200,'Tutorial deleted success');
    }
}
