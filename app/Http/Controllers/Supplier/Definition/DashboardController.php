<?php

namespace App\Http\Controllers\Supplier\Definition;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // supplier/definition/dashboard
    var $route_path = "supplier.definition.dashboard.";
    var $view_path = "supplier.definition.dashboard.";

    public static $input_form = [
        "get_packing_form_details",
        "input_form_guarding_require_permission",
        "input_form_loading_require",
        "input_form_quality_control_permission",
    ];
    public static $output_form = [
        "exit_form_require_permission",
        "exit_form_require_quality_permission",
        "exit_form_require_draft_permission",
        "exit_form_loading_require_permission",
        "exit_form_guarding_require_permission",

    ];


    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_supplier");
            $order_by = session("order_by_supplier");
        }
        session(["search_supplier" => $search, "order_by_supplier" => $order_by]);


        $list = Supplier::
        where("code", "like", "%" . $search . "%")->
        orWhere("caption", "like", "%" . $search . "%")->
        paginate(50);

        $order_by_Option = Option::OrderBy("supplier", $order_by);
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("supplier.definition.dashboard.edit");
        return view($this->route_path . "index", compact("list", "search", "order_by", "order_by_Option",'edit_permission'));

    }

    public function create()
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("supplier.definition.dashboard.edit");
        if (!$edit_permission) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد');
        }
        $supplier = new Supplier();
        $country_option = Option::get("country", 112);
        $province_option = Option::get("province");
        $status_option = Option::get("active_status");
        $supplier_type_option = Option::get("supplier_type");
        $cost_center_option = Option::get("cost_center");
        $personal_type_option = Option::get("personal_type");
        $company_option = Option::get("company");
        $worker_option = Option::get("worker_cooperation_type", 0, 1300);

        $setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($setting);
        $allow_to_insert=0;
        if (!$data_supplier_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش تامین کنندگان مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض تامین کنندگان اقدام نمایید.");
        }
        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_guarding_require_permission_post_id);
        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");


        return view($this->view_path . "create", compact("data_supplier_default_setting", "status_option", "cost_center_option",
            "supplier", "get_the_supplier_image", "allow_to_insert","country_option", "province_option", "supplier_type_option", "post_option_list","personal_type_option",'company_option'
        ,'worker_option'));
    }

    public function store(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("supplier.definition.dashboard.edit");
        if (!$edit_permission) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد.');
        }
        $exsit_supplier=Supplier::where('user_id',$request->user_id)->exists();
        if($exsit_supplier){
            return back()->withErrors("این تامین کننده قبلا در سامانه ثبت نام نموده است");
        }

        $exsit_agent=Supplier::where('user_id',$request->user_id)->exists();
        if($exsit_agent){
            return back()->withErrors("این تامین کننده قبلا در سامانه به عنوان نماینده ثبت نام نموده است");
        }

        if($request->personal_type_id==2){
            $exsit_company=Supplier::where('company_id',$request->company_id)->exists();
            if($exsit_company){
                return back()->withErrors("این شرکت قبلا در سامانه به عنوان تامین کننده ثبت نام نموده است");
            }
        }
        $user_image = null;
        $result_file = self::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }

        $supplier = new Supplier();
        $supplier->save();

        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $supplier->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);

            $supplier->image_id = $user_image->id;
            $supplier->save();
        }


        $request["can_i_borrow_from_this_supplier"] = $request->can_i_borrow_from_this_supplier ? 1 : 0;

        $supplier->update($request->all());

        if ($request->personal_type_id == 2) {
            $supplier->user_id =  $supplier->company->user_id;
            $supplier->caption =  $supplier->company->caption;
        } else {
            $supplier->caption =  $supplier->user->fullname();
        }
        $supplier->save();

        $type = "create";
        self:: ConfirmInputForm($supplier, $type, $request);
        self:: ConfirmExitForm($supplier, $type, $request);
        self::CreateExitFormPost($supplier, $type, $request);


//        if ( $request["input_form_guarding_require_permission"] && ! $request["input_form_loading_require"] ) {
//            return back()->withErrors( "در تنظیمات ثبت اطلاعات تامین: اگر فرم ورود نیاز به تایید نگهبانی داشته باشد، باید تیک ارسال (بارگیری) نیز فعال باشد. " );
//        }


        SupplierAddress::create([
            'is_default' => 1,
            'supplier_id' => $supplier->id,
            'address_id' => ($supplier->personal_type_id== 1) ?  $supplier->user->user_address()->first()->address->id :
                 $supplier->company->user_address()->first()->address->id,
        ]);
        $address = $supplier->getDefaultAddress();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک تامین کننده با موفقیت اضافه شده."]);

    }

    public function edit(Supplier $supplier)
    {


        $address = $supplier->getDefaultAddress();
        $province_option = Option::get("province", $address->province_id ?? null);
        $country_option = Option::get("country", $address->country->id ?? 112);
        $status_option = Option::get("active_status", $supplier->active_status_id);
        $supplier_type_option = Option::get("supplier_type", $supplier->supplier_type_id);
        $cost_center_option = Option::get("cost_center", $supplier->cost_center_id);
        $allow_to_insert=1;
        $setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($setting);
        if (!$data_supplier_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش تامین کنندگان مشخص نشده است.
            لطفا از منوی تنظیمات،تنظیمات اولیه جهت ثبت تنظیمات پیش فرض تامین کنندگان اقدام نمایید.");
        }
        foreach (self::$input_form as $item) {
            $data_supplier_default_setting->$item->value = $supplier->$item ?? 0;
            $data_supplier_default_setting->$item->enable = 0;

        }
        foreach (self::$output_form as $item) {
            $data_supplier_default_setting->$item->value = $supplier->$item ?? 0;
            $data_supplier_default_setting->$item->enable = 0;

        }
        $data_supplier_default_setting->exit_form_require_permission_post_id = $supplier->exit_form_require_permission_post_id ?? 0;
        $data_supplier_default_setting->exit_form_require_draft_permission_post_id = $supplier->exit_form_require_draft_permission_post_id ?? 0;
        $data_supplier_default_setting->exit_form_guarding_require_permission_post_id = $supplier->exit_form_guarding_require_permission_post_id ?? 0;

        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_guarding_require_permission_post_id);

        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");


        return view($this->view_path . "edit", compact("data_supplier_default_setting",
            "status_option", "get_the_supplier_image", "cost_center_option", "post_option_list",
            "supplier", "country_option", "address", "province_option", "supplier_type_option","allow_to_insert"));
    }

    public function update(Request $request, Supplier $supplier)
    {



        $user_image = null;
        $result_file = self::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }
        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $supplier->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);
            $supplier->image_id = $user_image->id;
            $supplier->save();
        }

        $request["can_i_borrow_from_this_supplier"] = $request->can_i_borrow_from_this_supplier ? 1 : 0;
        $supplier->update($request->all());

        $type = "update";
        self:: ConfirmInputForm($supplier, $type, $request);
        self:: ConfirmExitForm($supplier, $type, $request);
        self::CreateExitFormPost($supplier, $type, $request);

        $supplier->UpdateAddress($request, 1);

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات تامین کننده با موفقیت بروز رسانی شد."]);

    }

    public static function ConfirmInputForm(Supplier $supplier, $type, Request $request)
    {
        $setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($setting);


        foreach (self::$input_form as $item) {

            if (!$data_supplier_default_setting->$item->enable || $type == "update") {
                $supplier->$item = $request->$item ? 1 : 0;
            } else {
                $supplier->$item = $data_supplier_default_setting->$item->value ? 1 : 0;
            }
        }

        $supplier->save();
    }

    public static function ConfirmExitForm(Supplier $supplier, $type, Request $request)
    {

        $setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($setting);

        foreach (self::$output_form as $item) {

            if (!$data_supplier_default_setting->$item->enable || $type == "update") {
                $supplier->$item = $request->$item ? 1 : 0;
            } else {
                $supplier->$item = $data_supplier_default_setting->$item->value ? 1 : 0;
            }
        }

        $supplier->save();

    }

    public static function CreateExitFormPost(Supplier $supplier, $type, Request $request)
    {
        $setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($setting);


        if (!$data_supplier_default_setting->exit_form_require_draft_permission->enable || $type == "update") {
            $supplier->exit_form_require_draft_permission_post_id = $request->exit_form_require_draft_permission_post_id;
        } elseif ($data_supplier_default_setting->exit_form_require_draft_permission->enable && $data_supplier_default_setting->exit_form_require_draft_permission->value) {
            $supplier->exit_form_require_draft_permission_post_id = $data_supplier_default_setting->exit_form_require_draft_permission_post_id;
        }

        if (!$data_supplier_default_setting->exit_form_require_permission->enable || $type == "update") {
            $supplier->exit_form_require_permission_post_id = $request->exit_form_require_permission_post_id;
        } elseif ($data_supplier_default_setting->exit_form_require_permission->enable && $data_supplier_default_setting->exit_form_require_permission->value) {
            $supplier->exit_form_require_permission_post_id = $data_supplier_default_setting->exit_form_require_permission_post_id;
        }
        if (!$data_supplier_default_setting->exit_form_guarding_require_permission->enable || $type == "update") {
            $supplier->exit_form_guarding_require_permission_post_id = $request->exit_form_guarding_require_permission_post_id;
        } elseif ($data_supplier_default_setting->exit_form_guarding_require_permission->enable && $data_supplier_default_setting->exit_form_guarding_require_permission->value) {
            $supplier->exit_form_guarding_require_permission_post_id = $data_supplier_default_setting->exit_form_guarding_require_permission_post_id;
        }

        $supplier->save();

    }

    public static function checkFileUploded(
        Request $request,
                $file_name,
                $format_list = [
                    "pdf",
                    "xls",
                    "xlsx",
                    "doc",
                    "docx",
                    "png",
                    "jpg",
                    "zip",
                    "txt"
                ])
    {
        $size_byte = 0;
        $max_size_mb = Setting::getIntegerValue("office_automation_max_file_size_in_mb");

        $file = $request->file($file_name);
        if (!$file) {
            return ["result" => true, 'warning' => "فایل انتخاب نشده است"];
        }
        $size_byte += $file->getSize();
        if (!in_array(File::get_file_extension($file->getClientOriginalName()), $format_list)) {
            return ["result" => false, "error" => "فرمت فایل بارگذاری شده قابل قبول نیست"];
        }

        if ($size_byte / 1024 / 1024 > $max_size_mb) {

            return [
                "result" => false,
                "error" => "حداکثر اندازه فایل جهت بارگذاری " . $max_size_mb . " مگابایت می باشد."
            ];

        }


        return ["result" => true];
    }
}
