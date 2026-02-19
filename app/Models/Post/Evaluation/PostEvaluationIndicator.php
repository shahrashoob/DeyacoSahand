<?php

namespace App\Models\Post\Evaluation;

use App\Models\HR\Evaluation\EvaluationIndicator;
use App\Models\HR\Evaluation\EvaluationType;
use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostEvaluationIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id','evaluation_type_id','evaluation_indicator_id','weight','priority_number','post_evaluation_id'
    ];

    protected $table='post_evaluation_indicators';


    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function evaluation_type() {

        return $this->belongsTo(EvaluationType::class);
    }
    public function evaluation_indicator() {

        return $this->belongsTo(EvaluationIndicator::class);
    }
    public function post_evaluation() {

        return $this->belongsTo(PostEvaluation::class);
    }
}
