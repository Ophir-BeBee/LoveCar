<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopAdResource extends JsonResource
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
            'image' => config('base_url')."storage/".$this->image,
            'exp_date' => $this->exp_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'shop' => $this->shop
        ];
    }
}
