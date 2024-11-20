<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Summary extends Model
{
    //
    protected $fillable=[
        'text',
        'project_id',
        'type'
    ];
    
    public function project(){
        return $this->belongsTo(Project::class);
    }
}
