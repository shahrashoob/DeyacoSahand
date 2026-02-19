<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\HR\Evaluation\EvaluationIndicator;
use App\Models\Utility\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFormIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'evaluation_form_id','evaluation_indicator_id','value','weight','message_id'
    ];

    protected $table='evaluation_form_indicators';
    public function evaluation_form() {

        return $this->belongsTo(EvaluationForm::class);
    }
    public function evaluation_indicator() {


        return $this->belongsTo(EvaluationIndicator::class,'evaluation_indicator_id');
    }
    public function message() {


        return $this->belongsTo(Message::class);
    }
}
