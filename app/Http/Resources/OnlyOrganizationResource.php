<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\SuggestResource;
use App\Http\Resources\SkillResource;
use App\Http\Resources\DetailsResource;
use App\Http\Resources\NumberResource;
use App\Http\Resources\SocialsResource;
use App\Http\Resources\ProblemResource;

class OnlyOrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "name"=>$this->name,
            "email"=>$this->email,
            "orgId"=>$this->organization->id,
            "created_at"=>$this->organization->created_at,
            "experience"=>$this->organization->experience,
            "details"=>DetailsResource::collection($this->organization->details),
            "skils"=>SkillResource::collection($this->organization->skils),
            "logo"=>$this->organization->logo,
            "images"=>ImageResource::collection($this->organization->images),
            "view"=>$this->organization->view,
            "message"=>$this->organization->message,
            "number"=>NumberResource::collection($this->organization->numbers),
            "socials"=>SocialsResource::collection($this->organization->socials),
            "address"=>$this->organization->address,
            "phone"=>$this->organization->phone,
            'suggests'=>SuggestResource::collection($this->organization->suggests),
            'problems'=>ProblemResource::collection($this->organization->problems)
        ];
    }
}
