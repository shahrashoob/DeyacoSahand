<?php

namespace App\Http\Controllers\Contractor\Definition;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAddress;
use App\Models\File\File;
use App\Models\HR\Company\Company;
use App\Models\Post\Post;

use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //
    var $route_path = "contractor.definition.dashboard.";
    var $view_path = "contractor.definition.dashboard.";

    public static $input_form = [
        "checking_form_not_delivered_at_register_production",
        "get_packing_form_details",
        "input_form_guarding_require_permission",
        "input_form_loading_require",
        "input_form_quality_control_permission",
    ];
    public static $output_form = [
        "exit_form_require_permission",
        "exit_form_require_draft_permission",
        "exit_form_loading_require_permission",
        "exit_form_guarding_require_permission",
        "checking_carrier_at_delivery_of_product",

    ];

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_contractor");
            $order_by = session("order_by_contractor");
        }
        session(["search_contractor" => $search, "order_by_contractor" => $order_by]);
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("contractor.definition.dashboard.edit");
        $edit_permission_software_system = $post_user->checkButtonPermission("contractor.definition.dashboard.edit_software_system");
        $list = Contractor::where("code", "like", "%" . $search . "%")->orWhere("caption", "like", "%" . $search . "%")->paginate(50);
        $order_by_Option = Option::OrderBy("contractor", $order_by);

        return view($this->route_path . "index", compact("list", "search", "edit_permission_software_system", "order_by", "order_by_Option", 'edit_permission'));

    }


    public function create()
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("contractor.definition.dashboard.edit");
        if (!$edit_permission) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد.');
        }
        $contractor = new Contractor();
        $country_option = Option::get("country", 112);
        $province_option = Option::get("province");
        $status_option = Option::get("active_status");
        $cost_center_option = Option::get("cost_center");
        $post_option = Option::get("posts");
        $software_system_option = Option::get("software_system");
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);
        $company_option = Option::get("company");
        $worker_option = Option::get("worker_cooperation_type", 0, 1200);
        $personal_type_option = Option::get("personal_type");
        $allow_to_insert = 0;

        if (!$data_contractor_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش پیمانکاران مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض پیمانکاران اقدام نمایید.");
        }
        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_guarding_require_permission_post_id);

        $barcode_algorithm_option = Option::get("barcode_algorithm", 0);

        return view($this->view_path . "create", compact("barcode_algorithm_option", "status_option", "contractor", "country_option", "data_contractor_default_setting",
            "province_option", "post_option_list", "allow_to_insert", "cost_center_option", "post_option", "software_system_option", 'get_the_contractor_image', 'personal_type_option', 'worker_option', 'company_option'));


    }

    public function store(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("contractor.definition.dashboard.edit");
        if (!$edit_permission) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد.');
        }

        $exsit_contractor = Contractor::where('user_id', $request->user_id)->exists();
        if ($exsit_contractor) {
            return back()->withErrors("این پیمانکار قبلا در سامانه ثبت نام نموده است");
        }

        $exsit_agent = Contractor::where('user_id', $request->user_id)->exists();
        if ($exsit_agent) {
            return back()->withErrors("این پیمانکار قبلا در سامانه به عنوان نماینده ثبت نام نموده است");
        }

        if ($request->personal_type_id == 2) {
            $exsit_company = Contractor::where('company_id', $request->company_id)->exists();
            if ($exsit_company) {
                return back()->withErrors("این شرکت قبلا در سامانه به عنوان پیمانکار ثبت نام نموده است");
            }
        }
        $address =
            ($request->personal_type_id == 1) ?
                Worker::find($request->user_id) :
                Company::find($request->company_id);

        $address = $address->user_address()->first();
        if (!$address) {
            return back()->withErrors("آدرس پیمانکار قابل تشخیص نمی باشد، لطفا از طریق همکاری با ثبت نام نمایید.");
        }


        $contractor = new Contractor();
        $contractor->save();

        $user_image = null;
        $result_file = \App\Http\Controllers\Supplier\Definition\DashboardController::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }
        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $contractor->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);

            $contractor->image_id = $user_image->id;
            $contractor->save();
        }

        $request["it_is_coordination_for_sending"] = $request->it_is_coordination_for_sending ? 1 : 0;
        $request["show_packing_forms_in_warehouse"] = $request->show_packing_forms_in_warehouse ? 1 : 0;
//        $request["is_order_registration_date_chosen_by_contractor"] = $request->is_order_registration_date_chosen_by_contractor ? 1 : 0;
        $request["sent_address_place_type_of_transport"] = $request->sent_address_place_type_of_transport ? 1 : 0;
//        if ( $request["input_form_guarding_require_permission"] && ! $request["input_form_loading_require"] ) {
//            return back()->withErrors( "در تنظیمات ثبت اطلاعات تولید: اگر فرم ورود نیاز به تایید نگهبانی داشته باشد، باید تیک ارسال (بارگیری) نیز فعال باشد. " );
//        }

        $contractor->update($request->all());
        $contractor->user_id = $request->user_id;
        $contractor->save();

        if ($request->personal_type_id == 2) {
            $contractor->user_id = $contractor->company->user_id;
            $contractor->caption = $contractor->company->caption;
        } else {
            $contractor->caption = $contractor->worker->fullname();
        }
        $contractor->save();
        $type = "create";
        self:: ConfirmInputForm($contractor, $type, $request);
        self:: ConfirmExitForm($contractor, $type, $request);
        self::CreateExitFormPost($contractor, $type, $request);


        ContractorAddress::create([
            'is_default' => 1,
            'contractor_id' => $contractor->id,
            'address_id' => $address->address->id,
        ]);
        $address = $contractor->getDefaultAddress();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک پیمانکار با موفقیت اضافه شده."]);

    }

    public function edit(Contractor $contractor)
    {


        $allow_to_insert = 1;
        $address = $contractor->getDefaultAddress();
        $province_option = Option::get("province", $address->province_id);
        $country_option = Option::get("country", $address->country->id ?? 112);
        $status_option = Option::get("active_status", $contractor->active_status_id);
        $cost_center_option = Option::get("cost_center", $contractor->cost_center_id);
        $post_option = Option::get("posts", $contractor->post_id);
        $software_system_option = Option::get("software_system", $contractor->software_system_id);
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);
        if (!$data_contractor_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش پیمانکاران مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض پیمانکاران اقدام نمایید.");
        }
        foreach (self::$input_form as $item) {
            $data_contractor_default_setting->$item->value = $contractor->$item ?? 0;
            $data_contractor_default_setting->$item->enable = 0;

        }
        foreach (self::$output_form as $item) {
            $data_contractor_default_setting->$item->value = $contractor->$item ?? 0;
            $data_contractor_default_setting->$item->enable = 0;

        }
        $data_contractor_default_setting->exit_form_require_permission_post_id = $contractor->exit_form_require_permission_post_id ?? 0;
        $data_contractor_default_setting->exit_form_require_draft_permission_post_id = $contractor->exit_form_require_draft_permission_post_id ?? 0;
        $data_contractor_default_setting->exit_form_guarding_require_permission_post_id = $contractor->exit_form_guarding_require_permission_post_id ?? 0;

        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_guarding_require_permission_post_id);

        $barcode_algorithm_option = Option::get("barcode_algorithm", $contractor->barcode_algorithm_id);

        return view($this->view_path . "edit", compact("barcode_algorithm_option", "status_option", 'allow_to_insert', 'get_the_contractor_image', 'data_contractor_default_setting', "post_option_list", "contractor", "country_option", "address", "province_option", "software_system_option", "cost_center_option", "post_option"));

    }

    public function update(Request $request, Contractor $contractor)
    {


//        $national_code_count = Contractor::where("national_code", "like", $request->national_code)->
//        where("national_code", "!=", $contractor->national_code ?? 0)->exists();
//        if ($national_code_count) {
//            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
//        }
//        $post = Post::find($request->post_id);
//        if (!$post) {
//            return back()->withErrors("لطفا پست سازمانی مرتبط با پیمانکار را انتخاب نمایید.");
//        }
//        $exist_contractor = Contractor::
//        where("post_id", $post->id)->
//        where("id", "!=", $contractor->id)->
//        first();
//
//        if ($exist_contractor) {
//            return back()->withErrors("این پست سازمانی قبلا برای پیمانکار " . $exist_contractor->caption . " انتخاب شده است.");
//        }

        $user_image = null;
        $result_file = \App\Http\Controllers\Supplier\Definition\DashboardController::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }
        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $contractor->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);

            $contractor->image_id = $user_image->id;
            $contractor->save();
        }

        $request["start_of_work_time"] = new Carbon($request->start_of_work_time_h . ':' . $request->start_of_work_time_m . ':00');
        $request["end_of_work_time"] = new Carbon($request->end_of_work_time_h . ':' . $request->end_of_work_time_m . ':00');
        $request["it_is_coordination_for_sending"] = $request->it_is_coordination_for_sending ? 1 : 0;
        $request["show_packing_forms_in_warehouse"] = $request->show_packing_forms_in_warehouse ? 1 : 0;

//        $request["is_order_registration_date_chosen_by_contractor"] = $request->is_order_registration_date_chosen_by_contractor ? 1 : 0;
        $request["sent_address_place_type_of_transport"] = $request->sent_address_place_type_of_transport ? 1 : 0;


//        if ( $request["input_form_guarding_require_permission"] && ! $request["input_form_loading_require"] ) {
//            return back()->withErrors( "در تنظیمات ثبت اطلاعات تولید: اگر فرم ورود نیاز به تایید نگهبانی داشته باشد، باید تیک ارسال (بارگیری) نیز فعال باشد. " );
//        }


        $contractor->update($request->all());
        $type = "update";
        self:: ConfirmInputForm($contractor, $type, $request);
        self:: ConfirmExitForm($contractor, $type, $request);
        self::CreateExitFormPost($contractor, $type, $request);
        $contractor->UpdateAddress($request, 1);


        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات پیمانکار با موفقیت بروز رسانی شد."]);


    }

    public function edit_software_system(Contractor $contractor)
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission_software_system = $post_user->checkButtonPermission("contractor.definition.dashboard.edit_software_system");
        if (!$edit_permission_software_system) {
            return back()->withErrors('امکان دسترسی به این صفحه نامعتبر است.');
        }
        $software_system_option = Option::get("software_system", $contractor->software_system_id);

        return view($this->view_path . "edit_software_system", compact('software_system_option', 'contractor'));

    }

    public function update_software_system(Request $request, Contractor $contractor)
    {

        $post_user = Auth::user()->posts->first();
        $edit_permission_software_system = $post_user->checkButtonPermission("contractor.definition.dashboard.edit_software_system");
        if (!$edit_permission_software_system) {
            return back()->withErrors('امکان دسترسی به این صفحه نامعتبر است.');
        }
        $request["is_order_registration_date_chosen_by_contractor"] = $request->is_order_registration_date_chosen_by_contractor ? 1 : 0;
        $contractor->update($request->all());
        if ($request->software_system_id == 0) {// در صورتی که در درخواست ویرایش فاقد سامانه انتخاب کرد این ها باید نال باشند
            $contractor->api_url = null;
            $contractor->api_username = null;
            $contractor->api_password = null;
            $contractor->api_key = null;
            $contractor->image_id = $user_image->id ?? "";
            $contractor->save();
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "تنظمات سامانه جامع مشتری با موفقیت بروز رسانی شد."]);
    }

    public static function ConfirmInputForm(Contractor $contractor, $type, Request $request)
    {
        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);


        foreach (self::$input_form as $item) {

            if (!$data_contractor_default_setting->$item->enable || $type == "update") {
                $contractor->$item = $request->$item ? 1 : 0;
            } else {
                $contractor->$item = $data_contractor_default_setting->$item->value ? 1 : 0;
            }
        }

        $contractor->save();
    }

    public static function ConfirmExitForm(Contractor $contractor, $type, Request $request)
    {

        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);

        foreach (self::$output_form as $item) {

            if (!$data_contractor_default_setting->$item->enable || $type == "update") {
                $contractor->$item = $request->$item ? 1 : 0;
            } else {
                $contractor->$item = $data_contractor_default_setting->$item->value ? 1 : 0;
            }
        }

        $contractor->save();

    }

    public static function CreateExitFormPost(Contractor $contractor, $type, Request $request)
    {
        $setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($setting);


        if (!$data_contractor_default_setting->exit_form_require_draft_permission->enable || $type == "update") {
            $contractor->exit_form_require_draft_permission_post_id = $request->exit_form_require_draft_permission_post_id;
        } elseif ($data_contractor_default_setting->exit_form_require_draft_permission->enable && $data_contractor_default_setting->exit_form_require_draft_permission->value) {
            $contractor->exit_form_require_draft_permission_post_id = $data_contractor_default_setting->exit_form_require_draft_permission_post_id;
        }

        if (!$data_contractor_default_setting->exit_form_require_permission->enable || $type == "update") {
            $contractor->exit_form_require_permission_post_id = $request->exit_form_require_permission_post_id;
        } elseif ($data_contractor_default_setting->exit_form_require_permission->enable && $data_contractor_default_setting->exit_form_require_permission->value) {
            $contractor->exit_form_require_permission_post_id = $data_contractor_default_setting->exit_form_require_permission_post_id;
        }
        if (!$data_contractor_default_setting->exit_form_guarding_require_permission->enable || $type == "update") {
            $contractor->exit_form_guarding_require_permission_post_id = $request->exit_form_guarding_require_permission_post_id;
        } elseif ($data_contractor_default_setting->exit_form_guarding_require_permission->enable && $data_contractor_default_setting->exit_form_guarding_require_permission->value) {
            $contractor->exit_form_guarding_require_permission_post_id = $data_contractor_default_setting->exit_form_guarding_require_permission_post_id;
        }

        $contractor->save();

    }
}
