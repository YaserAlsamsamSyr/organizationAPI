<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivitiesResource extends JsonResource
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
            'type'=>$this->type,
            'text'=>$this->text,
            'videoUrl'=>$this->videoUrl,
            'videoImg'=>$this->videoImg,
            'rate'=>$this->rate,
            'pdf'=>$this->pdf,
            'comments'=>CommentResource::collection($this->comments),
            'images'=>ImageResource::collection($this->images)
        ];
    }
}
