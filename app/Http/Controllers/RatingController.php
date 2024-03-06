<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Http\Requests\RatingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RatingController extends Controller
{

    protected $model;

    public function __construct(Rating $model)
    {
        $this->model = $model;
    }

    //create rating
    public function store(RatingRequest $request){
        //check shop
        $shop = Shop::find($request->shop_id);
        if(!$shop){
            return sendResponse(null,404,'Shop not found');
        }

        //check rating
        $rating = $this->model->where('user_id',Auth::user()->id)->where('shop_id',$request->shop_id)->first();
        if($rating){
            $rating->delete();
        }

        //create rating
        $data = $this->changeRatingDataToArray($request);
        $rating = $this->model->create($data);

        $this->averageRatingCalculate($shop);  //call average rating calculate function

        $data = $this->model
        ->where('id',$rating->id)
        ->with('shop:id,ratings')
        ->first();
        return sendResponse($data,200,'Added your rating');
    }

    //update rating
    public function update(RatingRequest $request){
        //check rating
        $rating = $this->model->where('user_id',Auth::user()->id)->where('shop_id',$request->shop_id)->first();
        if(!$rating){
            return sendResponse(null,404,'Rating not found');
        }

        //check shop
        $shop = Shop::find($rating->shop_id);
        if(!$shop){
            $rating->delete();
            return sendResponse(null,404,'Shop not found');
        }

        //user authorization
        if(Gate::denies('auth-rating-update',$rating)){
            return sendResponse(null,401,'Not allowed');
        }

        //update rating data
        $rating->star = $request->star;
        $rating->feedback = $request->feedback ? $request->feedback : null;
        $rating->save();

        $this->averageRatingCalculate($shop);  //call average rating calculate function

        $data = $this->model
        ->where('id',$rating->id)
        ->with('shop:id,ratings')
        ->first();
        return sendResponse($data,200,'Rating has been updated');
    }

    //delete rating
    public function destroy(Request $request){
        //check rating
        $rating = $this->model->find($request->rating_id);
        if(!$rating){
            return sendResponse(null,404,'Rating already deleted');
        }

        //check shop
        $shop = Shop::find($rating->shop_id);
        if(!$shop){
            $rating->delete();
            return sendResponse(null,404,'Shop not found');
        }

        //user authorization
        if(Gate::denies('auth-rating-delete',$shop)){
            return sendResponse(null,401,'Not allowed');
        }

        //delete rating
        $rating->delete();

        $this->averageRatingCalculate($shop);   //call average rating calculate function

        return sendResponse(null,200,'Rating has been deleted');
    }

    //average rating calculate
    private function averageRatingCalculate($shop){
        $allRatings = $this->model->get();  //get all ratings
        $allRaters = count($allRatings);  //get all raters

        $allStars = 0;
        foreach($allRatings as $rating){
            $allStars += $rating->star;   //get all stars
        }

        $averageRating = $allStars/$allRaters;  //average calculation
        $shop->rating = $averageRating;
        $shop->save();
    }

    //change rating data to array
    private function changeRatingDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'shop_id' => $request->shop_id,
            'star' => $request->star,
            'feedback' => $request->feedback ? $request->feedback : null
        ];
    }

}
