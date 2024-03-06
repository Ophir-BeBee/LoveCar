<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Income;
use App\Models\IncomeDate;
use Illuminate\Http\Request;
use App\Http\Requests\IncomeRequest;
use Illuminate\Support\Facades\Gate;

class IncomeController extends Controller
{

    protected $model;

    public function __construct(Income $model)
    {
        $this->model = $model;
    }

    //get income
    public function index($carId,$month,$year){
        $data = IncomeDate::whereYear('created_at',$year)
        ->whereMonth('created_at',$month)
        ->with(['incomes' => function($query) use($carId){
            $query->where('car_id',$carId);
        }])
        ->get();
        return $data;
    }

    //create income
    public function store(IncomeRequest $request){
        //check date
        $date = IncomeDate::where('date',$request->date)->first();
        if(!$date){
            $date = IncomeDate::create(["date" => $request->date]);
        }
        //check car
        $car = Car::find($request->incomes[0]['car_id']);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        foreach($request->incomes as $income){
            $this->model->create([
                'car_id' => $income['car_id'],
                'income_date_id' => $date->id,
                'from' => $income['from'],
                'to' => $income['to'],
                'price' => $income['price'],
            ]);
        }
        $data = IncomeDate::where('id',$date->id)
        ->with('incomes')
        ->get();
        return sendResponse($data,200,'Income created success');
    }

    //update income
    public function update(IncomeRequest $request){
        //check income date
        $income_date = IncomeDate::find($request->income_date_id);
        if(!$income_date){
            return sendResponse(null,404,'Not found');
        }

        //check income
        $incomes = $this->model->where('income_date_id',$income_date->id)->get();
        if(count($incomes)==0){
            return sendResponse(null,404,'Income not found');
        }

        //check car
        $car = Car::find($incomes[0]['car_id']);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-income-update',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        //update date
        $income_date->date = $request->date;
        $income_date->save();

        //delete income data
        $incomes = $this->model->where('income_date_id',$income_date->id)->delete();
        //create new income data
        foreach($request->incomes as $income){
            $this->model->create([
                'car_id' => $income['car_id'],
                'income_date_id' => $income_date->id,
                'from' => $income['from'],
                'to' => $income['to'],
                'price' => $income['price'],
            ]);
        }

        $data = IncomeDate::where('id',$income_date->id)
        ->with('incomes')
        ->first();
        return sendResponse($data,200,'Income updated success');
    }

    //delete income
    public function destroy(Request $request){
        //check income date
        $income_date = IncomeDate::find($request->income_date_id);
        if(!$income_date){
            return sendResponse(null,404,'Not found');
        }

        //check incomes
        $incomes = $this->model->where('income_date_id',$income_date->id)->get();

        //check car
        $car = Car::find($incomes[0]->car_id);
        if(!$car){
            return sendResponse(null,404,'Car not found');
        }

        //user authorization
        if(Gate::denies('auth-income-delete',$car)){
            return sendResponse(null,401,'Not allowed');
        }

        $this->model->where('income_date_id',$income_date->id)->delete();
        return sendResponse(null,200,'Incomet deleted success');
    }
}
