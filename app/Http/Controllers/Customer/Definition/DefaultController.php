<?php

namespace App\Http\Controllers\Customer\Definition;

use App\Http\Controllers\Controller;

use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;


class DefaultController extends Controller
{
    var $route_path = "customer_group.definition.default.";
    var $view_path = "customer.definition.default.";


    public function index()
    {
        $setting = Setting::getStringValue("customer_default_setting");
        $data_customer_default_setting = json_decode($setting, true);


        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();

        $post_option_list = [];
        foreach ($orderPermissionType as $item) {
            $post_option_list[$item->id] = Option::get("posts", isset($data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'])
                ? $data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'] : null);
        }

        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_permission_post_id'] ?? 0);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_draft_permission_post_id'] ?? 0);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission_post_id'] ?? 0);
        $post_option_list["exit_form_require_demands_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_demands_permission_post_id'] ?? 0);

        $values = Setting::getValues();

        $customer_draft_contract_post_ids_for_final_confirm = Setting::getStringValue("customer_draft_contract_post_ids_for_final_confirm");
        $customer_draft_contract_post_ids_for_final_confirm_setting = json_decode($customer_draft_contract_post_ids_for_final_confirm);
        $post_option_customer_draft_contract_final_confirm = Option::get("posts",$customer_draft_contract_post_ids_for_final_confirm_setting);

        $customer_draft_contract_post_ids_for_init_confirm = Setting::getStringValue("customer_draft_contract_post_ids_for_init_confirm");
        $customer_draft_contract_post_ids_for_init_confirm_setting = json_decode($customer_draft_contract_post_ids_for_init_confirm);
        $post_option_customer_draft_contract_init_confirm = Option::get("posts", $customer_draft_contract_post_ids_for_init_confirm_setting);

        $payment_method_types = PaymentMethodType::get();
        return view($this->view_path . "index", compact(
            'data_customer_default_setting',
            'post_option_customer_draft_contract_final_confirm',
            'post_option_customer_draft_contract_init_confirm',
            "orderPermissionType",
            "post_option_list",
            "payment_method_types",
            "values"

        ));
    }

    public function submit(Request $request)
    {


        $data = ["order_permission" => [],
            "exit_form" => [],
            "payment_method_type" => [],
            "input_form" => [],

        ];

        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();
        foreach ($orderPermissionType as $item) {
            $data["order_permission"][$item->id] = [
                "checked" => $request->input("order_permission_checked_" . $item->id) ? 1 : 0,
                "send_sms_for_post_id" => $request->input("order_permission_" . $item->id),
                "enable" => $request->input("order_permission_enable_" . $item->id) ? 1 : 0,
            ];
        }
        foreach (AdminController::$output_form as $item) {
            $data["exit_form"][$item]["value"] = isset($request[$item]["value"]) ? 1 : 0;
            $data["exit_form"][$item]["enable"] = isset($request[$item]["enable"]) ? 1 : 0;
        }

        foreach (AdminController::$input_form as $item) {
            $data["input_form"][$item]["value"] = isset($request[$item]["value"]) ? 1 : 0;
            $data["input_form"][$item]["enable"] = isset($request[$item]["enable"]) ? 1 : 0;

        }
        $data["exit_form"]['exit_form_require_draft_permission_post_id'] = $request->exit_form_require_draft_permission_post_id ?? null;
        $data["exit_form"]['exit_form_require_permission_post_id'] = $request->exit_form_require_permission_post_id ?? null;
        $data["exit_form"]['exit_form_guarding_require_permission_post_id'] = $request->exit_form_guarding_require_permission_post_id ?? null;
        $data["exit_form"]['the_max_day_allowed_to_conform_exit_form_to'] = $request->the_max_day_allowed_to_conform_exit_form_to ?? 1;
        $data["exit_form"]['the_max_day_for_reject_product'] = $request->the_max_day_for_reject_product ?? 1;
        $data["exit_form"]['exit_form_require_demands_permission_post_id'] = $request->exit_form_require_demands_permission_post_id ??null;


        $payment_method_types = PaymentMethodType::get();
        foreach ($payment_method_types as $item) {
            $data["payment_method_type"][$item->id]["checked"] = isset($request->data['payment_method_type'][$item->id]['checked']) ? 1 : 0;
            $data["payment_method_type"][$item->id]["enable"] = isset($request->data['payment_method_type'][$item->id]['enable']) ? 1 : 0;
            $data["payment_method_type"][$item->id]["min_percentage"] = isset($request->data['payment_method_type'][$item->id]['min_percentage']) ?$request->data['payment_method_type'][$item->id]['min_percentage'] : null;
            $data["payment_method_type"][$item->id]["max_percentage"] = isset($request->data['payment_method_type'][$item->id]['max_percentage']) ? $request->data['payment_method_type'][$item->id]['max_percentage'] : null;
            $data["payment_method_type"][$item->id]["max_check_delivery_time_in_days"] = isset($request->data['payment_method_type'][$item->id]['max_check_delivery_time_in_days']) ? $request->data['payment_method_type'][$item->id]['max_check_delivery_time_in_days'] : null;

        }
//        $data["payment_method_type"] = isset($request->data['payment_method_type']) ? $request->data['payment_method_type'] : null;
        $json_data = json_encode($data);


        $setting = Setting::where('key', 'customer_default_setting')->first();
        $setting->string_value = $json_data;
        $setting->save();
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات پیشفرض با موفقیت ذخیره شد."]);
    }

}
