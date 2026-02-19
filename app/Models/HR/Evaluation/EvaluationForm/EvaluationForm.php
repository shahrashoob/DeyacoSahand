<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\HR\Evaluation\EvaluationType;
use App\Models\Post\Post;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id','user_id','status_id','last_completion_date_time','evaluation_type_id','value_evaluation_form'
    ];

    protected $table='evaluation_forms';


    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function worker() {

        return $this->belongsTo(Worker::class,"user_id");
    }
    public function status() {

        return $this->belongsTo(Status::class,'status_id');
    }
    public function get_last_completion_date_time(){
        return jdate(Carbon::parse($this->last_completion_date_time)->timestamp)->format('H:i Y/m/d ');
    }
    public function get_created_at(){
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }
    public function evaluation_type()
    {

        return $this->belongsTo(EvaluationType::class);
    }
    public function evaluation_form_indicators()
    {

        return $this->hasMany(EvaluationFormIndicator::class);
    }
    public function evaluation_form_exports()
    {

        return $this->hasMany(EvaluationFormExport::class);
    }
    public function get_evaluator()
    {
        $evaluation_form_export=EvaluationFormExport::where('evaluation_form_id' , $this->id)->first();

        return $evaluation_form_export->worker? $evaluation_form_export->worker->fullname():"نامشخص";
    }
}
