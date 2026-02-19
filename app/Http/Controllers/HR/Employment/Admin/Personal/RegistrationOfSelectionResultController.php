<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentSelection;
use App\Models\HR\Employment\EmploymentSelectionIndicatorValue;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Post\PostUser;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
این کنترلر برای ثبت نتیجه گزینش است
 * */

class RegistrationOfSelectionResultController extends Controller
{

    public static $info = [
        "route" => "hr.employment.admin.personal.registration_of_selection_result.",
        "enable_status" => ["103"],
        "button" => ["caption" => "ثبت نتیجه گزینش (پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.registration_of_selection_result.",

    ];
    var $view_path;
    var $route_path;
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }


    public function index(Employment $employment)
    {
        $employment_selection = $employment->employment_selections()->
        where([
            'priority_number' => $employment->current_priority_number,
            'status_id' => 4640103 // در انتظار انجام
        ])->
        first();

        if (!$employment_selection) {
            return back()->withErrors("اطلاعات گزینش مورد نظر یافت نشد، لطفا با پشتیبانی تماس بگرید.");
        }
        $result = $this->checkPermission($employment, $employment_selection);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if (count($employment_selection->selection->selection_indicators) == 0) {
            return back()->withErrors("شاخص های ارزیابی برای " . $employment_selection->selection->caption . " مشخص نشده است، لطفا با واحد منابع انسانی تماس بگیرید.");
        }
        $allow_contrac_detail = false;
        $allow_to_show_contract_details_in_employment_selection = $employment->employment_selections()->
        whereIn('status_id', [4640102, 4640103, 4640101])->
        count();
        if ($allow_to_show_contract_details_in_employment_selection == 1) {
            $allow_contrac_detail = true;
        }
        $absorption_type_option = Option::get("absorption_type");


        return view($this->view_path . "index", compact('employment', 'employment_selection', 'allow_contrac_detail', 'absorption_type_option'));

    }

    public function submit(Request $request, Employment $employment)
    {
        $employment_selection = $employment->employment_selections()->
        where([
            'priority_number' => $employment->current_priority_number,
            'status_id' => 4640103 // در انتظار انجام
        ])->
        first();

        if (!$employment_selection) {
            return back()->withErrors("اطلاعات گزینش مورد نظر یافت نشد، لطفا با پشتیبانی تماس بگرید.");
        }
        $result = $this->checkPermission($employment, $employment_selection);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $selection_selector = SelectionSelector::
        where([
            "selection_id" => $employment_selection->selection_id,
            "post_id" => $employment_selection->employment->post_id,
        ])->
        first();
        if (!$selection_selector) {
            return back()->withErrors("گزینش " . $employment_selection->selection->caption . " برای " . $employment_selection->employment->post->caption . " در سامانه تعریف نشده است، لطفا با واحد منابع انسانی تماس بگیرید.");
        }
        $rules = [];
        $sum_weight = 0;
        $sum_value = 0;

        // بررسی درست بودن امتیاز ها
        foreach ($employment_selection->selection->selection_indicators as $item) {

            $rules["value_$item->id"] = 'required|numeric|min:0|max:100';
            $this->validate($request, $rules, ['*.*' => 'مقدار امتیاز باید بین صفر تا صد باشد.']);
        }

        foreach ($employment_selection->selection->selection_indicators as $item) {

            $employment_selection_indicator_value = EmploymentSelectionIndicatorValue::create([
                'employment_id' => $employment->id,
                'selection_id' => $item->selection_id,
                'post_id' => $employment->post_id,
                'value' => $request->input("value_$item->id"),
                'selection_indicator_id' => $item->id,
                'weight' => $item->weight,
                "employment_selection_id" => $employment_selection->id,
            ]);

            $sum_value += $employment_selection_indicator_value->value * $employment_selection_indicator_value->weight;
            $sum_weight += $employment_selection_indicator_value->weight;
        }

        if ($sum_weight <= 0) {
            return back()->withErrors("وزن شاخص نمی تواند صفر یا کمتر از صفر باشد.لطفا با پشتیبانی تماس بگیرید.");
        }

        $employment_selection->score_obtained_to_confirm_selection = $sum_value / $sum_weight;
        $employment_selection->user_id = Auth::user()->id;
        $employment_selection->save();


        if (!$selection_selector) {
            return back()->withErrors("'گزینش کننده ای برای پست ایجاد نشده است. لطفا با پشتیبانی تماس بگیرید.");
        }

        //در صورتی که نمره کسب شده بیشتر باشد وضعیت گزینش انجام شده می شود.
        $value_is_valid = $employment_selection->score_obtained_to_confirm_selection >= $employment_selection->minimum_score_to_confirm_selection;


        if ($selection_selector->confirmation_is_required == 1 && !$value_is_valid) {//اگر رد با تایید گزینش کننده الزامی بود و امتیاز کسب نکرده بود

            $employment->status_id = 4640105;//عدم تایید
            $employment->save();

            $employment_selection->status_id = 4640105;  // عدم تایید
            $employment_selection->save();
            event(new EmploymentLogEvent($employment, 4640004, null, $employment_selection->id));
            return redirect()->route($this->dashboard_path . "view", $employment)->withError(
                "با توجه به اینکه حداقل امتیاز برای " .
                $employment_selection->selection->caption .
                " کسب نشده است، درخواست همکاری خاتمه یافته گردید."
            );
        }


        // گزینش انجام شده
        $employment_selection->status_id = 4640104;  // انجام شده
        $employment_selection->save();

        //اگر گزینشی با اولویت بالاتر وجود داشت که وضعیت ان شروع نشده/در انتظار انجام بود
        $no_started_employment_selection = $employment->employment_selections()->
        whereIn("status_id", [4640101, 4640102, 4640103])-> // شروع نشده/هماهنگی/ در انتظار انجام
        where('priority_number', '>=', $employment->current_priority_number)->
        orderBy('priority_number')->
        first();

        if ($no_started_employment_selection) {
            if ($no_started_employment_selection->coordination_time == null) {

                $employment->current_priority_number = $no_started_employment_selection->priority_number;
                $employment->status_id = 4640102; // در انتظارهماهنگی

                $no_started_employment_selection->status_id = 4640102; // در انتظار هماهنگی
                $no_started_employment_selection->save();
                Employment::SendSmsNextStatusForPost($employment);

            } else {

                $employment->current_priority_number = $no_started_employment_selection->priority_number;
                $employment->status_id = 4640103; // در انتظار انجام

                $no_started_employment_selection->status_id = 4640103; // در انتظار انجام
                $no_started_employment_selection->save();
                Employment::SendSmsNextStatusForPost($employment);
            }
        } else {
            //در صورتی که امتیاز کسب شده بیشتر باشدو اولیت بالاتر وجود نداشته باشد  وضعیت همکاری در انتظار بارگزاری مدارک می شود در صورتی که داکیومنتی وجود داشته باشد

            $post_document_recieve_step = PostDocumentReceiveStepConfirm::
            where(['post_id' => $employment->post_id,
                "confirm_type" => 2,
            ])->exists();
            if ($employment->was_any_info_in_ic == 1) {//اگر اطلاعات قبلا در ای سی وجود داشت
                if ($request->absorption_type_id == 1) {
                    $employment->status_id = 4640118; // در انتظارتایید طب کار
                } else {
                    $employment->status_id = 4640121; // در انتظارثبت اطلاعات بانکی

                }
            } else if ($post_document_recieve_step) {
                $employment->status_id = 4640107; // بارگزاری مدارک
            } else {
                if ($request->absorption_type_id == 1) {
                    $employment->status_id = 4640118; // در انتظارتایید طب کار
                } else {
                    $employment->status_id = 4640121; // در انتظارثبت اطلاعات بانکی

                }
                $employment->save();

            }

        }
        //در اخرین گزینش مشخصات قرارداد را میگیریم

        $employment->worker->absorption_type_id = $request->absorption_type_id;
        $employment->worker->right_to_work = $request->right_to_work;
        $employment->worker->start_date_of_contract = $request->start_date_of_contract;
        $employment->worker->end_date_of_contract = $request->end_date_of_contract;


        $employment->worker->save();
        $employment->save();


        event(new EmploymentLogEvent($employment, 4640004, $request->message, $employment_selection->id));

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" =>
            "نتیجه " .
            $employment_selection->selection->caption .
            " با موفقیت ثبت گردید."
        ]);
    }

    public function checkPermission(Employment $employment, EmploymentSelection $employment_selection)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        if (!$result["result"]) {
            return $result;
        }
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        // بررسی اینکه پست مربوطه اقدام به انجام ثبت نتیجه گزینش کند
        if (self::get_special_condition($employment_selection, $post_ids)) {
            return [
                "result" => true
            ];
        }

        return [
            "result" => false,
            "error" => "پست سازمانی شما جهت انجام گزینش نامعتبر است"
        ];
    }

    public static function get_special_condition($employment_selection, $post_ids)
    {


        $list = $employment_selection->employment_selection_selectors()->whereNotNull("post_id")->pluck("post_id");
        foreach ($list as $post_id) {
            if (in_array($post_id, $post_ids)) {
                return true;
            }
        }
        return false;
    }
}
