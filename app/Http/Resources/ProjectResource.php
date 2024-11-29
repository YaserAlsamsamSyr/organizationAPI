<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\SummaryResource;
use App\Http\Resources\ActivitiesResource;
use App\Http\Resources\ProblemResource;
use App\Http\Resources\SuggestResource;

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
            'orgId'=>$this->owner->owner->id,
            'orgName'=>$this->owner->owner->name,
            'id'=>$this->id,
            'name'=>$this->name,
            'address'=>$this->address,
            'logo'=>$this->logo,
            'summary'=>SummaryResource::collection($this->summaries),
            'start_At'=>$this->start_At,
            'end_At'=>$this->end_At,
            'benefitDir'=>$this->benefitDir,
            'benefitUnd'=>$this->benefitUnd,
            'activities'=>ActivitiesResource::collection($this->activities),
            'rate'=>$this->rate,
            'pdfURL'=>$this->pdfURL,
            'videoURL'=>$this->videoURL,
            'videoLogo'=>$this->videoLogo,
            'allWhoRate'=>$this->allWhoRate,
            'images'=>ImageResource::collection($this->images),
            'comments'=>CommentResource::collection($this->comments),
            'suggests'=>SuggestResource::collection($this->suggests),
            'problems'=>ProblemResource::collection($this->problems),
        ];
    }
}
