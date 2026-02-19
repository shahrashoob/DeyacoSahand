<?php

namespace App\Http\Controllers\Contractor\Definition;

use App\Http\Controllers\Controller;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    var $route_path = "contractor.definition.default.";
    var $view_path = "contractor.definition.default.";
    public function index()
    {


        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);


        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_permission_post_id??0);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_draft_permission_post_id??0);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_guarding_require_permission_post_id??0);

        $values = Setting::getValues();
        $contractor_draft_contract_post_ids_for_final_confirm = Setting::getStringValue("contractor_draft_contract_post_ids_for_final_confirm");
        $contractor_draft_contract_post_ids_for_final_confirm_setting = json_decode($contractor_draft_contract_post_ids_for_final_confirm);
        $post_option_contractor_draft_contract_final_confirm = Option::get("posts",$contractor_draft_contract_post_ids_for_final_confirm_setting);

        $contractor_draft_contract_post_ids_for_init_confirm = Setting::getStringValue("contractor_draft_contract_post_ids_for_init_confirm");
        $contractor_draft_contract_post_ids_for_init_confirm_setting = json_decode($contractor_draft_contract_post_ids_for_init_confirm);
        $post_option_contractor_draft_contract_init_confirm = Option::get("posts", $contractor_draft_contract_post_ids_for_init_confirm_setting);



        return view($this->view_path . "index", compact("post_option_list", "data_contractor_default_setting",
        "values","post_option_contractor_draft_contract_final_confirm","post_option_contractor_draft_contract_init_confirm"
        ));

    }

    public function submit(Request $request)
    {
        $request = $request->all();

        foreach (DashboardController::$input_form as $item) {
            $request[$item]["value"] = isset($request[$item]["value"]) ? 1 : 0;
            $request[$item]["enable"] = isset($request[$item]["enable"]) ? 1 : 0;

        }
        foreach (DashboardController::$output_form as $item) {
            $request[$item]["value"] = isset($request[$item]["value"]) ? 1 : 0;
            $request[$item]["enable"] = isset($request[$item]["enable"]) ? 1 : 0;
        }

        $json_data = json_encode($request);

        $setting = Setting::where('key', 'contractor_default_setting')->first();
        $setting->string_value = $json_data;
        $setting->save();
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات پیشفرض با موفقیت ذخیره شد."]);
    }

}
