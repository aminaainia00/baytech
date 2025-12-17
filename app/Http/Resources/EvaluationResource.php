<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Evaluation'=>[
                'house_id'=>$this->house_id,
                'user_id'=>$this->user_id,
                'star'=>$this->star
            ]
            ];
    }
}
