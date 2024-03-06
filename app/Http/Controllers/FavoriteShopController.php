<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\FavoriteShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteShopController extends Controller
{

    protected $model;

    public function __construct(FavoriteShop $model)
    {
        $this->model = $model;
    }

    //index
    public function index(){
        $data = $this->model->with('shop:id,name,logo,address')->get();

        if(count($data)==0){
            return sendResponse(null,404,'You have no favorite shop');
        }

        return sendResponse($data,200);
    }

    //create favorite shop
    public function store(Request $request){
        //check shop exist
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return sendResponse(null,404,'Shop not found');
        }

        //check favorite shop
        $favoriteShop = $this->model->where('shop_id',$request->shop_id)->first();
        if($favoriteShop){
            return sendResponse(null,405,'You already added this shop to favorites');
        }

        //create favorite
        $favoriteShop = $this->model->create([
            'user_id' => Auth::user()->id,
            'shop_id' => $request->shop_id
        ]);
        $data = $this->model->where('id',$favoriteShop->id)->with('shop:id,name,logo,address')->first();
        return sendResponse($data,200,'You added this shop to favorites');
    }

    //delete favorite shop
    public function destroy(Request $request){
        //check shop
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return sendResponse(null,404,'Shop not found');
        }

        //check favorite shop
        $favoriteShop = $this->model->where('shop_id',$request->shop_id)->first();
        if(!$favoriteShop){
            return sendResponse(null,405,'You already removed this shop from favorties');
        }

        $favoriteShop->delete();
        return sendResponse(null,200,'You removed this shop from favorites');
    }
}
