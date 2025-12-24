<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateBookeResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return[
            'Book'=>[
                'id'=>$this->id,
                'first_name'=>$this->user->first_name,
                'last_name'=>$this->user->last_name,
                'personal_photo'=>$this->user->personal_photo,
                'title'=>$this->house->title,
                'start_date'=>$this->start_date_update,
                'end_date'=>$this->end_date_update,
                'price_difference'=>$this->price_difference,
                'total_price'=>$this->total_price_update
            ]
        ];
    }
}
