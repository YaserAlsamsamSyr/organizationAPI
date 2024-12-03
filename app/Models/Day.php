<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable=['day'];

    public function traffics(){
        return $this->belongsToMany(Traffic::class,'traffic_per_days','day_id','traffic_id');
    }
    public function months(){
        return $this->belongsToMany(Month::class,'month_days','day_id','month_id');
    }
}
