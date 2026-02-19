<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\HR\Evaluation\EvaluationType;
use App\Models\Post\Evaluation\PostEvaluation;
use App\Models\Post\Evaluation\PostEvaluationIndicator;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class PostEvaluationController extends Controller
{
    private $view_path = "hr.post.post_evaluation.";
    private $route_path = "hr.post.post_evaluation.";

    //نمایش لیست تنظیمات ارزیابی عملکرد
    public function index(Post $post)
    {

        $evaluation_type_list = EvaluationType::all();
        $post_evaluation_list = PostEvaluation:: where('post_id', $post->id)->get()->keyBy('evaluation_type_id');
        return view($this->view_path . "index", compact('post', 'evaluation_type_list', 'post_evaluation_list'));
    }

    //نمایش ویرایش تنظیمات ارزیابی عملکرد
    public function edit(Post $post, EvaluationType $evaluation_type)
    {

        //در صورتی که نوع ارزیابی  بالا به پایین باشد می تواند به صفحه ویرایش برود در غیر این صورت پیام در دست پیاده سازی برای ان نمایش می دهد.
        if ($evaluation_type->id == 2) {
            $post_evaluation = PostEvaluation::
            where('post_id', $post->id)->
            where('evaluation_type_id', $evaluation_type->id)->
            first();
            //در صورتی که قبلا هیچ فیلد با این پست و این نوع ارزیابی نبود ایجاد می شود
            if (!$post_evaluation) {
                $post_evaluation = PostEvaluation::create([
                    'evaluation_type_id' => $evaluation_type->id,
                    'post_id' => $post->id,
                    'cron' => '0 0 1 * *',
                    'time_of_complete' => 24,
                    'active_status_id' => 1210,
                    'last_run_date_time' => null,
                    'next_must_run_date_time' => null,
                ]);
            }

            $next_run_date = $post_evaluation->next_run_date();
            $post_evaluation->next_must_run_date_time = $next_run_date;
            $post_evaluation->save();

            $active_status_option = Option::get("status", $post_evaluation->active_status_id, 1100);
            return view($this->view_path . "edit", compact('active_status_option', 'post_evaluation', 'post'));
        } else {
            return back()->withErrors("این نوع ارزیابی در دست پیاده سازی می باشد.");
        }
    }

//ویرایش تنظیمات ارزیابی عملکرد
    public function update(PostEvaluation $post_evaluation, Request $request)
    {
        $sum_weight = $post_evaluation->post_evaluation_indicators()->sum('weight');
        if ($sum_weight != 100) {
            return back()->withErrors("مجموع وزن‌های شاخص‌ها این پست کمتر از 100 می‌باشد.");
        }
        $post_evaluation->cron = $request->cron;
        $post_evaluation->time_of_complete = $request->time_of_complete;
        $post_evaluation->active_status_id = $request->active_status_id;
        $post_evaluation->save();

        $next_run_date = $post_evaluation->next_run_date();
        $post_evaluation->next_must_run_date_time = $next_run_date;
        $post_evaluation->save();

        return redirect()->route($this->route_path . "index", [$post_evaluation->post_id, $post_evaluation->evaluation_type_id])->with(["success" => "اطلاعات با موفقیت ذخیره شد."]);
    }

//نمایش لسیت شاخص ها
    public function add_indicator(PostEvaluation $post_evaluation)
    {

        $post_evaluation_indicators = PostEvaluationIndicator::  where('post_id', $post_evaluation->post->id)->
        where('evaluation_type_id', $post_evaluation->evaluation_type->id)->
        get();

        $evaluation_indicator_option = Option::get("evaluation_indicator");
        return view($this->view_path . "add_indicator", compact('post_evaluation', 'evaluation_indicator_option', 'post_evaluation_indicators'));
    }

    //افزودن شاخص برای پست
    public function store_indicator(PostEvaluation $post_evaluation, Request $request)
    {

        $request->validate([
            'weight' => ['required'],
            "priority_number" => ['required'],
            "evaluation_indicator_id" => ['required'],

        ]);

        $exsit = PostEvaluationIndicator::where("evaluation_indicator_id", $request->evaluation_indicator_id)->
        where("post_id", $post_evaluation->post_id)->
        where("evaluation_type_id", $post_evaluation->evaluation_type_id)->exists();

        if ($exsit) {
            return back()->withErrors("شاخص مورد نظر تکراری می باشد.");
        }

        $sum_weight = $post_evaluation->post_evaluation_indicators()->sum('weight');
        $new_sum = $sum_weight + $request->weight;
        if ($new_sum > 100) {
            return back()->withErrors("جمع وزن‌های شاخص‌ها نمی‌تواند بیشتر از 100 باشد.");
        }

        $post_evaluation_indicator = PostEvaluationIndicator::create([
            'post_id' => $post_evaluation->post_id,
            'evaluation_type_id' => $post_evaluation->evaluation_type_id,
            "evaluation_indicator_id" => $request->evaluation_indicator_id,
            "weight" => $request->weight,
            "priority_number" => $request->priority_number,
            "post_evaluation_id" => $post_evaluation->id,


        ]);


        $post_evaluation_indicators = PostEvaluationIndicator::where([
            'post_id' => $post_evaluation->post_id,
            "evaluation_type_id" => $post_evaluation->evaluation_type_id,
        ])->get();
        $sum_weight = $post_evaluation_indicators->sum('weight');
        if ($sum_weight == 100) {
            $post_evaluation->active_status_id = 1200;
        } else {
            $post_evaluation->active_status_id = 1210;
        }
        $post_evaluation->save();

        return redirect()->route($this->route_path . "add_indicator", $post_evaluation)->with(["success" => "یک شاخص برای پست " . $post_evaluation->post->caption . "با موفقیت اضافه شد."]);
    }

    //حذف شاخص برای پست
    public function destroy_indicator(PostEvaluation $post_evaluation, PostEvaluationIndicator $post_evaluation_indicator)
    {
        if ($post_evaluation->post_id != $post_evaluation_indicator->post_id) {
            return back()->withErrors("اطلاعات شاخص جهت حذف نامعتبر است.");
        }

        $post_evaluation_indicator->delete();

        $post_evaluation_indicators = PostEvaluationIndicator::where([
            'post_id' => $post_evaluation->post_id,
            "evaluation_type_id" => $post_evaluation->evaluation_type_id,
        ])->get();
        $sum_weight = $post_evaluation_indicators->sum('weight');
        if ($sum_weight == 100) {
            $post_evaluation->active_status_id = 1200;
        } else {
            $post_evaluation->active_status_id = 1210;
        }
        $post_evaluation->save();
        return redirect()->route($this->route_path . 'add_indicator', $post_evaluation)->with(["success" => "یک  شاخص با موفقیت حذف گردید."]);

    }
}
