<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Book'=>[
                'id'=>$this->id,
                'first_name'=>$this->user->first_name,
                'last_name'=>$this->user->last_name,
                'personal_photo'=>$this->user->personal_photo,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'total_price'=>$this->total_price,
                'House'=>[
            'id'=>$this->house->id,
            'user_id'=>$this->house->user_id,
            'category'=>$this->house->category,
            'title'=>$this->house->title,
            'bedrooms'=>$this->house->bedrooms,
            'bathrooms'=>$this->house->bathrooms,
            'livingrooms'=>$this->house->livingrooms,
            'area'=>$this->house->area,
            'day_price'=>$this->house->day_price,
            'mainImage'=>$this->house->mainImage,
            'descreption'=>$this->house->descreption,
            'city' => $this->house->city->name,
            'governorate' => $this->house->governorate->name,
            'avg_star'=>$this->house->evaluations_avg_star
            ]
            ]
            ];
    }
}
