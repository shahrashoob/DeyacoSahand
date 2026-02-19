<?php

namespace App\Models\HR\Evaluation\EvaluationForm;

use App\Models\Post\Post;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFormExport extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id','user_id','evaluation_form_id'
    ];

    protected $table='evaluation_form_exports';
    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function worker() {

        return $this->belongsTo(Worker::class,"user_id");
    }
    public function evaluation_form() {

        return $this->belongsTo(EvaluationForm::class);
    }
}
