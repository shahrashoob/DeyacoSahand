<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\HR\Evaluation\EvaluationIndicator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFormIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id','user_id','evaluation_indicator_id','value','weight'
    ];

    protected $table='evaluation_form_indicators';
    public function evaluation_form() {

        return $this->belongsTo(EvaluationForm::class);
    }
    public function evaluation_indicator() {

        return $this->belongsTo(EvaluationIndicator::class);
    }

}
