<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class Social extends Model
{
    //
    protected $fillable=[
        'type',
        'url','organization_id'
    ];
    
    public function organization(){
        return $this->belongsTo(Organization::class);
    }
}
