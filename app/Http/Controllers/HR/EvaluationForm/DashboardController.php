<?php

namespace App\Http\Controllers\HR\EvaluationForm;

use App\Events\HR\EvaluationFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormExport;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormIndicator;
use App\Models\Post\PostUser;
use App\Models\Utility\Message;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $view_path = "hr.evaluation_form.dashboard.";
    private $route_path = "hr.evaluation_form.dashboard.";

    public function index()
    {
        $worker = Worker::find(Auth::id());
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $worker);
        $post_user_all[] = -1;
        $list = EvaluationFormExport::
        join("evaluation_forms", "evaluation_forms.id", "evaluation_form_id")->
        where("status_id", 4650001)->
        whereIn("evaluation_form_exports.post_id", $post_user_all)->
        paginate();

        return view($this->view_path . "index", compact('list'));
    }

//تابع نمایش شاخص
    public function confirm_indicator(EvaluationFormExport $evaluation_form_export)
    {

        if ($evaluation_form_export->evaluation_form->status_id != 4650001) {
            return redirect()->route($this->route_path . "index", $evaluation_form_export)->withErrors("این ارزیابی قبلا انجام شده است.");
        }
        return view($this->view_path . "confirm_indicator", compact('evaluation_form_export'));
    }

//تابع ذخیره نمرات شاخص
    public function store_indicator(EvaluationFormExport $evaluation_form_export, Request $request)
    {
        if ($evaluation_form_export->evaluation_form->status_id != 4650001) {
            return redirect()->route($this->route_path . "index", $evaluation_form_export)->withErrors("این ارزیابی قبلا انجام شده است.");
        }


        $rules = [];
        $sum_weight = 0;
        $sum_value = 0;


        foreach ($evaluation_form_export->evaluation_form->evaluation_form_indicators as $item) {
            $rules["value_$item->id"] = 'required|numeric|min:0|max:100';
            $this->validate($request, $rules, ['*.*' => 'مقدار شاخص باید بین صفر تا صد باشد.']);

            if ($request->input("message_id_$item->id") != "") {
                $msg = Message::create(
                    [
                        "text" => $request->input("message_id_$item->id"),
                        "other_id" => $item->id,
                        "message_type_id" => 310
                    ]
                );
                $item->message_id = $msg->id;
            }


            $item->value = $request->input("value_$item->id");
            $sum_value += $item->value * $item->weight;
            $sum_weight += $item->weight;
            $item->save();

        }

        $evaluation_form_export->evaluation_form->value_evaluation_form = $sum_value / $sum_weight;


        $evaluation_form_export->evaluation_form->status_id = 4650002; // ارزیابی شده
        $evaluation_form_export->evaluation_form->save();


        event(new EvaluationFormLogEvent($evaluation_form_export->evaluation_form, 4650002));

        return redirect()->route($this->route_path . "confirm_indicator", $evaluation_form_export)->with(["success" => "فرم ارزیابی  شما با موفقیت ثبت گردید."]);

    }
}
