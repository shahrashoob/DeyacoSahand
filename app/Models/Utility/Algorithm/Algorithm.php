<?php

namespace App\Models\Utility\Algorithm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Algorithm extends Model {
    use HasFactory;
    public function algorithm_type() {
        return $this->belongsTo( AlgorithmType::class );
    }
}
