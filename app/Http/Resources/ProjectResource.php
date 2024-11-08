<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ImageResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'address'=>$this->address,
            'logo'=>$this->logo,
            'summary'=>$this->summary,
            'start_At'=>$this->start_At,
            'end_At'=>$this->end_At,
            'benefitDir'=>$this->benefitDir,
            'benefitUnd'=>$this->benefitUnd,
            'activities'=>$this->activities,
            'rate'=>$this->rate,
            'pdfURL'=>$this->pdfURL,
            'videoURL'=>$this->videoURL,
            'images'=>ImageResource::collection($this->images),
            'comments'=>CommentResource::collection($this->comments),
        ];
    }
}
