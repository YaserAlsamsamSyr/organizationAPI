<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Project;
use App\Models\TypeProblem;

class Problem extends Model
{
    protected $fillable=[
        'date',
        'project_id',
        'organization_id',
        'number',
        'fullName',
        'phone',
        'email',
        'address',
        'benifit',
        'text',
        'problemDate',
        'isPrevious',
    ];
    
    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function typeProblems(){
        return $this->hasMany(TypeProblem::class);
    }
}
