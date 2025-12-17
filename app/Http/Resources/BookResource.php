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
                'user_id' => $this->user_id,
                'house_id' => $this->house_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'total_price'=>$this->total_price
            ]
            ];
    }
}
