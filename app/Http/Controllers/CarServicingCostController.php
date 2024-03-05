<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarServicingCost;
use App\Http\Requests\CarServicingCostRequest;
use App\Models\CarService;
use Illuminate\Support\Facades\Gate;

class CarServicingCostController extends Controller
{

    protected $model;

    public function __construct(CarServicingCost $model)
    {
        $this->model = $model;
    }

    //get servicing costs
    public function index($carId,$year){
        $data = $this->model
        ->where('car_id',$carId)
        ->whereYear('created_at',$year)
        ->with('car_services:id,car_servicing_cost_id,type,particular,price,quantity,amount,condition,brand,model,guarantee_value,guarantee_type,start_date,end_date')
        ->get();
        if(count($data)==0){
            return sendResponse(null,404,'No servicing cost history in this year');
        }
        return sendResponse($data,200);
    }

    //store
    public function store(CarServicingCostRequest $request){
        //check car
        $car = Car::find($request->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //insert service shop data
        $data = $this->changeServiceShopDataToArray($request,'create');
        $servicing_cost = $this->model->create($data);

        //assign services to array
        $services = $request->services;

        //insert services
        $total_amount = 0;
        foreach($services as $service){
            $total_amount += $service['amount'];
            $data = $this->changeServicesDataToArray($service,$servicing_cost->id);
            CarService::create($data);
        }

        //update total amount to cost
        $servicing_cost->total_amount = $total_amount;
        $servicing_cost->save();

        $data = $this->model
        ->where('id',$servicing_cost->id)
        ->with('car_services:id,car_servicing_cost_id,type,particular,price,quantity,amount,condition,brand,model,guarantee_value,guarantee_type,start_date,end_date')
        ->first();
        return sendResponse($data,200,'Servicing cost creation success');
    }

    //update
    public function update(CarServicingCostRequest $request){
        //check servicing cost
        $servicing_cost = $this->model->find($request->id);
        if(!$servicing_cost){
            return sendResponse(null,404,'Servicing cost not found');
        }

        //check car
        $car = Car::find($servicing_cost->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-car-servicing_cost-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //update service shop
        $data = $this->changeServiceShopDataToArray($request,'update');
        $servicing_cost->update($data);

        //assign services to array
        $services = $request->services;

        //insert services
        $total_amount = 0;
        foreach($services as $service){
            $total_amount += $service['amount'];
            $data = $this->changeServicesDataToArray($service,$servicing_cost->id);
            CarService::where('car_servicing_cost_id',$request->id)->update($data);
        }

        //update total amount to cost
        $servicing_cost->total_amount = $total_amount;
        $servicing_cost->save();

        $data = $this->model
        ->where('id',$servicing_cost->id)
        ->with('car_services:id,car_servicing_cost_id,type,particular,price,quantity,amount,condition,brand,model,guarantee_value,guarantee_type,start_date,end_date')
        ->first();
        return sendResponse($data,200,'Servicing cost update success');
    }

    //delete
    public function destroy(Request $request){
        //check servicign cost
        $servicing_cost = $this->model->find($request->id);
        if(!$servicing_cost){
            return sendResponse(null,404,'Servicing cost not found');
        }

        //check car
        $car = Car::find($servicing_cost->car_id);
        if(!$car){
            CarService::where('car_servicing_cost_id',$servicing_cost->id)->delete();
            $servicing_cost->delete();
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-car-servicing_cost-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        CarService::where('car_servicing_cost_id',$servicing_cost->id)->delete();
        $servicing_cost->delete();
        return sendResponse(null,200,'Servicing cost history deleted success');
    }

    //change services data to array
    private function changeServicesDataToArray($service,$id){
        $response = [
            'car_servicing_cost_id' => $id,
            'type' => $service['type'],
            'particular' => $service['particular'],
            'price' => $service['price'],
            'quantity' => $service['quantity'],
            'amount' => $service['amount']
        ];
        if($service['type']!='services'){
            $response['condition'] = $service['condition'];
            $response['brand'] = $service['brand'];
            $response['model'] = $service['model'];
            $response['guarantee_value'] = $service['guarantee_value'];
            $response['guarantee_type'] = $service['guarantee_type'];
            $response['start_date'] = $service['start_date'];
            $response['end_date'] = $service['end_date'];
        }
        return $response;
    }

    //change service shop data to array
    private function changeServiceShopDataToArray($request,$type){
        $response = [
            'date' => $request->date,
            'shop_name' => $request->shop_name,
            'shop_phone' => $request->shop_phone,
            'shop_address' => $request->shop_address
        ];
        if($type=='create') $response['car_id'] = $request->car_id;
        return $response;
    }
}
