<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\RentalCost;
use Illuminate\Http\Request;
use App\Http\Requests\RentalCostRequest;
use Illuminate\Support\Facades\Gate;

class RentalCostController extends Controller
{

    protected $model;

    public function __construct(RentalCost $model)
    {
        $this->model = $model;
    }

    //get rental cost
    public function index($carId,$month,$year){
        $data = $this->model
        ->where('car_id',$carId)
        ->whereYear('created_at',$year)
        ->whereMonth('created_at',$month)
        ->get();
        if(count($data)==0){
            return sendResponse($data,404,"There is no rental cost in this month");
        }
        return sendResponse($data,200);
    }

    //create rental cost
    public function store(RentalCostRequest $request){
        //check car
        $car = Car::find($request->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        $rental_cost = $this->model->create([
            'car_id' => $request->car_id,
            'date' => $request->date,
            'descrption' => $request->description ? $request->description : null,
            'cost' => $request->cost
        ]);
        $data = $this->model->find($rental_cost->id);
        return sendResponse($data,200,'Rental cost created success');
    }

    //update rental cost
    public function update(RentalCostRequest $request){
        //check rental cost
        $rental_cost = $this->model->find($request->id);
        if(!$rental_cost){
            return sendResponse(null,404,'Rental cost not found');
        }

        //check car
        $car = Car::find($rental_cost->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-rental_cost-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $rental_cost->date = $request->date;
        $rental_cost->description = $request->description ? $request->description : null;
        $rental_cost->cost = $request->cost;
        $rental_cost->save();
        return sendResponse($rental_cost,200,'Rental cost updated success');
    }

    //delete rental cost
    public function destroy(Request $request){
        //check rental cost
        $rental_cost = $this->model->find($request->id);
        if(!$rental_cost){
            return sendResponse(null,405,'You already deletd this rental cost');
        }

        //check car
        $car = Car::find($rental_cost->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-rental_cost-delete',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $rental_cost->delete();
        return sendResponse(null,200,'Rental cost has been deleted');
    }
}
