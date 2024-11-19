<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class Skil extends Model
{
    //
    protected $fillable=[
        'text','organization_id'
    ];
    
    public function organization(){
        return $this->belongsTo(Organization::class);
    }
}
