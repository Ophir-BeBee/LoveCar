<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopCategoryRequest;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShopCategoryController extends Controller
{

    protected $model;

    public function __construct(ShopCategory $model)
    {
        $this->model = $model;
    }

    //get all category
    public function index(){
        $categories = $this->model->get();
        return sendResponse($categories,200);
    }

    //create category
    public function store(ShopCategoryRequest $request){
        //user authorization
        if(Gate::denies('auth-shop-category')){
            return sendResponse(null,401,'Not allowed');
        }

        //check category
        $category = $this->model->where('name',$request->name)->first();
        if($category){
            return sendResponse(null,405,'Category already exist');
        }

        //create category
        $category = $this->model->create(['name' => $request->name]);
        return sendResponse($category,200,'Category create success');
    }

    //update category
    public function update(ShopCategoryRequest $request){
        //user authorization
        if(Gate::denies('auth-shop-category')){
            return sendResponse(null,401,'Not allowed');
        }

        //check category
        $category = $this->model->find($request->category_id);
        if(!$category){
            return sendResponse(null,405,'Category not found');
        }

        //check name available or not
        $check = $this->model->where('name',$request->name)->first();
        if($check){
            return sendResponse(null,405,'Category already exist');
        }

        $category->update(['name'=>$request->name]);
        return sendResponse($category,200,'Category update success');
    }

    //delete category
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-shop-category')){
            return sendResponse(null,401,'Not allowed');
        }

        //check category
        $category = $this->model->find($request->category_id);
        if(!$category){
            return sendResponse(null,404,'Category not found');
        }

        //delete category
        $category->delete();
        return sendResponse(null,200,'Category deleted success');
    }
}
