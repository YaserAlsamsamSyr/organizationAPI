<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TypeProblemResource;
class ProblemResource extends JsonResource
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
            'date'=>$this->created_at,
            'number'=>$this->id,
            'fullName'=>$this->fullName,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'address'=>$this->address,
            'benifit'=>$this->benifit,
            'problemDate'=>$this->problemDate,
            'isPrevious'=>$this->isPrevious,
            'text'=>$this->text,
            'typeProblem'=>TypeProblemResource::collection($this->typeProblems),
            'project_name'=>$this->project->name??'',
            'organization_name'=>$this->organization->owner->name??''
        ];
    }
}
