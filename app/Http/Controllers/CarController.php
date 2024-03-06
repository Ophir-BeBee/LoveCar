<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarBrand;
use Illuminate\Http\Request;
use App\Http\Requests\CarRequest;
use App\Http\Resources\AllCarsResource;
use App\Http\Resources\CarResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CarController extends Controller
{

    protected $model;

    public function __construct(Car $model)
    {
        $this->model = $model;
    }

    //get my cars
    public function index(){
        $cars = $this->model
        ->where('user_id',Auth::user()->id)
        ->select('id','user_id','brand_id','model_id','plate_no','mileage')
        ->with('brand:id,name')
        ->with('model:id,brand_id,name,image')
        ->get();
        return sendResponse(CarResource::collection($cars),200);
    }

    //all cars
    public function all(){
        $cars = CarBrand::select('id','name')->with('car_models:id,brand_id,name,image')->get();
        return sendResponse(AllCarsResource::collection($cars),200);
    }

    //store car
    public function store(CarRequest $request){
        $data = $this->changeCarDataToArray($request,'create');
        $car = $this->model->create($data);
        $data = $this->model
        ->where('id',$car->id)
        ->select('id','user_id','brand_id','model_id','plate_no','mileage')
        ->with('brand:id,name')
        ->with('model:id,brand_id,name,image')
        ->first();
        return sendResponse(new CarResource($data),200,'Car has been created');
    }

    //update car
    public function update(CarRequest $request){
        //check car
        $car = $this->model->find($request->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-car-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $data = $this->changeCarDataToArray($request,'update');
        $car->update($data);
        $data = $this->model
        ->where('id',$car->id)
        ->select('id','user_id','brand_id','model_id','plate_no','mileage')
        ->with('brand:id,name')
        ->with('model:id,brand_id,name,image')
        ->first();
        return sendResponse(new CarResource($data),200,'Car has been updated');
    }

    //delete car
    public function destroy(Request $request){
        //check car
        $car = $this->model->find($request->car_id);
        if(!$car){
            return sendResponse(null,405,'Car already deleted');
        }

        //user authorization
        if(Gate::denies('auth-car-delete',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $car->delete();
        return sendResponse(null,200,'Car has been deleted');
    }

    //change car data to array
    private function changeCarDataToArray($request,$type){
        $response = [
            'brand_id' => $request->brand_id,
            'model_id' => $request->model_id,
            'usage' => $request->usage,
            'mileage' => $request->mileage,
            'plate_no' => $request->plate_no,
            'fuel_type' => $request->fuel_type,
            'color' => $request->color
        ];
        if($type == 'create') $response['user_id'] = Auth::user()->id;
        return $response;
    }
}
