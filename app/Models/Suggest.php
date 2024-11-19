<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Project;

class Suggest extends Model
{
    protected $fillable=[
        'text',
        'email',
        'fullName',
        'phone',
        'address',
        'project_id',
        'organization_id'
    ];
    
    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
