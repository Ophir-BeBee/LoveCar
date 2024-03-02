<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        //call url variable
        $base_url = config('base_url');

        //assign post_images to variable first
        $post_images = $this->post->post_images;

        //create array
        $images = array();
        foreach($post_images as $post_image){
            $post_image_name = $base_url."storage/".$post_image->name;  //prepare image url
            array_push($images,$post_image_name);  //push to array
        }

        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'title' => $this->post->title,
            'description' => $this->post->description,
            'post_images' => $images,
            'is_saved' => $this->post->is_saved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
