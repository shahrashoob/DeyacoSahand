<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserBankAccount;
use App\Models\Post\PostUser;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
/*
این کنترلر برای طب کار می باشد.
 * */
class WorkMedicineController extends Controller
{
    protected $view_path = "hr.employment.register.personal.work_medicine.";
    protected $route_path = "hr.employment.register.personal.work_medicine.";
    protected $next_route = "hr.employment.register.personal.bank_information.index";

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640118,4640115,4640121])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $address_of_work_medicine_doctor_setting = Setting::getStringValue("address_of_work_medicine_doctor");

        return view($this->view_path . "index", compact('employment','address_of_work_medicine_doctor_setting'));

    }

    public function submit($key, Request $request)
    {


        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640118,4640115,4640121])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $old_document = EmploymentDocumentType::where('employment_id', $employment->id)
            ->where('document_type_id', 8)
            ->first();

        if ($old_document) {
            $file = $old_document->file;
        } else {
            $file = null;
        }

        $input_file = 'work_medicine_file_id' ;
        $result_file = PersonalInfoController::checkFileUploded($request, $input_file, ["png", 'jpg', 'jpeg', 'pdf', 'xls', 'xlsx', 'zip', 'doc', 'docx']);
        if (!$result_file["result"]) {
            return back()->withErrors( $result_file["error"]);
        }

        if ($request->hasFile($input_file)) {
            $file = File::uploadFile(
                $request->file($input_file),
                $employment->id . '8' . Str::random(15) . File::get_file_extension($request->file($input_file)->getClientOriginalName()),
                90,
                'upload/employment_document_type', true);

            if ($old_document) {
                $old_document->update(['file_id' => $file ? $file->id : null]);
            } else {
                EmploymentDocumentType::create([
                    'user_id' => $employment->user_id,
                    'employment_id' => $employment->id,
                    'file_id' => $file ? $file->id : null,
                    'document_type_id' => 8,
                    'receive_document_step_id' => 10,
                    'other_id' => null,
                ]);
            }
        }

        $employment->status_id = 4640121;//در انتظار تایید اطلاعات مالی
        $employment->save();
        event(new EmploymentLogEvent($employment, 4640021,null, null, $employment->user_id));

        return redirect()->route($this->next_route, $employment->key)->with(["success" => "طب کار با موفقیت ثبت گردید."]);


    }
    //تابع نمایش پی دی اف
    public function letter($key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640118,4640115])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        Self::WorkMedicineLetter($employment);
    }
    public static function WorkMedicineLetter(Employment $employment){



        $controller=new WorkMedicineController();
        $post_id_for_contract_setting = Setting::getIntegerValue("post_id_for_contract");
        $post_user=PostUser::where('post_id',$post_id_for_contract_setting)->first();
        $name_of_work_medicine_doctor_setting = Setting::getStringValue("name_of_work_medicine_doctor");
        $address_of_work_medicine_doctor_setting = Setting::getStringValue("address_of_work_medicine_doctor");
        $html =view($controller->view_path . "letter", compact('employment','post_user','address_of_work_medicine_doctor_setting','post_id_for_contract_setting','name_of_work_medicine_doctor_setting'))->render();
        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A5',
            'orientation' => 'L',
        ]);


        $pdf->WriteHTML($html);
        $pdf->Output('work_medicine.pdf', 'D');
    }
}
