<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFormLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'evaluation_form_id','user_id','evaluation_indicator_id','event_id','status_id','message_id'
    ];

    protected $table='evaluation_form_logs';
    public function status() {

        return $this->belongsTo(Status::class);
    }
    public function event() {

        return $this->belongsTo(Event::class);
    }
    public function worker() {

        return $this->belongsTo(Worker::class,"user_id");
    }
    public function message() {

        return $this->belongsTo(Message::class);
    }
    public function evaluation_form() {

        return $this->belongsTo(EvaluationForm::class);
    }

}
