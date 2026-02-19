<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\Post\PostDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadPrimaryDocumentController extends Controller
{
    protected $view_path = "hr.employment.register.upload_primary_document.";
    protected $route_path = "hr.employment.register.upload_primary_document.";
    protected $next_route = "hr.employment.register.other.index";

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $post_document_type = PostDocumentType::
        where([
            'post_id' => $employment->post_id,
            'document_delivery_type' => 1
        ])->get();

        if (!$post_document_type) {
            return redirect()->route("login")->withErrors("مدارکی برای بارگزاری اولیه وجود ندارد.");
        }

        return view($this->view_path . "index", compact('post_document_type', 'employment'));

    }

    public function submit($key, Request $request)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $post_document_types = PostDocumentType::
        where([
            'post_id' => $employment->post_id,
            'document_delivery_type' => 1
        ])->get();

        foreach ($post_document_types as $item) {
            //بارگزاری فایل مدارک استخدام
            $file = null;
            $input_file = 'file_' . $item->id;

            $result_file = PersonalInfoController::checkFileUploded($request, $input_file, ["png", 'jpg', 'jpeg', 'pdf', 'xls','xlsx', 'zip', 'doc', 'docx']);
            if (!$result_file["result"]) {
                return back()->withErrors($result_file["error"]);
            }
        }

        foreach ($post_document_types as $item) {
            //بارگزاری فایل مدارک استخدام
            $file = null;
            $input_file = 'file_' . $item->id;


            if ($request->hasFile($input_file)) {
                $file = File::uploadFile(
                    $request->file($input_file),
                    $employment->id.$item->id. Str::random(5) . File::get_file_extension($request->file($input_file)->getClientOriginalName()),
                    90,
                    'upload/employment_document_type/', true);
            }

            EmploymentDocumentType::create([
                'user_id' => $employment->user_id,
                'employment_id' => $employment->id,
                'file_id' => $file ? $file->id : null,
                'document_type_id' => $item->document_type_id,
            ]);
        }


        return redirect()->route($this->next_route, $employment->key)->with(["success" => "مدارک شما با موفقیت ثبت گردید."]);


    }
}
