<?php

namespace App\Http\Controllers\HR\Employment\Admin\Supplier;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ConfirmDraftInformationController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.supplier.confirm_draft_information.",
        "enable_status" => ["138"],
        "button" => ["caption" => "تایید پیش نویس اطلاعات (تامین کننده)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.supplier.confirm_draft_information.",
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

        $supplier_draft_contract_required_init_confirm = Setting::getIntegerValue("supplier_draft_contract_required_init_confirm");
        $supplier_draft_contract_required_final_confirm = Setting::getIntegerValue("supplier_draft_contract_required_final_confirm");
        $supplier_draft_contract_confirm = Setting::getIntegerValue("supplier_draft_contract_confirm");

        if ($supplier_draft_contract_confirm) {
//تغیر وضعیت ها به صورت زیر
            if ($supplier_draft_contract_required_init_confirm) {
                $employment->status_id = 4640111;//در انتظار تایید اولیه قرارداد هوشمند
            } elseif ($supplier_draft_contract_required_final_confirm) {
                $employment->status_id = 4640112;//در انتظار تایید نهایی قرارداد هوشمند
            } else {
                $employment->status_id = 4640113;//در انتظار تایید قرارداد توسط تامین کننده
                self::SendSmsContractsupplier($employment);
            }
        } else {
            self::CreateCostCenterSupplier($employment);//ثبت مرکز هزینه
        }
        $employment->save();
        event(new EmploymentLogEvent($employment, 4640045));

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " تایید پیش نویس اطلاعات با موفقیت ثبت گردید."]);

    }
    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
    public static function CreateCostCenterSupplier($employment)
    {
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");
        //آیا شرکت نرم افزار مالی مجزا دارد؟مرکز هزینه را طبق این ایجاد می کنیم
        $supplier = $employment->supplier;
        if ($company_have_separate_financial_software || $company_have_separate_warehousing_software) {
            $employment->status_id = 4640114; // در انتظار ثبت مرکز تامین کننده
            $employment->save();
        }
        if (!$company_have_separate_warehousing_software) {

            $cost_center = CostCenter::create([
                "code" => 0,
                "caption" => $employment->supplier->caption,
                "status_id" => 1200,
            ]);
            $cost_center->code = "001" . "/" . $cost_center->id;
            $cost_center->save();
            $supplier->cost_center_id = $cost_center->id;
            $supplier->save();
        }
        if (!$company_have_separate_financial_software && !$company_have_separate_warehousing_software) {
            $cost_center = CostCenter::create([
                "code" => 0,
                "caption" => $employment->supplier->caption,
                "status_id" => 1200,
            ]);
            $cost_center->code = "001" . "/" . $cost_center->id;
            $cost_center->save();
            $supplier->cost_center_id = $cost_center->id;
            $supplier->save();
            Employment::AddUserToPost($employment);

            $supplier_draft_contract_confirm = Setting::getIntegerValue("supplier_draft_contract_confirm");
            $supplier_draft_contract_required_init_confirm = Setting::getIntegerValue("supplier_draft_contract_required_init_confirm");
            $supplier_draft_contract_required_final_confirm = Setting::getIntegerValue("supplier_draft_contract_required_final_confirm");


            if ($supplier_draft_contract_confirm && ($supplier_draft_contract_required_init_confirm || $supplier_draft_contract_required_final_confirm)) {
                Employment::SendSmsNextStatusForPost($employment);
                $employment->status_id = 4640128;//در انتظار تحویل مدارک به بایگانی
            } else {
                $employment->status_id = 4640106;//آغاز همکاری
                $employment->supplier->active_status_id=1200;
                $employment->supplier->save();
            }
            $employment->save();
            event(new EmploymentLogEvent($employment, 4640018));//ثبت مرکز هزینه

        }
    }
    public static function SendSmsContractsupplier(Employment $employment)
    {

        $token10 = $employment->worker->fullname("with_gender_2");
        $token = "در سامانه" . Setting::getStringValue('software_name');
        $token3 = "_APP_NAME_" . "/employment/register";

        Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
            new SMSNotification("employmentconfirmcontracttype6",
                $token,
                $token3,
                $token10,
            ));


    }
}
