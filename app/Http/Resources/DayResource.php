<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstTime=$allTime=0;
        $firstTime+=$this->traffics()->where('firstTime',1)->count();
        $allTime+=$this->traffics()->count();
        return [
            'day'=>$this->day,
            'firstTime'=>$firstTime,
            'allTime'=>$allTime
        ];
    }
}
