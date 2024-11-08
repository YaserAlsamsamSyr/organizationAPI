<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Comment extends Model
{
    protected $fillable = ['text','project_id'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
