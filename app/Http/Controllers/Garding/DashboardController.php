<?php

namespace App\Http\Controllers\Garding;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Transport\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class DashboardController extends Controller
{

    var $view_path = "guarding.dashboard.";
    var $route_path = "guarding.dashboard.";

    public function index()
    {

        $list_input_form = Form::where("forms.status_id", 500000710)->paginate(); // در انتظار ورود
        $list_output_form = Form::where("forms.status_id", 500000530)->paginate(); // در انتظار خروج
     
        $list_input_transport = Transport::whereIn("status_id", [
            6010104,
            6010101
        ])->get(); // در انتظار ورود به سازما

        return view($this->view_path . "index", compact("list_input_form", "list_output_form", "list_input_transport",));

    }

    public function show_transport(Transport $transport)
    {


        return view($this->view_path . "show_transport", compact("transport"));

    }

    public function show_exit_form(Form $form)
    {


        $packing_form = [];
        foreach ($form->item as $item) {
            $packing_form[$item->packing_form_item->packing_form->id] = 1;
        }
        $product_request_form_form = ProductRequestFormForm::where("form_id", $form->id)->first();


        return view($this->view_path . "show_exit_form", compact("form", "packing_form", "product_request_form_form"));
    }

    public function show_input_form(Form $form)
    {


        $packing_form = [];
        foreach ($form->item as $item) {
            $packing_form[$item->packing_form_item->packing_form->id] = 1;
        }


        return view($this->view_path . "show_input_form", compact("form", "packing_form"));
    }

    public function confirm_input_form(Request $request, Form $form)
    {


        if ($form->status_id != 500000710) { // در انتظار تایید نگهبانی
            return back()->withErrors("این فرم قبلا تایید شده است.");
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.show_form")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        $form->status_id = Form::nextStatusForInputForm($form);
        $form->save();

        event(new FormLogEvent($form, ""));

        return back()->with(["success" => "فرم ورود با موفقیت تایید شد."]);

    }

    public function reject_input_form(Request $request, Form $form)
    {


        if ($form->status_id != 500000710) { // در انتظار تایید نگهبانی
            return back()->withErrors("این فرم قبلا تایید شده است.");
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.show_form")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        $form->status_id = 500000100;
        $form->save();

        event(new FormLogEvent($form, ""));

        return back()->with(["success" => "عدم تایید فرم انبار با موفقیت ثبت گردید."]);

    }

    public function confirm_exist_form(Request $request, Form $form)
    {


        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.show_form")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات (ثبت خروج بار (برگ خروج) برای نگهبانی) مورد نظر را ندارید");
        }
        $result = self::ConfirmExitFrom($form,false);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return back()->with(["success" => "برگ حروج از انبار با موفقیت تایید شد."]);

    }

    /**
     * @param Form $form
     * @param $form_has_transport آیا بارگیری دارد
     * @return array
     */
    public static function ConfirmExitFrom(Form $form,$form_has_transport)
    {
        if ($form->status_id != 500000530) { // در انتظار تایید نگهبانی
            return [
                "result" => false,
                "error" => "این فرم قبلا تایید شده است."
            ];
        }


        $product_request_form_form = ProductRequestFormForm::join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        where("product_request_form_form.form_id", $form->id)->
        select("product_request_form_form.id", "product_request_form_id")->
        first();

        if (!$product_request_form_form) {
            return [
                "result" => false,
                "error" => "بسته بندی های فرم در سیستم وجود ندارد، لطفا با پشتیبانی تماس بگیرید."
            ];

        }


        if($form_has_transport==false) {
            switch ($product_request_form_form->product_request_form->applicant_type_id){
                case 20:  // ارسال درخواست برای پیمانکارانی که سامانه دارند.
                    $contractor=Contractor::find($product_request_form_form->product_request_form->applicant_id);
                    if(!$contractor){
                        return [
                            "result" => false,
                            "error" => "شناسه پیمانکار در درخواست نامعتبر است."
                        ];
                    }
                    $result_call_api_input = Contractor::CallApiAddInputFormForContractor($contractor, $product_request_form_form->product_request_form, $form,null);
                    if (!$result_call_api_input["result"]) {
                        return $result_call_api_input;
                    }
                    break;
                case 30: // ارسال درخواست برای مشتریانی که سامانه دارند
                    $customer=Customer::find($product_request_form_form->product_request_form->applicant_id);
                    if(!$customer){
                        return [
                            "result" => false,
                            "error" => "شناسه مشتری در درخواست نامعتبر است."
                        ];
                    }
                    $result_call_api_input = Customer::CallApiAddInputFormForCustomer($customer, $product_request_form_form->product_request_form, $form,null);
                    if (!$result_call_api_input["result"]) {
                        return $result_call_api_input;
                    }
                    break;
            }

        }


        $product_request_form_form_list = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();

        foreach ($product_request_form_form_list as $item) {
            event(new ProductRequestFormLogEvent($item->product_request_form, "", $form->id, 7005009));

        }

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $result = $product_request_form_form->product_request_form->checkIfValidConfirmRequest($form->id);

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form_form->product_request_form->updateExistFormStatusForm($form);

        return $result;
    }


    public function reject_exist_form(Request $request, Form $form)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.show_form")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }
        $result = self::RejectExitFrom($form);
        if ($result["result"]) {
            return back()->with(["success" => "عدم تایید فرم خروج با موفقیت ثبت گردید."]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function RejectExitFrom(Form $form)
    {


        if ($form->status_id != 500000530) { // در انتظار تایید نگهبانی
            return [
                "result" => false,
                "error" => "این فرم قبلا تایید شده است."
            ];
        }


        $product_request_form_form = ProductRequestFormForm::join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        where("product_request_form_form.form_id", $form->id)->
        select("product_request_form_form.id", "product_request_form_id")->
        first();

        if (!$product_request_form_form) {
            return [
                "result" => false,
                "error" => "بسته بندی های فرم در سیستم وجود ندارد، لطفا با پشتیبانی تماس بگیرید."
            ];

        }

        $result = $product_request_form_form->product_request_form->rejectRequest($form);
        return $result;
    }

}
