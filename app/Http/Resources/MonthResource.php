<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstTime=0;
        $allTime=0;
        foreach($this->days as $i){
            $firstTime+=$i->traffics()->where('firstTime',1)->count();
            $allTime+=$i->traffics()->count();
        }
        return [
            'month'=>$this->month,
            'firstTime'=>$firstTime,
            'allTime'=>$allTime,
            'days'=>DayResource::collection($this->days)
        ];
    }
}
