<?php

namespace App\Models\Post\Evaluation;

use App\Events\HR\EvaluationFormLogEvent;
use App\Models\HR\Evaluation\EvaluationCompletionType;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationForm;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormExport;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormIndicator;
use App\Models\HR\Evaluation\EvaluationType;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id', 'evaluation_type_id', 'cron', 'time_of_complete', 'active_status_id', 'last_run_date_time', 'next_must_run_date_time'
    ];
    protected $table = 'post_evaluations';

    public function post()
    {

        return $this->belongsTo(Post::class);
    }

    public function evaluation_type()
    {

        return $this->belongsTo(EvaluationType::class);
    }

    public function active_status()
    {

        return $this->belongsTo(Status::class, 'active_status_id');
    }

    public function get_last_run_date_time()
    {
        if ($this->last_run_date_time == null) {
            return null;
        }
        return jdate(Carbon::parse($this->last_run_date_time)->timestamp)->format('H:i Y/m/d ');

    }

    public function get_next_must_run_date_time()
    {

        return jdate(Carbon::parse($this->next_must_run_date_time)->timestamp)->format('H:i Y/m/d ');

    }

    public function next_run_date()
    {
        $cron = CronExpression::factory($this->cron);
        $cron->isDue();

//        $previous_run_date = Carbon::parse( $cron->getPreviousRunDate()->format( 'Y-m-d H:i:s' ) );
        $next_run_date = Carbon::parse($cron->getNextRunDate()->format('Y-m-d H:i:s'));

        return $next_run_date;
    }

    public function post_evaluation_indicators()
    {

        return $this->hasMany(PostEvaluationIndicator::class, 'post_evaluation_id');
    }

    public static function CreateNewEvaluationForm(PostEvaluation $post_evaluation, $log_user_id)
    {
        //چک کردن اینکه آیا زمان بعدی رسیده است یا خیر؟

        $current_date_time = now();
        if ($post_evaluation->next_must_run_date_time > $current_date_time) {

            return [
                'result' => false,
                'error' => 'زمان انجام فرم ارزیابی نامتعبر است.',
            ];

        }
        if ($post_evaluation->active_status_id == 1210) {
            return [
                'result' => false,
                'error' => 'وضعیت ارزیابی غیر فعال می باشد.',
            ];
        }

        $last_completion_date_time = $current_date_time->addHours(24);
        $post_evaluation_indicator_list = PostEvaluationIndicator::where("post_evaluation_id", $post_evaluation->id)->get();

        $user_ids = PostUser::
        where("post_id", $post_evaluation->post_id)->
        groupBy("user_id")->
        pluck("user_id")->
        toArray();
        foreach ($user_ids as $user_id) {//در اینجا به ازای هر کاربر فرم ارزیابی ایجاد می شود.
            //ایجاد جدول فرم ارزیابی
            $evaluation_form = EvaluationForm::create([
                'post_id' => $post_evaluation->post->id,
                'user_id' => $user_id,
                'status_id' => 4650001,// در انتظار ارزیابی
                'last_completion_date_time' => $last_completion_date_time,
                'evaluation_type_id' => $post_evaluation->evaluation_type_id,

            ]);

            //ایجاد جدول فرم ارزیابی _ارزیاب ها
            $evaluation_form_export = EvaluationFormExport::create([
                'post_id' => $post_evaluation->post->parent_id,
                'evaluation_form_id' => $evaluation_form->id,
                'user_id' => null,
                'status_id' => 4650001,//وضیعت را در انتظار انجام ارزیابی می گزاریم
                'last_completion_date_time' => $last_completion_date_time,

            ]);

            //ایجاد جدول فرم ارزیابی _شاخص
            foreach ($post_evaluation_indicator_list as $post_evaluation_indicator) {
                $evaluation_form_indicator = EvaluationFormIndicator::create([
                    'evaluation_form_id' => $evaluation_form->id,
                    'evaluation_indicator_id' => $post_evaluation_indicator->evaluation_indicator_id,
                    'value' => null,
                ]);
            }

            //ایجاد رکورد در جدول لاگ که وضعیت ان در انتظار ارزیابی می باشد.
            event(new EvaluationFormLogEvent($evaluation_form, 4650001, null, $log_user_id));

        }

        //اپدیت کردن زمان بعدی ایجاد پرسشنامه
        $post_evaluation->next_must_run_date_time = $post_evaluation->next_run_date();
        $post_evaluation->save();
        $response = [
            'result' => true,
            'message' => 'اطلاعات با موفقیت ثبت گردید.',
        ];

        return $response;
    }
}
