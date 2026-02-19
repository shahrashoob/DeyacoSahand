<?php

namespace App\Http\Controllers\HR\Worker;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\Worker;
use Illuminate\Http\Request;
use function Sodium\compare;

class PersonalFileController extends Controller
{
    var $view_path = "hr.worker.personal_file.";
    var $route_path = "hr.worker.personal_file.";


    public function index(Worker $worker)
    {
        $employment = Employment::where('user_id', $worker->id)->first();

        if (!$employment) {
            return back()->withErrors('پرونده پرسنلی برای ' . $worker->fullname() . ' یافت نشد.');
        }
        $active_tab = "personal_info";
        $allow_show_upload = false;
        $allow_delete = false;

        $employment_document_types_for_personal = EmploymentDocumentType::where([
            'receive_document_step_id' => 1,
            'employment_id' => $employment->id
        ])->get();

        return view($this->view_path . "index", compact('worker', "employment", 'active_tab',
            'employment_document_types_for_personal', 'allow_show_upload', 'allow_delete'));
    }


}
