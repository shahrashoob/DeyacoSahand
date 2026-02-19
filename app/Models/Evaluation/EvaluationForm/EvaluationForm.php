<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\Post\Post;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id','user_id','status_id','last_completion_date_time'
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
}
