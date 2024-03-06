<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopServiceRequest;
use App\Models\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShopServiceController extends Controller
{

    protected $model;

    public function __construct(ShopService $model)
    {
        $this->model = $model;
    }

    //get all services
    public function index(){
        $services = $this->model->get();
        return sendResponse($services,200);
    }

    //create shop service
    public function store(ShopServiceRequest $request){
        //user authorization
        if(Gate::denies('auth-shop-service')){
            return sendResponse(null,401,'Not allowed');
        }

        //check service
        $service = $this->model->where('name',$request->name)->first();
        if($service){
            return sendResponse(null,405,'Service already exist');
        }

        //create service
        $service = $this->model->create(['name' => $request->name]);
        return sendResponse($service,200,'Service create success');
    }

    //update shop services
    public function update(ShopServiceRequest $request){
         //user authorization
         if(Gate::denies('auth-shop-service')){
            return sendResponse(null,401,'Not allowed');
        }

        //check category
        $service = $this->model->find($request->service_id);
        if(!$service){
            return sendResponse(null,405,'Service not found');
        }

        //check name available or not
        $check = $this->model->where('name',$request->name)->first();
        if($check){
            return sendResponse(null,405,'Service already exist');
        }

        $service->update(['name'=>$request->name]);
        return sendResponse($service,200,'Service update success');
    }

    //delete service
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-shop-service')){
            return sendResponse(null,401,'Not allowed');
        }

        //check category
        $service = $this->model->find($request->service_id);
        if(!$service){
            return sendResponse(null,404,'Service not found');
        }

        //delete service
        $service->delete();
        return sendResponse(null,200,'Service deleted success');
    }
}
