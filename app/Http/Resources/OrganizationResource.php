<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ImageResource;

class OrganizationResource extends JsonResource
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
        "id"=>$this->id,
        "name"=>$this->name,
        "email"=>$this->email,
        "orgId"=>$this->organization->id,
        "experience"=>$this->organization->experience,
        "details"=>$this->organization->details,
        "skils"=>$this->organization->skils,
        "logo"=>$this->organization->logo,
        "images"=>ImageResource::collection($this->organization->images),
        "view"=>$this->organization->view,
        "message"=>$this->organization->message,
        "number"=>$this->organization->number,
        "socials"=>$this->organization->socials,
        "address"=>$this->organization->address,
        "phone"=>$this->organization->phone,
        "complaints"=>$this->organization->complaints,
        "suggests"=>$this->organization->suggests,
        "projects"=>ProjectResource::collection($this->organization->projects)
       ];
    }
}
