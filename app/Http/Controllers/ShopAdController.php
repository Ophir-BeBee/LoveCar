<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopAd;
use Illuminate\Http\Request;
use App\Http\Requests\ShopAdRequest;
use App\Http\Resources\ShopAdResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ShopAdController extends Controller
{

    protected $model;

    public function __construct(ShopAd $model)
    {
        $this->model  = $model;
    }

    //get shop ads
    public function index(){
        $data = $this->model
        ->with('shop:id,name,address,logo')
        ->get();
        if(count($data)==0){
            return sendResponse(404,'There is no shop ads');
        }
        return sendResponse(ShopAdResource::collection($data),200);
    }

    //create shop ad
    public function store(ShopAdRequest $request){
        //user authorization
        if(Gate::denies('auth-shopAds')){
            return sendResponse(401,'Not allowed');
        }

        //check shop
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return sendResponse(404,'Shop not found');
        }

        //check ads
        $shopAds = $this->model->where('shop_id',$request->shop_id)->first();
        if($shopAds){
            return sendResponse(405,'This shop already has ads');
        }

        //create shop ad
        $data = $this->changeShopAdsDataToArray($request);
        $shopAds = $this->model->create($data);

        //insert ads image
        if($request->file('image')){
            $imageFile = $request->file('image');

            //create image name
            $imageName = uniqid() . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->storeAs('public',$imageName);
            $shopAds->image = $imageName;
            $shopAds->save();
        }

        $data = $this->model
        ->where('id',$shopAds->id)
        ->with('shop:id,name,address,logo')
        ->first();
        return sendResponse(new ShopAdResource($data),200,'ShopAds has been created');
    }

    //update shop ads
    public function update(ShopAdRequest $request){
        //user authorization
        if(Gate::denies('auth-shopAds')){
            return sendResponse(401,'Not allowed');
        }

        //check ads
        $shopAds = $this->model->where('id',$request->shopAds_id)->first();
        if(!$shopAds){
            return sendResponse(404,'Shop ads not found');
        }

        //delete image
        Storage::delete('public/'.$shopAds->image);

        //update ads data
        $shopAds->update([
            'exp_date' => $request->exp_date
        ]);

        if($request->file('image')){
            $imageFile = $request->file('image');

            //create image name
            $imageName = uniqid() . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->storeAs('public',$imageName);
            $shopAds->image = $imageName;
            $shopAds->save();
        }

        $data = $this->model
        ->where('id',$shopAds->id)
        ->with('shop:id,name,address,logo')
        ->first();
        return sendResponse(new ShopAdResource($data),200,'ShopAds has been updated');
    }

    //delete shop ads
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-shopAds')){
            return sendResponse(401,'Not allowed');
        }

        //check ads
        $shopAds = $this->model->where('id',$request->shopAds_id)->first();
        if(!$shopAds){
            return sendResponse(404,'Shop ads not found');
        }

        //delete ads
        $shopAds = $this->model->find($shopAds->id);
        Storage::delete('public/'.$shopAds->image);
        $shopAds->delete();
        return sendResponse(200,'ShopAds has been deleted');
    }

    //change shop ads data to array
    private function changeShopAdsDataToArray($request){
        return [
            'shop_id' => $request->shop_id,
            'exp_date' => $request->exp_date,
        ];
    }

}
