<?php

namespace App\Http\Controllers\Utility\Transport\Loading;

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

class DashboardController extends Controller
{

    var $view_path = "utility.transport.loading.dashboard.";
    var $route_path = "utility.transport.loading.dashboard.";

    public function index()
    {

        $list = Form::where("forms.status_id", 500000525)->paginate();

        $transport_list = Transport::where("transport_type_id", 2)->where("car_id", ">", 0)->orderByDesc("id")->paginate();

        return view($this->view_path . "index", compact("list", "transport_list"));

    }

    public function show_form(Form $form)
    {


        $packing_form = [];
        foreach ($form->item as $item) {
            $packing_form[$item->packing_form_item->packing_form->id] = 1;
        }
        $product_request_form_form = ProductRequestFormForm::where("form_id", $form->id)->first();

        $transport_form = TransportForm::where(["form_id" => $form->id])->first();

        return view($this->view_path . "show_form", compact("transport_form", "form", "packing_form", "product_request_form_form"));
    }

    public function confirm_exist_form(Request $request, Form $form)
    {

        $result = self::result_confirm_exist_form($request, $form, false);

        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }


    }

    public static function result_confirm_exist_form(Request $request, Form $form, $form_has_transport)
    {


        if ($form->status_id != 500000525) { // در انتظار تایید بارگیری
            return [
                "result" => false,
                "error" => "این فرم قبلا تایید شده است."
            ];
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("utility.transport.loading.show_form")) {
            return [
                "result" => false,
                "error" => "شما اجازه دسترسی به عملیات (تایید برگ خروج (بارگیری) ) مورد نظر را ندارید"
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


        if ($form_has_transport == false ) {
        switch ($product_request_form_form->product_request_form->applicant_type_id) {
            case 20:  // ارسال درخواست برای پیمانکارانی که سامانه دارند.
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
            case 30:
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

    }


        $product_request_form_form_list = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();


        foreach ($product_request_form_form_list as $item) {
            event(new ProductRequestFormLogEvent($item->product_request_form, "", $form->id, 7005014));
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
            "message" => "برگ حروج از انبار با موفقیت تایید شد.",
            "product_request_form_form" => $product_request_form_form
        ];


    }

    public function confirm_input(Request $request, Transport $transport)
    {


        if ($transport->status_id != 6010104) { // در انتظار تایید ورود
            return back()->withErrors("وضعیت بار در انتظار تایید ورود به سازمان نمی باشد.");

        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.allow_confirm_input_loading")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }
        foreach ($transport->transport_forms as $transport_form) {
            if ($transport_form->form->status_id != 500000710) {
                return back()->withErrors("وضعیت فرم های داخل بار نامعتبر است.");
            }
        }

        foreach ($transport->transport_forms as $transport_form) {

            $transport_form->form->status_id = Form::nextStatusForInputForm($transport_form->form);
            $transport_form->form->save();

            event(new FormLogEvent($transport_form->form, ""));
        }

        $transport->status_id = 6010105; // وارد شده به سازمان
        $transport->save();

        return back()->with(["success" => "ورود بار با موفقیت ثبت گردید"]);

    }

    public function reject_input(Request $request, Transport $transport)
    {

        if ($transport->status_id != 6010104) { // در انتظار تایید ورود
            return back()->withErrors("وضعیت بار در انتظار تایید ورود به سازمان نمی باشد.");

        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("utility.transport.loading.show_form")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");


        }
        foreach ($transport->transport_forms as $transport_form) {
            if ($transport_form->form->status_id != 500000710) {
                return back()->withErrors("وضعیت فرم های داخل بار نامعتبر است.");
            }
        }

        foreach ($transport->transport_forms as $transport_form) {

            $transport_form->form->status_id = 500000100;
            $transport_form->form->save();

            event(new FormLogEvent($transport_form->form, ""));
        }

        $transport->status_id = 6010105; // وارد شده به سازمان
        $transport->save();

        return back()->with(["success" => "عدم تایید بار با موفقیت ثبت گردید"]);
    }

    public function confirm_output(Request $request, Transport $transport)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.allow_confirm_output_loading")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        foreach ($transport->transport_forms as $transport_form) {
            if ($transport_form->form->status_id != 500000530) {
                return back()->withErrors("وضعیت فرم های داخل بار نامعتبر است.");
            }
        }

        $check_call = false;
        foreach ($transport->transport_forms as $transport_form) {
            if (!$check_call) {
                // اگر پیمان کار باشد، باید فرم ورود بخورد.
                $result = Transport::CallApiAddInputFormForTransport($transport, $transport_form->form);
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $check_call = true;
            }

            $result = \App\Http\Controllers\Garding\DashboardController::ConfirmExitFrom($transport_form->form, true);
            if (!$result["result"]) {
                return back()->withErrors("برگ خروج " . $transport_form->form->code . " :" . $result["error"]);
            }
        }

        $transport->status_id = 6010102; // خارج شده از سازمان
        $transport->save();

        return back()->with(["success" => " تایید خروج بار با موفقیت ثبت گردید"]);
    }

    public function reject_output()
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("guarding.dashboard.allow_confirm_output_loading")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");

        }
        return back()->withErrors("جهت عدم تایید بار با پشتیبانی سامانه تماس بگیرید.");

    }

}
