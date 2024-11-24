<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Organization;

class Image extends Model
{
    protected $fillable=['url','project_id','organization_id','activity_id'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function organization(){
        return $this->belongsTo(Organization::class);
    }
    public function activity(){
        return $this->belongsTo(Activities::class);
    }
}
