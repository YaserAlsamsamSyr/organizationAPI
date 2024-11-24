<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Activities extends Model
{
    //
    protected $fillable=[
        'type',
        'text',
        'project_id',
        'pdf',
        'rate',
        'videoImg',
        'videoUrl',
        'project_id'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    public function images(){
        return $this->hasMany(Image::class);
    }
}
