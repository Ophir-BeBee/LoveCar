<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {



        return [
            'id' => $this->id,
            'brand' => $this->brand->name,
            'model' => $this->model->name,
            'plate_no' => $this->plate_no,
            'mileage' => $this->mileage,
            'image' => config('base_url')."storage/".$this->model->image
        ];
    }
}
