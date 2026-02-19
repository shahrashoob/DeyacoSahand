<?php

namespace App\Models\HR\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationCompletionType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];

    protected $table='evaluation_completion_types';
}
