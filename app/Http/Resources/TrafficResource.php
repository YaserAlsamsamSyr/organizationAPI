<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrafficResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->user->name,
            'email'=>$this->user->email,
            'mac'=>$this->mac,
            'date'=>$this->year."/".$this->month."/".$this->day
        ];
    }
}
