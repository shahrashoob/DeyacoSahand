<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Contract\ContractType;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\Shift\ShiftWork;
use App\Models\Post\Post;
use App\Models\Post\PostContractType;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStep;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class EmploymentController extends Controller
{
    ///  hr/post/employment
    private $view_path = "hr.post.employment.";
    private $route_path = "hr.post.employment.";

    //نمایش لیست تنظیمات ارزیابی عملکرد
    public function index(Post $post)
    {

        $selection_setting = SelectionPostSetting::where("post_id", $post->id)->get();
        $document_receive_steps = DocumentReceiveStep::all();
        $confirm_type = $post->post_document_receive_step_confirms()->pluck("confirm_type", "receive_document_step_id")->toArray();
        $is_necessary_to_deliver_document_to_archive = $post->post_document_receive_step_confirms()->pluck("is_necessary_to_deliver_document_to_archive", "receive_document_step_id")->toArray();
        $contract_option = Option::get("contracts", $post->contract_id);
        $shift_work = ShiftWork::pluck("caption", "id")->toArray();
        $shift_work[0] = "نامشخص";
        $shift_option = Option::get("shift", $post->shift_id);
        $contract_types = ContractType::all();
        $shift_delivery_module_option = Option::get("shift_delivery_module", $post->shift_delivery_module_id);
        $post_contract_type_ids = $post->post_contract_types()->pluck("contract_type_id", "contract_type_id")->toArray();

        return view($this->view_path . "index", compact('post', 'shift_option',"contract_option","shift_delivery_module_option", "post_contract_type_ids", "contract_types",
            "selection_setting", "document_receive_steps", "confirm_type",'is_necessary_to_deliver_document_to_archive'));
    }

    public function info_setting(Request $request, Post $post)
    {

        $request["is_basis_for_duties_on_the_opinion_of_employer"] = $request->is_basis_for_duties_on_the_opinion_of_employer ? 1 : 0;
        $request["is_basis_for_calculation_working_hour_on_labor_low"] = $request->is_basis_for_calculation_working_hour_on_labor_low ? 1 : 0;
        $request["is_basis_for_daily_salary_on_labor_low"] = $request->is_basis_for_daily_salary_on_labor_low ? 1 : 0;
        $request["allow_show_result_of_selection"] = $request->allow_show_result_of_selection ? 1 : 0;
        $request["does_it_have_shift_work"] = $request->does_it_have_shift_work ? 1 : 0;
        $post->update($request->all());
        if(!$post->does_it_have_shift_work){
            $post->shift_id=0;
            $post->shift_delivery_module_id=0;
            $post->save();
        }

        ########################## Document Types
        PostDocumentReceiveStepConfirm::where([
            "post_id" => $post->id,
        ])->delete();
$is_necessary_to_deliver_document_to_archive=$request->is_necessary_to_deliver_document_to_archive;
        foreach (DocumentReceiveStep::all() as $document_receive_step) {
            PostDocumentReceiveStepConfirm::create([
                "post_id" => $post->id,
                'receive_document_step_id' => $document_receive_step->id,
                'confirm_type' => $request->input("confirm_type_$document_receive_step->id"),
                'is_necessary_to_deliver_document_to_archive'=>isset($is_necessary_to_deliver_document_to_archive[$document_receive_step->id]),
            ]);
        }
//        $post_contract_types = $request->post_contract_type;
//        PostContractType::where([
//            "post_id" => $post->id,
//        ])->delete();
//
//        foreach (ContractType::all() as $contract_type) {
//            if (isset($post_contract_types[$contract_type->id])) {
//                PostContractType::create([
//                    "post_id" => $post->id,
//                    'contract_type_id' => $contract_type->id,
//                ]);
//            }
//        }

        return back()->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }
}
