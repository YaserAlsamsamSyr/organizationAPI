<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Summary;
use App\Models\Activities;
use App\Models\Suggest;
use App\Models\Problem;
use App\Models\Opinion;

class Project extends Model
{
    protected $fillable = [
        'name',
        'address',
        'logo',
        'start_At',
        'end_At',
        'benefitDir',
        'benefitUnd',
        'pdfURL',
        'videoURL',
        'organization_id',
        'allWhoRate'
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

    public function summaries(){
        return $this->hasMany(Summary::class);
    }

    public function activities(){
        return $this->hasMany(Activities::class);
    }

    public function suggests(){
        return $this->hasMany(Suggest::class);
    }

    public function problems(){
        return $this->hasMany(Problem::class);
    }

    public function opinions(){
        return $this->hasMany(Opinion::class);
    }
}
