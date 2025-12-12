<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'House'=>[
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'category'=>$this->category,
            'title'=>$this->title,
            'bedrooms'=>$this->bedrooms,
            'bathrooms'=>$this->bathrooms,
            'livingrooms'=>$this->livingrooms,
            'area'=>$this->area,
            'day_price'=>$this->day_price,
            'mainImage'=>$this->mainImage,
            'descreption'=>$this->descreption,
            'city_id' => $this->city_id,
            'governorate_id' => $this->governorate_id,
            'city' => $this->city ? [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ] : null,
            'governorate' => $this->governorate ? [
                'id'   => $this->governorate->id,
                'name' => $this->governorate->name,
            ] : null,
            ]
        ];
    }
}
