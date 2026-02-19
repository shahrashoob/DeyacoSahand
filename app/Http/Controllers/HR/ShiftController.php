<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Imports\ShiftWorkImport;
use App\Models\HR\Shift\ShiftWorkGroupType;
use App\Models\Post\Post;
use App\Models\HR\Shift\NewShiftWorkDay;
use App\Models\HR\Shift\Shift;
use App\Models\HR\Shift\ShiftWorkDay;
use App\Models\HR\Shift\ShiftWorkGroup;
use App\Models\HR\Shift\ShiftWork;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class ShiftController extends Controller
{
    // hr/shift
    var $route_path = "hr.shift.";
    var $view_path = "hr/shift/";

    public function index()
    {
        $list = Shift::paginate(30);

        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {
        $status_option = Option::get("status", 0, 1100);

        $max_shift_work_count = ShiftWork::count();

        return view($this->view_path . "create", compact("status_option", "max_shift_work_count"));

    }

    public function store(Request $request)
    {

        Shift::create($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "یک شیفت با موفقیت اضافه شد."]);

    }

    public function edit(Shift $shift)
    {
        $status_option = Option::get("status", $shift->active_status_id, 1100);

        $max_shift_work_count = ShiftWork::count();

        return view($this->view_path . "edit", compact("shift", "status_option", "max_shift_work_count"));

    }

    public function update(Shift $shift, Request $request)
    {

        $list = Post::where("shift_id", $shift->id)->get();
        if ($request->active_status_id == 1210 && count($list) > 0) {
            $message = "";
            foreach ($list as $item) {
                $message .= "<br/>" . $item->caption;
            }

            return back()->withErrors("با توجه به اینکه شیفت در پست های زیر به کار برده شده است، امکان غیر فعال سازی آن وجود ندارد." . $message);
        }


        $shift->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد."]);

    }

    public function upload_shift_work(Shift $shift)
    {


        $year = jdate(Carbon::parse(Carbon::now())->timestamp)->format('Y');

        $option = [];
        for ($i = $year - 3; $i <= $year + 3; $i++) {
            $item = ["value" => $i, "caption" => $i];
            if ($i == $year) {
                $item["selected"] = "selected";
            }
            $option[] = $item;
        }
        $model = [
            "id" => $shift->id,
            "name" => "shift_work",
            "route" => "hr.shift.submit_shift_work",
            "caption" => "آپلود شیفت های کاری " . $shift->code
        ];

        return view($this->view_path . "upload_shift_work", compact("model", "shift", "option"));
    }

    public function edit_shift_group(Shift $shift)
    {
        $shift_work_group_type_list = ShiftWorkGroupType::get();
        $shift_work_group_type_checked = [];
        for ($i = 1; $i <= count($shift_work_group_type_list); $i++) {
            $shift_work_group_type_checked [$i] = [];
        }
        for ($k = 1; $k <= $shift->number_of_shift_work; $k++) {
            $selected_ids = ShiftWorkGroup::
            where("shift_id", $shift->id)->
            where("shift_work_id", $k)->
            pluck("shift_work_group_type_id", "shift_work_group_type_id")->
            toArray();
            foreach ($selected_ids as $id) {
                $shift_work_group_type_checked[$id][$k] = $k;
            }
        }

        return view($this->view_path . "edit_shift_group", compact("shift", "shift_work_group_type_list", "shift_work_group_type_checked"));
    }

    public function update_shift_group(Request $request, Shift $shift)
    {
        $shift_work_group_type_list = ShiftWorkGroupType::pluck("id", "id");

        for ($k = 1; $k <= $shift->number_of_shift_work; $k++) {
            $checked = false;
            foreach ($shift_work_group_type_list as $shift_work_group_type_id) {
                if (isset($request->data[$shift_work_group_type_id][$k])) {
                    $checked = true;
                }
            }

            if (!$checked) {
                return back()->withErrors("لطفا دسته بندی برای گروه $k را مشخص نمایید. ");
            }
        }

        ShiftWorkGroup::
        where("shift_id", $shift->id)->
        delete();
        for ($k = 1; $k <= $shift->number_of_shift_work; $k++) {
            foreach ($shift_work_group_type_list as $shift_work_group_type_id) {
                if (isset($request->data[$shift_work_group_type_id][$k])) {
                    ShiftWorkGroup::create([
                        "shift_id" => $shift->id,
                        "shift_work_id" => $k,
                        "shift_work_group_type_id" => $shift_work_group_type_id
                    ]);
                }
            }
        }
        return redirect()->route($this->route_path . "index",)->with(["success" => "اطلاعات با موفقیت ثبت گردید"]);
    }

    public function submit_shift_work(Request $request, Shift $shift)
    {
        $SWI = new ShiftWorkImport();
        $SWI->year = $request->year;
        $SWI->shift = $shift;


        Excel::import($SWI, request()->file('file_uploaded'));

        return redirect()->route($this->route_path . "show_shift_work", [$shift,$request->check_avg ?? 0 ]);
    }

    public function show_shift_work(Shift $shift,$check_avg)
    {

        $list = NewShiftWorkDay::where("shift_id", $shift->id)->orderBy("message","desc")->orderBy("day")->orderBy("shift_work_id")->paginate(50);
        $count = NewShiftWorkDay::where("shift_id", $shift->id)->count();
        $error_count = NewShiftWorkDay::where("shift_id", $shift->id)->where("message", "!=", "")->count();
        $legal_working_hours_in_minute = $shift->legal_working_hours_in_minute;
        $error = "";
        $warning = "";
        if($check_avg) { // آیا میانگین ساعت کار قانونی در ماه چک شود یا خیر
            for ($k = 1; $k <= $shift->number_of_shift_work; $k++) {
                $e = $this->checkErrorLegalTime($shift, $k, $legal_working_hours_in_minute);
                if (!$e["result"]) {
                    $error_count++;
                    $error .= $e["error"];
                }
                if (isset($e["warning"])) {
                    $warning .= $e["warning"] . "<br/>";
                }

            }
            if ($error != "") {
                $error .= " ساعت کار قانونی در هر ماه باید برابر باشد با: (تعداد روز های ماه - تعداد روزهای تعطیل رسمی - تعداد جمعه) *" . $legal_working_hours_in_minute . " دقیقه" . "<br/>";
            }
        }
        return view($this->view_path . "show_shift_work", compact("list", "shift", "count", "error_count", "error", "warning"));
    }

    public function confirm_shift_work(Shift $shift)
    {
        $min_date = NewShiftWorkDay::orderBy("day")->first()->datetime;
        $max_date = NewShiftWorkDay::orderByDesc("day")->first()->datetime;
        $list = NewShiftWorkDay::
        select("shift_id", "shift_work_id", "day", "start_datetime", "end_datetime", "datetime", "description", "work_day_type_id", "legal_working_hours_in_minute")->
        get()->
        toArray();

        ShiftWorkDay::
        where("datetime", ">=", $min_date)->
        where("datetime", "<=", $max_date)->
        where("shift_id", $shift->id)->
        delete();
        ShiftWorkDay::insert($list);
        NewShiftWorkDay::where("id", ">", 0)->delete();

        $shift->updated_at = now();
        $shift->save();

        return redirect()->route($this->route_path . "index")->with(["success" => "آپلود با موفقیت انجام شد."]);
    }


    public function calc_entry_log_for_days(Request $request)
    {
       return view($this->view_path."calc_entry_log_for_days");
    }
    public function submit_calc_entry_log_for_days(Request $request)
    {

        if(!$request->days || $request->days > 10 ){
            return  back()->withErrors("لطفا تعداد روز را وارد نمایید و حداکثر  10 روز قابل قبول می باشد.");
        }

       $diff_in_day=Carbon::now()->diffInDays( Carbon::parse($request->start_date));

        for ($k = 0; $k < $request->days; $k++) {

            Script1023Controller::handle(-($diff_in_day+$k));
        }

        return back()->with(["success"=>"محاسبات مجدد برای تمامی پرسل برای ".$request->days." روز انجام شد."]);
    }

    /**
     * @return array
     * چک کردن ساعت کار قانونی در هر ماه
     */
    public function checkErrorLegalTime(Shift $shift, $shift_work_id, $legal_working_hours_in_minute)
    {

        $error = "";

        // فروردین
        $month = 1;

        $month_string =
            [
                "",
                "فروردین",
                "  اردیبهشت",
                "خرداد",
                "تیر",
                "مرداد",
                "شهریور",
                "مهر",
                "آبان",
                "آذر",
                "دی",
                "بهمن",
                "اسفند"
            ];

        // بررسی می کنیم که شیفت در کدام دسته ها قرار دارد
        $shift_work_group_ids = ShiftWorkGroup::where([
            "shift_id" => $shift->id,
            "shift_work_id" => $shift_work_id
        ])->pluck("shift_work_group_type_id", "shift_work_group_type_id")->
        toArray();
        if (count($shift_work_group_ids) == 0) {
            return [
                "result" => false,
                "error" => "برای گروه $shift_work_id" . " هیچ دسته بندی انتخاب نشده است، لطفا اطلاعات دسته بندی های شیفت را تکمیل نمایید."
            ];
        }

        // گروه شیفت های زیر باید با هم بررسی شوند.
        $shift_work_ids = ShiftWorkGroup::where([
            "shift_id" => $shift->id,
        ])->
        whereIn("shift_work_group_type_id", $shift_work_group_ids)->
        pluck("shift_work_id", "shift_work_id")->
        toArray();

// جمع کل ساعت های کاری
        $sum_1 = NewShiftWorkDay::
        where("shift_id", $shift->id)->
        whereIn("shift_work_id", $shift_work_ids)->
        where("month", ">", 0)->
        groupBy("month")->
        SelectRaw("sum(legal_working_hours_in_minute) as sum_1,month")->
        pluck("sum_1", "month")->
        toArray();

        // جمع کل ساعت های بدون تعطیلی
        $sum_2 = NewShiftWorkDay::
        where("shift_id", $shift->id)->
        whereIn("shift_work_id", $shift_work_ids)->
        where("month", ">", 0)->
        whereNotIn("work_day_type_id", [2, 4])->
        groupBy("month")->
        SelectRaw("count(month)* $legal_working_hours_in_minute as sum_2,month")->
        pluck("sum_2", "month")->
        toArray();

//        $shift_work_string="";
//        foreach ($shift_work_ids as $item){
//            $shift_work_string.=$item." ,";
//        }
        $warning = "";
        for ($k = 1; $k <= 12; $k++) {

            if (!isset($sum_1[$k])) {
                $warning .= "<br/>" . $month_string[$k] . "  ماه :" . "جمع کل ساعت های کاری صفر است. ";
                continue;
            }
            if (!isset($sum_2[$k])) {
                $warning .= "<br/>" . $month_string[$k] . "  ماه :" . "جمع کل ساعت های کاری بدون تعطیلی ها صفر است. ";
                continue;
            }
            if ($sum_1[$k] != $sum_2[$k]) {
                $error .= "مجموع ساعت کار قانونی گروه شیفت  " . $shift_work_id . " در " . $month_string[$k] . " ماه " . " نادرست است. " . "<br/>";
                $error .= "جمع کل ساعت های کاری:" . $sum_1[$k] . " , تعداد روز های کاری " . ($sum_2[$k] / $legal_working_hours_in_minute) . "<br/><br/>";
            }
        }
//
        if ($warning != "") {
            $warning = "هشدار در گروه شیفت " . $shift_work_id . $warning . "<br/>";
        }
        return [
            "result" => $error == "" ? true : false,
            "error" => $error,
            "warning" => $warning
        ];
    }
}
