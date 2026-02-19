<?php

namespace App\Http\Controllers\HR\Setting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Bank\Bank;
use App\Models\HR\Employment\EmploymentNotificationSetting;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use Illuminate\Http\Request;
use function Aws\recursive_dir_iterator;

class DashboardController extends Controller
{
    //
    var $route_path = "hr.setting.dashboard.";
    var $view_path = "hr.setting.dashboard.";

    public function index()
    {

        $list = ShiftDeliveryModule::paginate();
        $values = Setting::getValues();
        $bank_list = Bank::all();
        $status_list = Status::where('status_type_id', 4640)->get();
        $notification_setting = EmploymentNotificationSetting::pluck('post_id', 'status_id')->toArray();
        foreach ($status_list as $status) {

            $post_option_list[$status->id] = Option::get("posts", isset($notification_setting[$status->id])?$notification_setting[$status->id]:0);
        }

        $auto_exist_option_algorithm_option=Option::get("auto_exit_option_algorithm",$values["auto_exit_option_algorithm"]->integer_value);

        return view($this->view_path . "index", compact("list", "values", 'bank_list', 'status_list', 'post_option_list', 'auto_exist_option_algorithm_option'));
    }

    public function edit_module(ShiftDeliveryModule $shift_delivery_module)
    {

        return view($this->view_path . "edit_module", compact("shift_delivery_module"));
    }

    public function update_module(Request $request, ShiftDeliveryModule $shift_delivery_module)
    {

        $shift_delivery_module->update($request->all());


        return redirect()->route($this->route_path . "edit_module", $shift_delivery_module)->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);
    }

    public function update_bank(Request $request)
    {

        Bank::where('id', '>', '0')->update(['is_bank_allowed_to_choose' => 0]);

        if (empty($request->is_bank_allowed_to_choose)) {
            return redirect()->back()->withErrors("حداقل یک بانک را انتخاب کنید.");
        }
        $is_bank_allowed_to_chooses = array_keys($request->is_bank_allowed_to_choose);


        Bank::whereIn('id', $is_bank_allowed_to_chooses)->update(['is_bank_allowed_to_choose' => 1]);


        return redirect()->back()->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);

    }




    public function update_employment_notification(Request $request)
    {
        $status_list = Status::where('status_type_id', 4640)->get();

        EmploymentNotificationSetting::whereIn('status_id', $status_list->pluck('id'))->delete();

        foreach ($status_list as $status) {
            $id = "post_id_" . $status->id;
            $post_id = $request->$id ?? null;


            if ($post_id != 0) {
                $notification_setting = new EmploymentNotificationSetting();
                $notification_setting->post_id = $post_id;
                $notification_setting->status_id = $status->id;
                $notification_setting->save();
            }
        }

        return redirect()->back()->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);
    }


}
