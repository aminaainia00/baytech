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
            'Update_Book'=>[
                'id'=>$this->id,
             'start_date_update'=>$this->start_date_update,
             'end_date_update'=>$this->end_date_update,
            'price_difference'=>$this->price_difference,
            'total_price_update'=>$this->total_price_update
            ]
        ];
    }
}
