<?php

namespace App\Http\Controllers\Import;

use App\Exports\HR\LeaveReminderExport;
use App\Exports\Product\ProductPricingSampleFormatExport;
use App\Http\Controllers\Controller;
use App\Imports\LeaveReminderImport;
use App\Imports\Product\ProductPricingImport;
use App\Models\HR\LeaveOvertime\ImportLeaveReminder;
use App\Models\HR\LeaveOvertime\LeaveRemainder;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Import\ImportProductPricing;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LeaveReminderController extends Controller
{
    var $route_path = "import.leave_reminder.";
    var $view_path = "import.leave_reminder.";

    public function index()
    {

        $model = ["name" => "leave_reminder", "route" => "import.leave_reminder.upload", "caption" => " مانده مرخصی پرسنل "];
        return view("import/index", compact("model"));

    }

    public function upload()
    {

        Excel::import(new LeaveReminderImport(), request()->file('file_uploaded'));

        return redirect()->route($this->route_path . "show")->with(["success" => "آپلود با موفقیت انجام شده، در صورت تایید لیست مانده مرخصی برای سال مالی انتخاب شده بروز می شود."]);

    }

    public function show()
    {

        $list = ImportLeaveReminder::orderBy("error", "desc")->paginate(50);

        $error_count = ImportLeaveReminder::where("error", "!=", "")->count();

        return view("import/hr/leave_reminder", compact("list", "error_count"));

    }

    public function update()
    {

        $list = ImportLeaveReminder::select("user_id", "year", "leave_type_id","start_date","end_date","leave_in_start","leave_in_end","leave_remainder")->get()->toArray();



        foreach ($list as $item) {
            LeaveRemainder::where(["year"=>$item["year"], "user_id"=>$item["user_id"]])->delete();
        }

        LeaveRemainder::insert($list);


        foreach ( ImportLeaveReminder::all() as $item) {
            LeaveRemainder::UpdateLeaveReminder($item->worker);
        }


        return redirect()->route($this->route_path . "index")->with(["success" => "لیست مرخصی ها با موفقیت بروزرسانی گردید."]);
    }

    public function get_sampling_file()
    {


        $export                = new LeaveReminderExport();


        return Excel::download( $export, 'leave_reminder_for_all_user' . '.xlsx' );


    }
}

