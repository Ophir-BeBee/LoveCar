<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorialResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => config('base_url')."storage/".$this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_liked' => $this->is_liked,
            'tutorial_likes_count' => $this->tutorial_likes_count,
            'steps' => TutorialStepResource::collection($this->tutorial_steps)
        ];
    }
}
