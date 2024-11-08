<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Comment;
use App\Models\Image;

class Project extends Model
{
    protected $fillable = [
        'name',
        'address',
        'logo',
        'summary',
        'start_At',
        'end_At',
        'benefitDir',
        'benefitUnd',
        'activities',
        'rate',
        'pdfURL',
        'videoURL',
        'organization_id'
    ];

    public function owner(){
        return $this->belongsTo(Organization::class,"organization_id");
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function images(){
        return $this->hasMany(Image::class);
    }
}
