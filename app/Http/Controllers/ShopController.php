<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopService;
use Illuminate\Http\Request;
use App\Models\ShopToService;
use App\Models\ShopToCategory;
use App\Models\ShopToServices;
use App\Models\ShopServiceItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ShopCreateRequest;

class ShopController extends Controller
{

    protected $model;

    public function __construct(Shop $model)
    {
        $this->model = $model;
    }

    //get all shops
    public function index(){
        return response()->json([
            'data' => $this->model
            ->with(['shop_to_categories' => function($query){
                $query->with('shop_category:id,name');
            }])
            ->with(['shop_to_services' => function($query){
                $query->with(['shop_service' => function($query){
                    $query->with('shop_service_items');
                }]);
            }])
            ->get(),
            'status' => 200
        ]);
    }

    //create shops
    public function store(ShopCreateRequest $request){
        //user authorization
        if(Gate::denies('auth-shop')){
            return response()->json([
                'message' => "Not allowed",
                'status' => 401
            ]);
        }

        //assign array data\
        $shop_categories = $request->shop_categories;
        $shop_services = $request->shop_services;

        //create shop
        $data = $this->changeShopCreateDataToArray($request);
        $shop = $this->model->create($data);

        //insert shop categories
        $response_categories = array();
        foreach($shop_categories as $shop_category){
            array_push($response_categories,ShopToCategory::create([
                'shop_id' => $shop->id,
                'shop_category_id' => $shop_category
            ]));
        }

        //insert shop services and items
        $response_services = array();
        foreach($shop_services as $shop_service){

            //insert services
            array_push($response_services,ShopToService::create([
                'shop_id' => $shop->id,
                'shop_service_id' => $shop_service['shop_service_id']
            ]));

            $response_services['items'] = array();

            //insert items which are belong to service
            $items = $shop_service['items'];
            foreach($items as $item){
                array_push($response_services['items'],ShopServiceItem::create([
                    'shop_id' => $shop->id,
                    'shop_service_id' => $shop_service['shop_service_id'],
                    'name' => $item
                ]));
            }

        }

        return response()->json([
            'message' => 'Shop Creation success',
            'status' => 200
        ]);

    }

    //change shop create data to array
    private function changeShopCreateDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];
    }
}
