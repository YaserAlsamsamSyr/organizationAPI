<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable=['year'];

    public function months(){
        return $this->belongsToMany(Month::class,'year_months','year_id','month_id');
    }
}
