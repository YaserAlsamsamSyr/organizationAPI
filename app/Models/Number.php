<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class Number extends Model
{
        //
        protected $fillable=[
            'type',
            'number',
            'organization_id'
        ];

        public function organization(){
            return $this->belongsTo(Organization::class);
        }
}
