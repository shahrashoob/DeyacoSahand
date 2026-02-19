<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Personal\AbsenceController;
use App\Http\Controllers\HR\Personal\LeaveController;
use App\Http\Controllers\HR\Personal\MissionController;
use App\Http\Controllers\HR\Personal\OvertimeController;
use App\Http\Controllers\HR\Personal\ReplacementController;
use App\Http\Controllers\HR\Personal\ShiftWorkDayController;
use App\Http\Controllers\HR\ShiftDelivery\IlegalDeliveryModuleController;
use App\Models\HR\LeaveOvertime\LeaveRemainder;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Post\PostSmartObject;
use App\Models\Post\PostUser;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\HR\User\UserEntryLog;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use App\Models\Utility\SmartObjectType;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PersonalController extends Controller
{
    //
    var $view_path = "hr.personal.";
    public static $perfix_status_code = "4620";

    public function index(Worker $worker, $key, $back_url = "", $timestamp = 0)
    {

        if ($worker->random != $key) {
            return back()->withErrors("صفحه مورد نظر یافت نشد.");
        }

        // آدرس بازگشت را در سشن ذخیره می کنیم و اگر مقدار متغیر بازگشت خالی بود از سشن استفاده می کنیم.
        if ($back_url == "" && session("personal_back_url")) {
            $back_url = session("personal_back_url");
        }

        session(["personal_back_url" => $back_url]);


        $timestamp = Carbon::now()->timestamp;
        $controller_info = PersonalController::get_controller_info();
        $special_condition = PersonalController::special_condition($worker);
        $static_ip = "http://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("Personal", [$worker, $worker->getRandom(), $timestamp]);

        $allow_show_complete_data=false;
        $post_user = Auth::user()->posts->first();
        if (Auth::id()==$worker->id ||  WorkerController::AllowUserShow($post_user, $worker->id)) {
            $allow_show_complete_data=true;
        }


        $qr =$allow_show_complete_data? QrCode::size(200)->generate($url):"";
        $leave_list = [];
        $replacement_list = [];
        $leave_waiting_confirm = [];
        $absence_waiting_confirm = [];
        $leave_waiting_confirm_post = [];
        $replacement_waiting_confirm = [];
        $post_user_list = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker);

        $show_btn_delivery = false; // آیا فرد می تواند دکمه تحویل شیفت را ببیند
        $show_btn_confirm_posts = []; // در صورتیکه ماشینی تحویل فرد است، دکمه تایید تحویل شیفت نمایش داده می شود.
        $worker_id_equal_auth_id = Auth::id() == $worker->id;
        $illegal_delivery_ids = [];
        if ($worker_id_equal_auth_id) {


            // بررسی انیکه آیا هیچ ماشینی تحویل فرد هست یا خیر
            $count = ShiftDeliveryModule::getMachineListWhereWorkerIsOperator($worker, "count", "post_ids", "leaves_user_ids");
            if ($count != 0) {
                $show_btn_delivery = 1;
            }

            // بررسی اینکه آیا دکمه تایید تحویل شیفت نمایش داده شود یا خیر
            $count = ShiftDeliveryModule::getMachineLogWhereWorkerIsOperatorByEventType($worker, 650, "count");
            if ($count != 0) {
                $show_btn_confirm_posts [1] = 1;
            }

            // لیست ماشین هایی که تحویل یک فرد است، ولی نباید تحویل او باشد.
            $illegal_delivery_ids = IlegalDeliveryModuleController::illegal_machine_ids($worker);
            //  $illegal_delivery_machine = Machine::whereIn( "id", $illegal_delivery_ids )->get();

            // لیست درخواست های  مرخصی در انتظار تایید جانشین
            $leave_waiting_confirm = LeaveOvertime::
            join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
            join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
            where("replace_user_id", $worker->id)->
            where("leave_overtime_confirmation.status_id", 4630001)->
            where("leave_overtimes.status_id", 4630001)->
            whereIn("leave_overtime_group_id", [1,3])->
            orderByDesc("leave_overtimes.id")->
            select("leave_overtimes.*")->
            groupBy("leave_overtime_id")->
            paginate(5,["*"]);

            // لیست درخواست های جایگزینی در انتظار تایید جانشین
            $replacement_waiting_confirm = LeaveOvertime::
            join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
            join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
            where("replace_user_id", $worker->id)->
            where("leave_overtime_confirmation.status_id", 4630001)->
            where("leave_overtimes.status_id", 4630001)->
            where("leave_overtime_group_id", 4)->
            orderByDesc("leave_overtimes.id")->
            select("leave_overtimes.*")->
            groupBy("leave_overtime_id")->
            paginate(5);

            // لیست درخواست های در انتظار تایید مافوق
            $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids_with_out_menu", $worker);
            $leave_waiting_confirm_post = LeaveOvertime::
            join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
            whereIn("current_confirm_post_id", $post_ids)->
            where("leave_overtime_confirmation.status_id", 4630002)->
            where("leave_overtimes.status_id", 4630002)->
            orderByDesc("leave_overtimes.id")->
            select("leave_overtimes.*")->
            groupBy("leave_overtime_id")->
            paginate(5);


            // لیست درخواست های غیبت در انتظار تایید جانشین
            $absence_waiting_confirm = LeaveOvertime::
            join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
            join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
            where("replace_user_id", $worker->id)->
            where("leave_overtime_confirmation.status_id", 4630001)->
            where("leave_overtimes.status_id", 4630001)->
            where("leave_overtime_group_id", 5)->
            orderByDesc("leave_overtimes.id")->
            select("leave_overtimes.*")->
            groupBy("leave_overtime_id")->
            paginate(5);


        }


        // لیست همه درخواست های مرخصی
        $leave_list = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 1)->//مرخصی
        select("leave_overtimes.*")->
        orderByDesc("leave_overtimes.id")->paginate(5,["*"],"leave_page");


        // لیست همه درخواست های جانشینی
        $replacement_list = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 4)->//جانشینی
        select("leave_overtimes.*")->
        orderByDesc("leave_overtimes.id")->paginate(5,["*"],"replacement_page");

        // لیست همه درخواست های جانشینی غیبت
        $absence_list = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 5)->//غیبت
        select("leave_overtimes.*")->
        orderByDesc("leave_overtimes.id")->paginate(5,["*"],"absence_page");

        // لیست همه درخواست های اضافه کاری
        $overtime_list = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 2)->//اضافه کاری
        select("leave_overtimes.*")->
        orderByDesc("leave_overtimes.id")->paginate(5,["*"],"overtime_page");
        // لیست همه درخواست های ماموریت کاری
        $mission_list = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 3)->//ماموریت کاری
        select("leave_overtimes.*")->
        orderByDesc("leave_overtimes.id")->paginate(5,["*"],"mission_page");

        $list = UserEntryLog::where("user_id", $worker->id)->orderByDesc("id")->paginate(5);


        // Personal Time

        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, 0, 0);
        $shift_work_query->where("start_datetime", ">=", Carbon::now()->format("Y/m/d"))->
        where("end_datetime", "<=", Carbon::now()->addDay()->format("Y/m/d"));
        $shift_work_list = $shift_work_query->get();

        // لیست همه شیفت پست های جاری فرد
        $current_post_users = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_id_shift_work_ids", $worker);


        // نمایش مانده مرخصی
        $leave_reminder[1]=LeaveRemainder::getLeaveReminder($worker);

        return view($this->view_path . "index", compact("worker", "show_btn_delivery", "worker_id_equal_auth_id",
                "show_btn_confirm_posts", "post_user_list", "controller_info", "overtime_list", "mission_list", "shift_work_list", "replacement_list",
                "back_url", "qr", "list", "leave_list", "leave_waiting_confirm", "absence_waiting_confirm", "replacement_waiting_confirm",
                "leave_waiting_confirm_post", "special_condition", "current_post_users", "illegal_delivery_ids", "absence_list","leave_reminder",
            )
        );

    }

    public function index_qr(Worker $worker, $key, $timestamp = 0)
    {
        return $this->index($worker, $key, "", $timestamp);
    }

    public function current_user()
    {
        $worker = Worker::find(Auth::id());
        if (!$worker) {
            return back()->withErrors("صفحه مورد نظر یافت نشد.");
        }

        return $this->index($worker, $worker->random);
    }

    public static function get_controller_info()
    {
        return $controller_info = [
            "01" => Personal\ConfirmEntryController::$info,
            "02" => Personal\ConfirmExitController::$info,
            "03" => Personal\LeaveController::$info,
            "04" => Personal\ConfirmIllegalExistController::$info,
            "05" => Personal\OvertimeController::$info,
            "06" => Personal\MissionController::$info,
            "07" => Personal\ReplacementController::$info,
            "08" => Personal\UserDeviceController::$info,
            "09" => Personal\StartRemoteWorkController::$info,
            "10" => Personal\EndRemoteWorkController::$info,
        ];
    }

    public static function special_condition($worker)
    {
        $auth_id = Auth::id();

        return $controller_info = [
            "01" => $auth_id != $worker->id,
            "02" => $auth_id != $worker->id,
            "03" => $auth_id == $worker->id,
            "04" => $auth_id != $worker->id,
            "05" => $auth_id == $worker->id,
            "06" => $auth_id == $worker->id,
            "09" => $auth_id == $worker->id,
            "10" => $auth_id == $worker->id,
        ];
    }

    public static function checkPermissionConditions(Worker $worker, $info = false, $all_status = false)
    {
        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = PersonalController::$perfix_status_code . $value;
            }
            unset($value);
            if (!$all_status && !in_array($worker->status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت شاغل جهت عملیات نامعتبر است",
                    "error_type" => "for_machine_status"
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index", false, true)) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];
    }

    public function confirm_parent_post(Request $request)
    {

        $leave_overtime = LeaveOvertime::find($request->leave_overtime_id);
        if (!$leave_overtime) {
            return back()->withErrors("شناسه مرخصی/ماموریت/اضافه کاری نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }

        switch ($leave_overtime->leave_overtime_type->leave_overtime_group_id) {
            case 1:
                $leaveController = new LeaveController();

                return $leaveController->confirm_parent_post($request);
            case 2:
                $overtimeController = new OvertimeController();

                return $overtimeController->confirm_parent_post($request);
                break;
            case 3:
                $missionController = new MissionController();

                return $missionController->confirm_parent_post($request);
                break;
            case 4:
                $replacementController = new ReplacementController();

                return $replacementController->confirm_parent_post($request);
                break;
            case 5:
                $absenceController = new AbsenceController();

                return $absenceController->confirm_parent_post($request);
                break;
        }

        //return back()->withErrors( "این امکان پیاده سازی نشده است، لطفا با پشتیبانی تماس بگیرید." );
    }

    public function set_user_comment(Request $request)
    {

        $leave_overtime = LeaveOvertime::find($request->leave_overtime_id);
        if (!$leave_overtime) {
            return back()->withErrors("شناسه مرخصی/ماموریت/اضافه کاری نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }
        switch ($leave_overtime->leave_overtime_type->leave_overtime_group_id) {
            case 1:
                $leaveController = new LeaveController();

                return $leaveController->set_user_comment($request);
            case 2:
                $overtimeController = new OvertimeController();

                return $overtimeController->set_user_comment($request);
                break;
            case 3:
                $missionController = new MissionController();

                return $missionController->set_user_comment($request);
                break;
            case 4:
                $replacementController = new ReplacementController();

                return $replacementController->set_user_comment($request);
                break;
        }

        return back()->withErrors("این امکان پیاده سازی نشده است، لطفا با پشتیبانی تماس بگیرید.");
    }


    public function select_smart_object(SmartObjectType $smart_object_type, $back_route = "dashboard", $param1 = 0)
    {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $smart_object_list = PostSmartObject::whereIn("post_id", $post_ids)->pluck("smart_object_id")->toArray();
        $smart_object_list[]=-1;
        $smart_object_option=Option::get("smart_object_post",0,$smart_object_type->id,$smart_object_list);
        return view($this->view_path . "select_smart_object", compact(["smart_object_option", "back_route", "param1","smart_object_type"]));
    }

    public function submit_select_smart_object(Request $request,SmartObjectType $smart_object_type)
    {

        if (!$request->smart_object_id) {
            return back()->withErrors("لطفا یک شیء هوشمند را انتخاب نمایید.");
        }

        session(["default_smart_object_id" => $request->smart_object_id]);

        if ($request->back_route) {
            return redirect()->route($request->back_route, $request->param1)->with(["success" =>$smart_object_type->caption. " پیش فرض با موفقیت ثبت گردید"]);
        }
    }

}
