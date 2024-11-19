<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Problem;

class TypeProblem extends Model
{
    protected $fillable=['type','problem_id'];

    public function problem(){
        return $this->belongsTo(Problem::class);
    }
}
