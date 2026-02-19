<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Document\DocumentType;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;

/*
این کنترلر برای تحویل مدارک به باگانی است.
 * */

class DeliveryOfDocumentController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.personal.delivery_of_document.",
        "enable_status" => ["117"],
        "button" => ["caption" => "تایید تحویل مدارک(پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.delivery_of_document.",


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
        $document_type = DocumentType::where('nationality_id', $employment->nationality_id)->orWhereNull('nationality_id')->pluck('id');

     $document_receive_step_document_types = DocumentReceiveStepDocumentType::join('post_document_receive_step_confirms', 'document_receive_step_document_types.receive_document_step_id', 'post_document_receive_step_confirms.receive_document_step_id')->
        where([
            'post_document_receive_step_confirms.is_necessary_to_deliver_document_to_archive' => 1,
            'post_document_receive_step_confirms.post_id' => $employment->post_id,
        ])->
        when($document_type, function ($query, $document_type) {
            return $query->whereIn('document_receive_step_document_types.document_type_id', $document_type);
        })->
        select('document_receive_step_document_types.document_type_id')->
        groupBy('document_receive_step_document_types.document_type_id')->
        get();


        return view($this->view_path . "index", compact('document_receive_step_document_types', 'employment'));

    }

    public function submit(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");// نرم افزار انبار داری
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی
        $company_have_it_unit = Setting::getIntegerValue("company_have_it_unit");// واحد آیتی

        Employment::AddUserToPost($employment);
        if ($company_have_separate_financial_software || $company_have_separate_warehousing_software) {
            $employment->status_id = 4640119;//آغاز همکاری در انتظار تعریف اطلاعات مالی
        }
        if (!$company_have_separate_warehousing_software && $company_have_separate_financial_software) {// نرم افزار انبار داری مرتبط با کد هزینه می باشد.
            self::CreateCostCenter($employment);
            $employment->status_id = 4640119;//آغاز همکاری در انتظار تعریف اطلاعات مالی
        }
        if (!$company_have_separate_financial_software && !$company_have_separate_warehousing_software) {
            self::CreateCostCenter($employment);
            if($company_have_it_unit) {
                $employment->status_id = 4640120;//آغاز همکاری در انتظار دریافت اکانت اینترنت
            }
            else{
                $employment->status_id = 4640106; // آغاز همکاری
            }
        }
        $employment->save();
        Employment::SendSmsNextStatusForPost($employment);
        event(new EmploymentLogEvent($employment, 4640005));
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تحویل مدارک با موفقیت ثبت گردید و فرد مورد نظر در جایگاه پست " ."(". $employment->post->caption .")". " و شیفت "."(".($employment->post->shift->caption??"").")"." قرار گرفت"]);

    }
    public static function CreateCostCenter($employment)
    {
        $worker = $employment->worker;
        $cost_center = CostCenter::create([
            "code" => 0,
            "caption" => $employment->worker->fullname(),
            "status_id" => 1200,
        ]);
        $cost_center->code = "001" . "/" . $cost_center->id;
        $cost_center->save();
        $worker->cost_center_id = $cost_center->id;
        $worker->save();
    }
    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

}
