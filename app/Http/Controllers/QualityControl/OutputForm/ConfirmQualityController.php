<?php

namespace App\Http\Controllers\QualityControl\OutputForm;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportForm;
use Illuminate\Http\Request;

class ConfirmQualityController extends Controller
{

    var $view_path = "utility.transport.loading.dashboard.";
    var $route_path = "utility.transport.loading.dashboard.";
    var $dashboard_path = "quality_control.dashboard.index";


    public function confirm_exist_form(Request $request, Form $form)
    {

        $result = self::result_confirm_exist_form($request, $form);

        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }


    }

    public static function result_confirm_exist_form(Request $request, Form $form)
    {


        if ($form->status_id != 500000535) { // در انتظار تایید کنترل کیفیت
            return [
                "result" => false,
                "error" => "این فرم قبلا تایید شده است."
            ];
        }
        $list = ProductRequestFormForm::where("form_id", $form->id)->get();
        foreach ($list as $product_request_form_form) {
//            return  [
//                "result" => false,
//                "error" =>$product_request_form_form->product_request_form->applicant_type_id
//            ];
            if (in_array($product_request_form_form->product_request_form->applicant_type_id, [40])) {
                return [
                    "result" => false,
                    "error" => "برای تایید این برگ خروج لازم است تا از طریق داشبورد انبارک های ماشین اقدام نمایید."
                ];
            }
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("quality_control.reject_product.cheek_quality.index")) {
            return [
                "result" => false,
                "error" => "شما اجازه دسترسی به عملیات مورد نظر را ندارید"
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

        // ارسال درخواست برای پیمانکارانی که سامانه دارند.
        switch ($product_request_form_form->product_request_form->applicant_type_id) {
            case 20: // پیمانکار
                $contractor = Contractor::find($product_request_form_form->product_request_form->applicant_id);
                if (!$contractor) {
                    return [
                        "result" => false,
                        "error" => "شناسه پیمانکار در درخواست نامعتبر است."
                    ];
                }
                $result_call_api_input = Contractor::CallApiAddInputFormForContractor($contractor, $product_request_form_form->product_request_form, $form, null);
                if (!$result_call_api_input["result"]) {
                    return back()->withErrors($result_call_api_input["error"]);
                }
                break;
            case 30: // مشتری
                $customer = Customer::find($product_request_form_form->product_request_form->applicant_id);
                if (!$customer) {
                    return [
                        "result" => false,
                        "error" => "شناسه مشتری در درخواست نامعتبر است."
                    ];
                }
                $result_call_api_input = Customer::CallApiAddInputFormForCustomer($customer, $product_request_form_form->product_request_form, $form, null);
                if (!$result_call_api_input["result"]) {
                    return back()->withErrors($result_call_api_input["error"]);
                }
                break;
        }

        $product_request_form_form_list = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();


        foreach ($product_request_form_form_list as $item) {
            event(new ProductRequestFormLogEvent($item->product_request_form, "", $form->id, 7005024));
        }

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $result_pr = $product_request_form_form->product_request_form->checkIfValidConfirmRequest($form->id);

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form_form->product_request_form->updateExistFormStatusForm($form);

        if (!$result_pr["result"]) {
            return [
                "result" => false,
                "error" => $result_pr["error"]
            ];

        }

        return [
            "result" => true,
            "message" => "برگ حروج از انبار با موفقیت تایید شد."
        ];


    }


}