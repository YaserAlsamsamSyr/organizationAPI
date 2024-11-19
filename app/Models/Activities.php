<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Activities extends Model
{
    //
    protected $fillable=[
        'type',
        'text','project_id'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
