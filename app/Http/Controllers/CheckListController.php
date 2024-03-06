<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CheckList;
use Illuminate\Http\Request;
use App\Http\Requests\CheckListRequest;
use App\Http\Resources\CheckListResource;
use App\Models\CheckListCategory;
use Illuminate\Support\Facades\Gate;

class CheckListController extends Controller
{

    protected $model;

    public function __construct(CheckList $model)
    {
        $this->model = $model;
    }

    //get all check lists
    public function index($carId){
        $checkLists = CheckListCategory::with('check_lists')
        ->get();
        return sendResponse(CheckListResource::collection($checkLists),200);
    }

    //store check lists
    public function store(CheckListRequest $request){
        //check car
        $car = Car::find($request->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //check category
        $category = CheckListCategory::where('name',$request->category)->first();
        if(!$category){
            $category = CheckListCategory::create(['name' => $request->category]);
        }

        //assign array
        $parts = $request->parts;
        foreach($parts as $part){
            $this->model->create([
                'car_id' => $request->car_id,
                'category_id' => $category->id,
                'part' => $part
            ]);
        }

        $data = CheckListCategory::where('id',$category->id)
        ->select('id','name')
        ->with('check_lists')
        ->get();
        return sendResponse(CheckListResource::collection($data),200,'Check list created success');
    }

    //update check list
    public function update(CheckListRequest $request){
        //check category
        $category = CheckListCategory::find($request->category_id);
        if(!$category){
            return sendResponse(null,404,'Category not found');
        }

        //check checklist
        $checkLists = $this->model->where('category_id',$category->id)->get();
        if(count($checkLists)==0){
            return sendResponse(null,404,'Check lists not found');
        }

        $car = Car::find($checkLists[0]->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-checkList-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //categroy name update
        $category->name = $request->category;
        $category->save();

        //delete parts
        $checkLists = $this->model->where('category_id',$category->id)->delete();
        //assign array
        $parts = $request->parts;
        foreach($parts as $part){
            $this->model->create([
                'car_id' => $car->id,
                'category_id' => $category->id,
                'part' => $part
            ]);
        }

        $data = CheckListCategory::where('id',$category->id)
        ->select('id','name')
        ->with('check_lists')
        ->first();
        return sendResponse(new CheckListResource($data),200,'Check list updated success');
    }

    //change condition
    public function checked(Request $request){
        //check check list
        $checkList = $this->model->find($request->id);
        if(!$checkList){
            return sendResponse(null,404,'Check list not found');
        }

        //update condition
        $checkList->condition = $request->condition;
        $checkList->save();

        $data = CheckListCategory::where('id',$checkList->category_id)
        ->select('id','name')
        ->with('check_lists')
        ->first();
        return sendResponse(new CheckListResource($data),200,'You checked this part');
    }

    //delete check lists
    public function destroy(Request $request){
        //check category
        $category = CheckListCategory::find($request->category_id);
        if(!$category){
            return sendResponse(null,404,'Category not found');
        }

        //check checklist
        $checkLists = $this->model->where('category_id',$category->id)->get();

        $car = Car::find($checkLists[0]->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-checkList-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $this->model->where('category_id',$category->id)->delete();
        $category->delete();
        return sendResponse(null,200,'Check list deleted success');
    }

}
