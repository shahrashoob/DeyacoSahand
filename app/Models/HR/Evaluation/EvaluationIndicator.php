<?php

namespace App\Models\HR\Evaluation;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption','evaluation_completion_type_id'
    ];

    protected $table='evaluation_indicators';


    public function evaluation_completion_type() {

        return $this->belongsTo(EvaluationCompletionType::class);
    }
}
