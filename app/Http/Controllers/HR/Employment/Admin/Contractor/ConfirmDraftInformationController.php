<?php

namespace App\Http\Controllers\HR\Employment\Admin\Contractor;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class ConfirmDraftInformationController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.contractor.confirm_draft_information.",
        "enable_status" => ["137"],
        "button" => ["caption" => "تایید پیش نویس اطلاعات (پیمانکار)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.contractor.confirm_draft_information.",
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
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return view($this->view_path . "index", compact("employment"));
    }

    public function submit(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $contractor_draft_contract_required_init_confirm = Setting::getIntegerValue("contractor_draft_contract_required_init_confirm");
        $contractor_draft_contract_required_final_confirm = Setting::getIntegerValue("contractor_draft_contract_required_final_confirm");
        $contractor_draft_contract_confirm = Setting::getIntegerValue("contractor_draft_contract_confirm");

        if ($contractor_draft_contract_confirm) {
            //تغیر وضعیت ها به صورت زیر
            if ($contractor_draft_contract_required_init_confirm) {
                $employment->status_id = 4640130;//در انتظار تایید اولیه قرارداد هوشمند
            } elseif ($contractor_draft_contract_required_final_confirm) {
                $employment->status_id = 4640131;//در انتظار تایید نهایی قرارداد هوشمند
            } else {
                $employment->status_id = 4640132;//در انتظار تایید قرارداد توسط پیمانکار
            }
        } else {
            self::CreateCostCenterContractor($employment);//ثبت مرکز هزینه
        }
        $employment->save();
        event(new EmploymentLogEvent($employment, 4640044));
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " تایید پیش نویس اطلاعات با موفقیت ثبت گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

    public static function CreateCostCenterContractor($employment)
    {
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");

        //آیا شرکت نرم افزار مالی مجزا دارد؟مرکز هزینه را طبق این ایجاد می کنیم

        if ($company_have_separate_warehousing_software) {
            $employment->status_id = 4640133; // در انتظار ثبت مرکز پیمانکار
        }
        if (!$company_have_separate_warehousing_software) {
            $cost_center = CostCenter::create([
                "code" => 0,
                "caption" => $employment->contractor->caption,
                "status_id" => 1200,
            ]);
            $cost_center->code = "001" . "/" . $cost_center->id;
            $cost_center->save();
            $contractor = $employment->contractor;
            $contractor->cost_center_id = $cost_center->id;
            $contractor->save();

            Employment::AddUserToPost($employment);

            $contractor_draft_contract_confirm = Setting::getIntegerValue("contractor_draft_contract_confirm");
            $contractor_draft_contract_required_init_confirm = Setting::getIntegerValue("contractor_draft_contract_required_init_confirm");
            $contractor_draft_contract_required_final_confirm = Setting::getIntegerValue("contractor_draft_contract_required_final_confirm");


            if ($contractor_draft_contract_confirm && ($contractor_draft_contract_required_init_confirm || $contractor_draft_contract_required_final_confirm)) {
                Employment::SendSmsNextStatusForPost($employment);
                $employment->status_id = 4640134;//در انتظار تحویل مدارک به بایگانی
            } else {
                $employment->status_id = 4640106;//آغاز همکاری
                $employment->contractor->active_status_id=1200;
                $employment->contractor->save();
            }
            $employment->save();
            event(new EmploymentLogEvent($employment, 4640038));//ثبت مرکز هزینه

        }
    }
}
