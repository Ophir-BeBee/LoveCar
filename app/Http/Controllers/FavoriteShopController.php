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
            return response()->json([
                'data' => null,
                'message' => 'You have no favorite shop',
                'status' => 404
            ]);
        }
        return response()->json([
            'data' => $data,
            'status' => 200
        ]);
    }

    //create favorite shop
    public function store(Request $request){
        //check shop exist
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return response()->json([
                'message' => 'Shop not found',
                'status' => 404
            ]);
        }

        //check favorite shop
        $favoriteShop = $this->model->where('shop_id',$request->shop_id)->first();
        if($favoriteShop){
            return response()->json([
                'message' => 'You already added this shop to favorites',
                'status' => 405
            ]);
        }

        //create favorite
        $favoriteShop = $this->model->create([
            'user_id' => Auth::user()->id,
            'shop_id' => $request->shop_id
        ]);
        return response()->json([
            'data' => $this->model
                    ->where('id',$favoriteShop->id)
                    ->with('shop:id,name,logo,address')
                    ->first(),
            'message' => 'You added this shop to favorites',
            'status' => 200
        ]);
    }

    //delete favorite shop
    public function destroy(Request $request){
        //check shop
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return response()->json([
                'message' => 'Shop not found',
                'status' => 404
            ]);
        }

        //check favorite shop
        $favoriteShop = $this->model->where('shop_id',$request->shop_id)->first();
        if(!$favoriteShop){
            return response()->json([
                'message' => 'You already removed this shop from favorites',
                'status' => 405
            ]);
        }

        $favoriteShop->delete();
        return response()->json([
            'data' => null,
            'message' => 'You removed this shop from favorites',
            'status' => 200
        ]);
    }
}
