<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\FuelCost;
use Illuminate\Http\Request;
use App\Http\Requests\FuelCostRequest;
use Illuminate\Support\Facades\Gate;

class FuelCostController extends Controller
{

    protected $model;

    public function __construct(FuelCost $model)
    {
        $this->model = $model;
    }

    //get all fuel costs of car
    public function index($carId,$month,$year){
        //check car
        $car = Car::find($carId);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //get data
        $data = $this->model
        ->where('car_id',$car->id)
        ->whereYear('created_at',$year)
        ->whereMonth('created_at',$month)
        ->get();
        if(count($data)==0){
            return sendResponse(null,404,'No history this month');
        }
        return sendResponse($data,200);
    }

    //store
    public function store(FuelCostRequest $request){
        //check car
        $car = Car::find($request->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-car-fuel_cost',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //update mileage to car
        $car->mileage = $request->mileage;
        $car->save();

        //insert history
        $data = $this->changeFuelCostDataToArray($request,'create');
        $data = $this->model->create($data);
        return sendResponse($data,200,'Fuel cost creation success');
    }

    //update
    public function update(FuelCostRequest $request){
        //check fuel cost
        $fuel_cost = $this->model->find($request->id);
        if(!$fuel_cost){
            return sendResponse(null,404,'Not found');
        }

        //get car
        $car = Car::find($fuel_cost->car_id);

        //user authorization
        if(Gate::denies('auth-car-fuel_cost',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //update mileage to car
        $car->mileage = $request->mileage;
        $car->save();

        //update data
        $data = $this->changeFuelCostDataToArray($request,'update');
        $fuel_cost->update($data);
        return sendResponse($fuel_cost,200,'Fuel cost update success');
    }

    //delete
    public function destroy(Request $request){
        //check fuel cost
        $fuel_cost = $this->model->find($request->id);
        if(!$fuel_cost){
            return sendResponse(null,404,'Not found');
        }

        $car = Car::find($fuel_cost->car_id);
        if(!$car){
            $fuel_cost->delete();
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-car-fuel_cost',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //delete data
        $fuel_cost->delete();
        return sendResponse(null,200,'Fuel cost history delete success');
    }

    //change fuel cost data to array
    private function changeFuelCostDataToArray($request,$type){
        $response = [
            'date' => $request->date,
            'price' => $request->price,
            'liter' => $request->liter,
            'cost' => $request->cost,
            'mileage' => $request->mileage,
            'fuel_type' => $request->fuel_type,
            'station_name' => $request->station_name,
            'city' => $request->city
        ];
        if($type=='create') $response['car_id'] = $request->car_id;
        return $response;
    }
}
