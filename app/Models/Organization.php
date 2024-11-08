<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;
use App\Models\Image;

class Organization extends Model
{
    protected $fillable=[
        "experience",
        "details",
        "skils",
        "logo",
        "view",
        "message",
        "number",
        "socials",
        "address",
        "phone",
        "complaints",
        "suggests",
        "user_id"
    ];

    public function projects(){
        return $this->hasMany(Project::class);
    }

    public function owner(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function images(){
        return $this->hasMany(Image::class);
    }
}
