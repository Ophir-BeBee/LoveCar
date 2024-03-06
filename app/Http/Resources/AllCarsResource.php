<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllCarsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        //assign base url
        $base_url = config('base_url');

        //assign models
        $car_models = $this->car_models;

        foreach($car_models as $car_model){
            $car_model->image = $base_url."storage/".$car_model->image;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'car_models' => $this->car_models
        ];
    }
}
