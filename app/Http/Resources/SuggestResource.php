<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestResource extends JsonResource
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
            'text'=>$this->text,
            'email'=>$this->email,
            'fullName'=>$this->fullName,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'project_name'=>$this->project->name ?? '',
            'organization_name'=>$this->organization->owner->name ?? ''
        ];
    }
}
