<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    protected $fillable=['mac','firstTime'];

    public function days(){
        return $this->belongsToMany(Day::class,'traffic_Per_Days','traffic_id','day_id');
    }
}
