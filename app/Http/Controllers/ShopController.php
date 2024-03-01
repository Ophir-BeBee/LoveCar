<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopImage;
use App\Models\ShopService;
use Illuminate\Http\Request;
use App\Models\ShopToService;
use App\Models\ShopToCategory;
use App\Models\ShopToServices;
use App\Models\ShopServiceItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ShopCreateRequest;
use App\Http\Requests\ShopUpdateRequest;

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
                $query->select('id','shop_id','shop_category_id');
                $query->with('shop_category:id,name');
            }])
            ->with(['shop_to_services' => function($query){
                $query->select('id','shop_id','shop_service_id');
                $query->with(['shop_service' => function($query){
                    $query->select('id','name');
                    $query->with(['shop_service_items' => function($query){
                        $query->select('id','shop_id','shop_service_id','name');
                    }]);
                }]);
            }])
            ->with('shop_images:id,shop_id,name')
            ->get(),
            'status' => 200
        ]);
    }

    //shop show
    public function show(Request $request){
        return response()->json([
            'shop' => $this->model
            ->where('id',$request->id)
            ->with(['shop_to_categories' => function($query){
                $query->select('id','shop_id','shop_category_id');
                $query->with('shop_category:id,name');
            }])
            ->with(['shop_to_services' => function($query){
                $query->select('id','shop_id','shop_service_id');
                $query->with(['shop_service' => function($query){
                    $query->select('id','name');
                    $query->with(['shop_service_items' => function($query){
                        $query->select('id','shop_id','shop_service_id','name');
                    }]);
                }]);
            }])
            ->with('shop_images:id,shop_id,name')
            ->first(),
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

        //assign array data
        $shop_categories = $request->shop_categories;
        $shop_services = $request->shop_services;

        //create shop
        $data = $this->changeShopCreateDataToArray($request);
        $shop = $this->model->create($data);

        //check shop logo
        if($request->file('logo')){
            $logoImageFile = $request->file('logo');
            $logoImageName = uniqid() . '_' . time() . '.' . $logoImageFile[0]->getClientOriginalExtension();
            $logoImageFile[0]->storeAs('public',$logoImageName);
            $shop->logo = $logoImageName;
            $shop->save();
        }else{
            $shop->update(['logo'=>null]);
        }

        //insert shop categories
        foreach($shop_categories as $shop_category){
            ShopToCategory::create([
                'shop_id' => $shop->id,
                'shop_category_id' => $shop_category
            ]);
        }

        //insert shop services and items
        foreach($shop_services as $shop_service){

            //insert services
            ShopToService::create([
                'shop_id' => $shop->id,
                'shop_service_id' => $shop_service['shop_service_id']
            ]);

            //insert items which are belong to service
            $items = $shop_service['items'];
            foreach($items as $item){
                ShopServiceItem::create([
                    'shop_id' => $shop->id,
                    'shop_service_id' => $shop_service['shop_service_id'],
                    'name' => $item
                ]);
            }

        }

        //insert images section
        if($request->file('image')){

            $imageFile = $request->file('image');

            //four images validation
            $imageCount = count($imageFile);
            if($imageCount>5){
                return response()->json([
                    'message' => "Can't upload more than 5 photos",
                    'status' => 405
                ]);
            }

            //store images
            for($i=0;$i<$imageCount;$i++){
                $imageName = uniqid() . '_' . time() . '.' . $imageFile[$i]->getClientOriginalExtension();
                $imageFile[$i]->storeAs('public',$imageName);
                ShopImage::create([
                    'shop_id' => $shop->id,
                    'name' => $imageName
                ]);
            }
        }

        return response()->json([
            'shop' => $this->model
            ->where('id',$shop->id)
            ->with(['shop_to_categories' => function($query){
                $query->select('id','shop_id','shop_category_id');
                $query->with('shop_category:id,name');
            }])
            ->with(['shop_to_services' => function($query){
                $query->select('id','shop_id','shop_service_id');
                $query->with(['shop_service' => function($query){
                    $query->select('id','name');
                    $query->with(['shop_service_items' => function($query){
                        $query->select('id','shop_id','shop_service_id','name');
                    }]);
                }]);
            }])
            ->with('shop_images:id,shop_id,name')
            ->first(),
            'message' => 'Shop Creation success',
            'status' => 200
        ]);

    }

    //update shop
    public function update(ShopUpdateRequest $request){
        //user authorization
        if(Gate::denies('auth-shop')){
            return response()->json([
                'message' => "Not allowed",
                'status' => 401
            ]);
        }

        $shop = $this->model->find($request->shop_id);
        if(!$shop){
            return response()->json([
                'message' => 'Shop not found',
                'status' => 404
            ]);
        }

        //logo check and delete
        if($shop->logo){
            Storage::delete('public/'.$shop->logo);
        }

        //assign array data
        $shop_categories = $request->shop_categories;
        $shop_services = $request->shop_services;

         //update shop
         $data = $this->changeShopUpdateDataToArray($request);
         $shop->update($data);

         //check logo or not
         if($request->file('logo')){
            $logoImageFile = $request->file('logo');
            $logoImageName = uniqid() . '_' . time() . '.' . $logoImageFile[0]->getClientOriginalExtension();
            $logoImageFile[0]->storeAs('public',$logoImageName);
            $shop->logo = $logoImageName;
            $shop->save();
        }else{
            $shop->logo = null;
            $shop->save();
        }

         //delete old categories and services and items
         ShopToCategory::where('shop_id',$request->shop_id)->delete();
         ShopToService::where('shop_id',$request->shop_id)->delete();
         ShopServiceItem::where('shop_id',$request->shop_id)->delete();


         //insert new shop categories
        foreach($shop_categories as $shop_category){
            ShopToCategory::create([
                'shop_id' => $shop->id,
                'shop_category_id' => $shop_category
            ]);
        }

        //insert new shop services and items
        foreach($shop_services as $shop_service){

            //insert new services
            ShopToService::create([
                'shop_id' => $shop->id,
                'shop_service_id' => $shop_service['shop_service_id']
            ]);

            //insert new items which are belong to service
            $items = $shop_service['items'];
            foreach($items as $item){
                ShopServiceItem::create([
                    'shop_id' => $shop->id,
                    'shop_service_id' => $shop_service['shop_service_id'],
                    'name' => $item
                ]);
            }

        }

        //old image delete
        $images = ShopImage::where('shop_id',$request->shop_id)->get();
        foreach($images as $image){
            Storage::delete('public/'.$image->name);
            $image->delete();
        }

        //insert images section
        if($request->file('image')){

            $imageFile = $request->file('image');

            //four images validation
            $imageCount = count($imageFile);
            if($imageCount>5){
                return response()->json([
                    'message' => "Can't upload more than 5 photos",
                    'status' => 405
                ]);
            }

            //store images
            for($i=0;$i<$imageCount;$i++){
                $imageName = uniqid() . '_' . time() . '.' . $imageFile[$i]->getClientOriginalExtension();
                $imageFile[$i]->storeAs('public',$imageName);
                ShopImage::create([
                    'shop_id' => $shop->id,
                    'name' => $imageName
                ]);
            }
        }
        return response()->json([
            'shop' => $this->model
            ->where('id',$shop->id)
            ->with(['shop_to_categories' => function($query){
                $query->select('id','shop_id','shop_category_id');
                $query->with('shop_category:id,name');
            }])
            ->with(['shop_to_services' => function($query){
                $query->select('id','shop_id','shop_service_id');
                $query->with(['shop_service' => function($query){
                    $query->select('id','name');
                    $query->with('shop_service_items:id,shop_id,shop_service_id,name');
                }]);
            }])
            ->with('shop_images:id,shop_id,name')
            ->first(),
            'message' => 'Post has been updated',
            'status' => 200
        ]);
    }

    //delete shop
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-shop')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        //image manual delete
        $images = ShopImage::where('shop_id',$request->id)->get();
        foreach($images as $image){
            Storage::delete('public/'.$image->name);
            $image->delete();
        }

        //logo check and delete
        $shop = $this->model->find($request->id);
        if($shop->logo){
            Storage::delete('public/'.$shop->logo);
        }

        //delete shop
        $shop->delete();
        return response()->json([
            'data' => null,
            'message' => 'Shop has been deleted',
            'status' => 200
        ]);
    }

    //change shop update data to array
    private function changeShopUpdateDataToArray($request){
        return [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];
    }

    //change shop create data to array
    private function changeShopCreateDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
    }
}
