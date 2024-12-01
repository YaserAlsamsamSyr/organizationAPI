<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    protected $fillable=['month'];

    public function days(){
        return $this->belongsToMany(Day::class,'month_days','month_id','day_id');
    }
    public function years(){
        return $this->belongsToMany(Year::class,'year_months','month_id','year_id');
    }
}
