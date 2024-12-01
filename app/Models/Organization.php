<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;
use App\Models\Image;
use App\Models\Detail;
use App\Models\Social;
use App\Models\Number;
use App\Models\Skil;
use App\Models\Problem;
use App\Models\Suggest;

class Organization extends Model
{
    protected $fillable=[
        "experience",
        "logo",
        "view",
        "message",
        "address",
        "phone",
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

    public function details(){
        return $this->hasMany(Detail::class);
    }
    
    public function skils(){
        return $this->hasMany(Skil::class);
    }
    
    public function numbers(){
        return $this->hasMany(Number::class);
    }
    
    public function socials(){
        return $this->hasMany(Social::class);
    }
    
    public function suggests(){
        return $this->hasMany(Suggest::class);
    }
    public function problems(){
        return $this->hasMany(Problem::class);
    }
}
