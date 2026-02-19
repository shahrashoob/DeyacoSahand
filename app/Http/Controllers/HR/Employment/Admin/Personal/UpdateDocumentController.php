<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\Accounting\CostCenter;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Document\DocumentType;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
این کنترلر برای تحویل مدارک به باگانی است.
 * */

class UpdateDocumentController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.personal.update_document.",
        "enable_status" => ["106", "119", "120"],
        "button" => ["caption" => "بروز رسانی مدارک (پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.update_document.",


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
        $employment_document_types_for_personal = EmploymentDocumentType::
        where('employment_id', $employment->id)->
        whereIn('receive_document_step_id', [1, 11])->
        get();


        return view($this->view_path . "index", compact('employment_document_types_for_personal', 'employment'));

    }

    public function submit(Request $request, Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $employment_document_types_for_personal = EmploymentDocumentType::
        where('employment_id', $employment->id)->
        whereIn('receive_document_step_id', [1, 11])->
        get();


        foreach ($employment_document_types_for_personal as $employment_document_type) {
            $input_file = "document_type_" . $employment_document_type->id;
            if (isset($request->$input_file)) {

                $result = PersonalInfoController::checkFileUploded($request, $input_file);
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $file = File::uploadFile(
                    $request->file($input_file),
                    $employment->id . '8' . Str::random(15) . File::get_file_extension($request->file($input_file)->getClientOriginalName()),
                    90,
                    'upload/employment_document_type', true);

                $employment_document_type->update(['file_id' => $file ? $file->id : null]);


            }
        }


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "بارگذاری مدارک با موفقیت انجام شد."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

}
