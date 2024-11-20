<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpinionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'mac'=>$this->mac,
            'q1'=>$this->q1,
            'q2'=>$this->q2,
            'q3'=>$this->q3,
            'q4'=>$this->q4,
            'q5'=>$this->q5,
            'q6'=>$this->q6,
            'q7'=>$this->q7,
            'q8'=>$this->q8,
            'q9'=>$this->q9,
            'q10'=>$this->q10,
            'q11'=>$this->q11,
            'q12'=>$this->q12,
            'q13'=>$this->q13,
            'q14'=>$this->q14,
            'q15'=>$this->q15,
            'q16'=>$this->q16,
            'q17'=>$this->q17,
            'q18'=>$this->q18,
            'q19'=>$this->q19,
            'q20'=>$this->q20
        ];
    }
}
