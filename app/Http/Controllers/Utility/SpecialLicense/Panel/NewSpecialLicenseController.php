<?php

namespace App\Http\Controllers\Utility\SpecialLicense\Panel;

use App\Events\Utility\SpecialLicenseEvent;
use App\Http\Controllers\Contractor\Admin\ContractorAllocationController;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\Post\PostUser;
use App\Models\HR\User\UserEntryLog;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionForm;
use App\Models\Supplier\Supplier;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\SpecialLicense\SpecialLicenseConfirmation;
use App\Models\Utility\SpecialLicense\SpecialLicenseType;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewSpecialLicenseController extends Controller
{
    private $view_path = "utility.special_license.panel.new_special_license.";
    private $route_path = "utility.special_license.panel.new_special_license.";

    public function index(SpecialLicenseType $special_license_type, $reference_id, $param1 = null, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
    {

        $reference = $special_license_type->GetReference($reference_id);
        if (!$reference) {
            return back()->withErrors("شناسه مرجع درخواست کننده نامعتبر است.");
        }
        if ($special_license_type->special_license_type_expert()->count() == 0) {
            return back()->withErrors("با توجه به اینکه هیچ خبره ای جهت تایید مجوز در سامانه تعریف نشده است، امکان ثبت مجوز وجود ندارد.");
        }
        if ($special_license_type->active_status_id == 1210) {
            return back()->withErrors("با توجه به اینکه مجوز " . $special_license_type->caption . " در سامانه غیر فعال می باشد، امکان ثبت مجوز وجود ندارد." .
                "<br/>جهت فعال سازی مجوز با واحد پشتیبانی تماس بگیرید.");
        }
        $object1 = $special_license_type->getObject1($param1);
        $object2 = $special_license_type->getObject2($param2);
        $object3 = $special_license_type->getObject3($param3);
        $worker = Worker::find(Auth::id());
        $packing_type_option = null;
        $product_shrinkage_info = null;
        $production_channel = null;
        $supplier_option = null;
        $unit_option = null;
        $carrier_group_option = null;
        $owner_personal_option = null;
        $product_list = [];
        $status_option = null;
        $carrier_option = null;
        $label_packing_type_option = null;
        $printer_unit_display_type_option = null;
        $packing_layer_options = [];
        $option_17 = [];
        $discharge_type_option = null;
        switch ($special_license_type->id) {
            case 2:
                $machine = $reference->machine;
                $production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");
                $last_machine_log = MachineLog::getLastLogWithContour($machine);
                if ($production_form && $last_machine_log) {

                    ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
                }

                if ($param1 + 0 <= 0) {

                    return redirect()->route("production.machine.index")->withErrors("مقدار  تولید محاسبه شده برای کارت تولید 0 می باشد، لطفا آخرین پیک دستگاه را از طریق ثبت اپراتور مسول ثبت  و یکبار دیگر تلاش کنید");
                }
                break;
            case 4:// افزودن بسته بندی به درخواست خروج از انبار
                $packing_type_option = Option::get("packing_type_product", 0, $object1->product_id);
                break;

            case 10: // مجوز جمع شدگی
                $product_shrinkage_info = session("product_shrinkage_info");

                // اگر در زمان کنترل کیفیت نیاز به مجوز داشته است، اطلاعات جمع شدکی در json ذخیره شده است.
                if ($reference->status_id == 7007026) {

                    $jsn_data_list = JsonDataList::where(
                        [
                            "other_id" => $reference->id,
                            "message_type_id" => 370,
                        ])->
                    first();

                    if ($jsn_data_list) {
                        $qc_data = json_decode($jsn_data_list->data, true);
                        $product_shrinkage_info = $qc_data["product_shrinkage_info"];
                    }
                }

                if (!$product_shrinkage_info) {
                    return back()->withErrors("اطلاعات بسته بندی یافت نشد، لطفا یک بار دیگر تلاش کنید.");
                }


                $product_ids = [];
                foreach ($product_shrinkage_info as $item) {
                    $product_ids[] = $item["product_id"];
                }
                $product_ids[] = -1;
                $product_list = Product::whereIn("id", $product_ids)->get()->keyBy("id");
            case 9:
                $supplier_option = Option::get("supplier");
                break;
            case 11:
                $production_channel = ProductionChannel::find($param1);
                break;
            case 12:
//                $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
//                if (!$result["result"]) {
//                    return back()->withErrors($result["error"]);
//                }
//                $result_owner_ic = SpecialLicense::ShowOwnerInIc(env("APP_NAME"), env("IC_APIKEY"));
//                if (!$result_owner_ic["result"]) {
//                    return back()->withErrors($result_owner_ic["error"]);
//                }
                $unit_option = Option::get("unit");
                $carrier_group_option = Option::get("carrier_group");
//                $owner_personal_option = Option::get("result_owner_ic");
            case 13:
                $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $result_owner_ic = SpecialLicense::ShowOwnerInIc(env("APP_NAME"), env("IC_APIKEY"));
                if (!$result_owner_ic["result"]) {
                    return back()->withErrors($result_owner_ic["error"]);
                }
                $discharge_type_option = Option::get("discharge_type");
                $status_option = Option::get("status", 0, 1100);
                $carrier_option = Option::get("carrier_type", 0);
                $label_packing_type_option = Option::get("label_packing_type_option");
                $printer_unit_display_type_option = Option::get("printer_unit_display_type");
                $owner_personal_option = Option::get("result_owner_ic");
                $packing_layer = [
                    0 => (object)["id" => 0, "caption" => "یک لایه"],
                    1 => (object)["id" => 1, "caption" => "دو لایه"]
                ];
                foreach ($packing_layer as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    $packing_layer_options[] = $option;
                }
                break;
            case 14:
                $max_show_packing_form_code_in_special_license = Setting::getIntegerValue("max_show_packing_form_code_in_special_license");
                if ($param2 > $max_show_packing_form_code_in_special_license) {
                    return back()->withErrors("با توجه به اینکه هنوز " . $param2 . " بسته بندی از کالا خوانده نشده است، امکان ثبت مجوز وجود ندارد." .
                        "<br/> برای ثبت مجوز باید حداقل " . ($param2 - $max_show_packing_form_code_in_special_license) . " بسته بندی دیگر خوانده شود.");
                }
                break;
            case 15:
                $param3 = Setting::getIntegerValue("max_day_for_special_license_15");

                break;

            case 17:
                $packing_form = $reference;
                $production = $object1;

                $production_channel_type_id = $param4;

                $contractor_id = $param3;
                $result_ss = ContractorAllocationController::GetOtherProduction($production_channel_type_id, $contractor_id, $production);

                $material_count = $packing_form->items()->count();
                if ($material_count != 1) {
                    return back()->withErrors("با توجه به اینکه چند نوع ماده اولیه در بسته بندی وجود دارد، امکان انتخاب دستور پیان وجود ندارد.");
                }
                $packing_form_item = $packing_form->items()->first();

                $product_ids = [];
                foreach ($result_ss["production_allocation_togethers"] as $item) {

                    $product_ids[] = $item["production"]->product->id;

                }

                $product_ids[] = -1;
                $consumed_product = Product\ConsumedProduct\ConsumedProduct::whereIn("product_id", $product_ids)->get();
                $consumed_product_ids = [];
                foreach ($consumed_product as $item) {
                    if (!isset($consumed_product_ids[$item->product_id])) {
                        $consumed_product_ids[$item->product_id] = [];
                    }
                    $consumed_product_ids[$item->product_id][] = $item->material_id;
                }


                foreach ($result_ss["production_allocation_togethers"] as $item) {

                    if ( is_array($consumed_product_ids[$item["production"]->product_id]) && in_array($packing_form_item->product_id, $consumed_product_ids[$item["production"]->product_id])) {
                        $option = ["value" => $item["production"]->id, "text" => $item["production"]->serial . " - " . $item["production"]->product->caption];
                        $option_17[] = $option;
                    }

                }

                if(count($option_17) == 0){
                    return back()->withErrors("هیچ کارت پیمانی جهت پیشنهاد یافت نشد، لطفا از انتخاب کانال تولید مرتبط با بسته بندی ".$packing_form->code." اطمینان حاصل کنید."
                    );
                }
                break;


        }
        $result_create = SpecialLicenseType::AllowCreate($special_license_type, $reference);
        if (!$result_create["result"]) {
            return back()->withErrors($result_create["error"]);
        }

        $description = session("description");
        return view($this->view_path . "index", compact(
            "special_license_type", "description", "reference", "worker",
            "param1", "param2", "param3", "param4", "param5", "object1", "object2", "object3", "discharge_type_option", "status_option", "label_packing_type_option", "carrier_option",
            "packing_type_option", "product_shrinkage_info", "product_list", "production_channel", "unit_option", "carrier_group_option", "owner_personal_option", "printer_unit_display_type_option",
            "supplier_option", "packing_layer_options", "option_17"
        ));
    }

    public function store(Request $request, SpecialLicenseType $special_license_type, $reference_id, $param1 = null, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
    {

        $reference = $special_license_type->GetReference($reference_id);
        if (!$reference) {
            return back()->withErrors("شناسه مرجع درخواست کننده نامعتبر است.");
        }

        if ($special_license_type->special_license_type_expert()->count() == 0) {
            return back()->withErrors("با توجه به اینکه هیچ خبره ای جهت تایید مجوز در سامانه تعریف نشده است، امکان ثبت مجوز وجود ندارد.");
        }

        $result_create = SpecialLicenseType::AllowCreate($special_license_type, $reference);
        if (!$result_create["result"]) {
            return back()->withErrors($result_create["error"]);
        }

        $object1 = $special_license_type->getObject1($param1);
        $object2 = $special_license_type->getObject2($param2);
        $object3 = $special_license_type->getObject3($param3);


        $old_special_license = SpecialLicense::where([
            "special_license_type_id" => $special_license_type->id,
            "reference_id" => $reference_id,
            "status_id" => 6040001 // در انتظار تایید مجوز
        ])->first();
        if ($old_special_license) {
            if (in_array($special_license_type->id, [12, 13, 4])) { // نوع حامل و بسته بندی
                // بتواند حداکثر سه درخواست باز داشته باشد.
                $special_license_exist = SpecialLicense::where([
                    "special_license_type_id" => $special_license_type->id,
                    "reference_id" => $reference_id,
                    "status_id" => 6040001 // در انتظار تایید مجوز
                ])->get();
                if (count($special_license_exist) > 4) {


                    return back()->withErrors("سه درخواست مجوز مشابه قبلا ثبت گردیده است و در انتظار تایید می باشد. ");

                }
            } else {
                return back()->withErrors("یک درخواست مجوز مشابه با شماره " . $old_special_license->code . " قبلا ثبت گردید و در انتظار تایید می باشد.");
            }
        }

        switch ($special_license_type->id) {
            case 1: // درخواست مجوز خروج بیشتر از انبار
                if ($request->param3) {
                    $param3 = $request->param3;
                }
                if ($param3 < $param2) {
                    return back()->withErrors("مقدار کالا برای خروج باید مقداری بزرگتر از مقدار درخواست (" . $param2 . ") باشد.");
                }
                break;
            case 3:
                if ($param3 == "input") {
                    // بررسی ورود
                    $start_datetime = Carbon::parse($request->start_datetime);
                    $param1 = $start_datetime;

                    // S' < exit
                    if ($start_datetime->greaterThan($reference->exit_datetime)) {
                        return back()->withErrors("تاریخ و ساعت وارد شده باید کوچکتر از " . $reference->exit_datetime() . "باشد.");
                    }

                    // entry < S' < exit & id!=refrence.id
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("entry_datetime", "<=", $start_datetime)->
                    where("exit_datetime", ">=", $start_datetime)->
                    where("id", "!=", $reference->id)->
                    first();

                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }

                    // S' < entry < E & id!=refrence.id
                    $reference;
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("entry_datetime", ">=", $start_datetime)->
                    when($reference->exit_datetime, function ($query) use ($reference) {
                        return $query->where("entry_datetime", "<", $reference->exit_datetime);
                    })->
                    where("id", "!=", $reference->id)->
                    first();
                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }

                    // S' < exit < E & id!=refrence.id
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("exit_datetime", ">=", $start_datetime)->

                    when($reference->exit_datetime, function ($query) use ($reference) {
                        return $query->where("exit_datetime", "<=", $reference->exit_datetime);
                    })->

                    where("id", "!=", $reference->id)->
                    first();
                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }


                    $param4 = $reference->entry_datetime;

                } elseif ($param3 == "output") {
                    // بررسی خروج
                    $end_datetime = Carbon::parse($request->end_datetime);
                    $param2 = $end_datetime;

                    // E' > entry
                    if ($end_datetime->lessThan($reference->entry_datetime)) {
                        return back()->withErrors("تاریخ و ساعت وارد شده باید بزرگتر از " . $reference->entry_datetime() . "باشد.");
                    }

                    // entry < E' < exit & id!=refrence.id
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("entry_datetime", "<=", $end_datetime)->
                    where("exit_datetime", ">=", $end_datetime)->
                    where("id", "!=", $reference->id)->
                    first();

                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }

                    // S < entry < E' & id!=refrence.id
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("entry_datetime", ">=", $reference->entry_datetime)->
                    where("entry_datetime", "<=", $end_datetime)->
                    where("id", "!=", $reference->id)->
                    first();
                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }

                    // S < exit < E' & id!=refrence.id
                    $between_entry_log = UserEntryLog::
                    where("user_id", $reference->user_id)->
                    where("exit_datetime", ">=", $reference->entry_datetime)->
                    where("exit_datetime", "<=", $end_datetime)->
                    where("id", "!=", $reference->id)->
                    first();
                    if ($between_entry_log) {
                        return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                    }
                    $param4 = $reference->exit_datetime;

                } else {
                    return back()->withErrors("اطلاعات ثبت شده نادرست است، لطفا یک بار دیگر تلاش کنید.");
                }

                break;
            case 4:
                $packing_type_id = $request->packing_type_id;
                $count = ProductPackingType::where(["product_id" => $object1->product_id, "packing_type_id" => $packing_type_id])->count();
                if ($count == 0) {
                    return back()->withErrors("نوع بسته بندی به درستی انتخاب نشده است.");
                }
                $param2 = $packing_type_id;

                // بعد از تایید مجوز، بسته بندی به لیست بسته بندی های مجاز کالا اضافه گردد.
                $param4 = $request->add_to_product_packing ? 1 : 0;
                break;
            case 8: // تغییر در مقدار درخواست
                $packing_code = $request->packing_code;

                $packing_form = PackingForm::where("code", "DCPK/" . $packing_code)->first();
                if (!$packing_form) {
                    session(["description" => $request->description]);
                    return back()->withErrors("کد بسته بندی وارد شده معتبر نمی باشد.");
                }
                if (!$packing_form->warehouse_id) {
                    return back()->withErrors("لطفا بسته بندی انتخاب نمایید که وضعیت آن موجود در انبار باشد.");
                }
                $param2 = $packing_form->id;
                $param3 = $packing_form->warehouse_id;
                break;
            case 9:
                $param1 = $request->supplier_id;
                break;
            case 10: //جمع شدگی
                $product_shrinkage_info = session("product_shrinkage_info");

                if ($reference->status_id == 7007026) {

                    $jsn_data_list = JsonDataList::where(
                        [
                            "other_id" => $reference->id,
                            "message_type_id" => 370,
                        ])->
                    first();

                    if ($jsn_data_list) {
                        $qc_data = json_decode($jsn_data_list->data, true);
                        $product_shrinkage_info = $qc_data["product_shrinkage_info"];
                    }
                }
                if (!$product_shrinkage_info) {
                    return back()->withErrors("اطلاعات بسته بندی یافت نشد، لطفا یک بار دیگر تلاش کنید.");
                }
                $jsn_data_list = JsonDataList::create([
                    "other_id" => 0,
                    "message_type_id" => 320,
                    "data" => json_encode($product_shrinkage_info)
                ]);
                $param1 = $jsn_data_list->id;
                break;
            case 12:
                $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $data = $request->all();
                $carrier_type_exist = CarrierType::where('id', $request->caption)->exists();
                if ($carrier_type_exist) {
                    return back()->withErrors('عنوان نوع حامل قبلا در سامانه ثبت شده است.');
                }
                $result_ic = CarrierType::ExistCarrierTypeInIC($data, env("IC_APIKEY"));
                if (!$result_ic["result"]) {
                    return back()->withErrors($result_ic["error"]);
                }
                $jsn_data_list = JsonDataList::create([
                    "other_id" => 0,
                    "message_type_id" => 320,
                    "data" => json_encode($data)
                ]);
                $param1 = $jsn_data_list->id;
                break;
            case 13:
                $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }

                if (!session()->has('packing_type')) {
                    $packing_type_data = $request->all();
                    $packing_type_data['active_status_id'] = 1200;
                    session(['packing_type' => $packing_type_data]);
                    if (isset($request->count_packing_layer) && $request->count_packing_layer == 1) {// اگر دولایه انتخاب کرده بود
                        return redirect()->route("utility.special_license.panel.new_special_license_13.index");
                    }
                }

                $packing_type = session("packing_type");
                if (isset($packing_type) && !$packing_type) {
                    return back()->withErrors("با خطایی مواجه شده اید لطفا دوباره امتحان کنید.");
                }
                $result_ic = PackingType::ExistPackingTypeInIC($packing_type, env("IC_APIKEY"));
                if (!$result_ic["result"]) {
                    return back()->withErrors($result_ic["error"]);
                }
                $jsn_data_list = JsonDataList::create([
                    "other_id" => 0,
                    "message_type_id" => 1900,
                    "data" => json_encode($packing_type)
                ]);
                $param1 = $jsn_data_list->id;
                session()->forget('packing_type');
                break;
            case 16:
                $worker = $reference;
                // بررسی ورود و خروج
                $start_datetime = Carbon::parse($request->start_datetime);
                $end_datetime = Carbon::parse($request->end_datetime);

                if ($end_datetime->lessThan($start_datetime)) {
                    return back()->withErrors("تاریخ و ساعت ورود به سازمان باید از تاریخ و ساعت خروج از سازمان کوچکتر باشد. ");
                }

                if ($end_datetime->greaterThan(Carbon::now()->addHour(1))) {
                    return back()->withErrors("تاریخ و ساعت خروج از سازمان باید حداقل 1 ساعت از زمان حال کوچکتر باشد. ");
                }

                $between_entry_log = UserEntryLog::
                where("user_id", $worker->id)->
                where("entry_datetime", "<=", $start_datetime)->
                where("exit_datetime", ">=", $start_datetime)->
                first();
                if ($between_entry_log) {
                    return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                }
                $between_entry_log = UserEntryLog::
                where("user_id", $worker->id)->
                where("entry_datetime", "<=", $end_datetime)->
                where("exit_datetime", ">=", $end_datetime)->
                first();
                if ($between_entry_log) {
                    return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                }

                $between_entry_log = UserEntryLog::
                where("user_id", $worker->id)->
                where("entry_datetime", "<=", $end_datetime)->
                whereNull("exit_datetime")->
                first();
                if ($between_entry_log) {
                    return back()->withErrors("با توجه به اینکه ثبت درخواست با تاریخ تردد " . $between_entry_log->entry_datetime() . " تداخل دارد، امکان ثبت مجوز مقدور نمی باشد.");
                }

                // حداکثر مدت زمان حضور در سامان
                $max_time_allowed_for_percent_in_company = Setting::getIntegerValue("max_time_allowed_for_percent_in_company");
                $diffInHours = $end_datetime->diffInHours($start_datetime);
                if ($diffInHours > $max_time_allowed_for_percent_in_company) {
                    return back()->withErrors("حداکثر مدت زمان حضور در سازمان " . $max_time_allowed_for_percent_in_company . " ساعت می باشد، بنابراین امکان ثبت تردد در بازه های درخواست شده امکان پذیر نمی باشد. ");
                }
                $param1 = $request->start_datetime;
                $param2 = $request->end_datetime;


                break;
            case 17:

                $replace_production = Production::find($request->replace_production_id);

                if (!$replace_production) {
                    return back()->withErrors("لطفا کارت پیمان جایگزین را مشخص نمایید.");
                }

                $production = $object1; // ممکن است کارت تولید برای کالا صادر نشده باشد.
                // بررسی کانال تولید های کالا ها
                $production_channel_types = LineProductStation::whereIn("product_id", [$production->product_id??0, $replace_production->product_id])->groupBy("product_id")->get();

                if ($production_channel_types->count() > 0) {
                    $first = $production_channel_types->first();
                    $second = $production_channel_types->skip(1)->first();

                    if ($second && $first->production_channel_type_id != $second->production_channel_type_id) {
                        $message = " کانال تولید های کارت بسته بندی و کارت جایگزین با هم برابر نیستند، برای ثبت مجوز لازم است، تا دو کانال تولید با هم برابر باشند." .
                            "<br/>" . " کانال تولید کالای بسته بندی:" . $first->production_channel_type->caption . "<br/>" .
                            "کانال تولید کارت جایگزین: " . $second->production_channel_type->caption;
                        // return back()->withErrors($message);
                    }
                }

                $param2 = $replace_production->id;
                break;

        }
        $special_license = SpecialLicense::create([
            "special_license_type_id" => $special_license_type->id,
            "reference_id" => $reference_id,
            "user_id" => Auth::id(),
            "status_id" => 6040001, // در انتظار تایید مجوز
            "param1" => $param1,
            "param2" => $param2,
            "param3" => $param3,
            "param4" => $param4,
            "param5" => $param5,
        ]);
        $special_license->getCode();

        // اضافه کردن پست های تایید کننده
        foreach ($special_license_type->special_license_type_expert_post as $expert) {
            SpecialLicenseConfirmation::create([
                "special_license_id" => $special_license->id,
                "post_id" => $expert->post_id,
                "priority_number" => $expert->priority_number,
                "committee_id" => null,
                "status_id" => 6040201 // در انتظار تایید خبره
            ]);
        }

        // اضافه کردن کمیته های تایید کننده
        foreach ($special_license_type->special_license_type_expert_committee as $expert) {
            foreach ($expert->committee->committee_post as $committee_post) {
                SpecialLicenseConfirmation::create([
                    "special_license_id" => $special_license->id,
                    "post_id" => $committee_post->post_id,
                    "priority_number" => $expert->priority_number,
                    "committee_id" => $expert->committee_id,
                    "status_id" => 6040201 // در انتظار تایید خبره
                ]);
            }
        }

        // اضافه کردن پست های شناور
        $post_user_list = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object");

        foreach ($special_license_type->GetSpecialLicenseTypeExpertFloatingPost($special_license_type) as $expert) {

            foreach ($post_user_list as $post_user) {
                $post_id = null;
                switch ($expert->floating_post_type_id) {
                    case 1: // خود فرد
                        $post_id = $post_user->post_id;
                        break;
                    case 2: // مدیر سطح 1
                        $post_id = $post_user->post->parent->id ?? null;
                        break;
                    case 3: // مدیر سطح 2
                        $post_id = $post_user->post->parent->parent->id ?? null;
                        break;
                    case 4: // مدیر سطح 3
                        $post_id = $post_user->post->parent->parent->parent->id ?? null;
                        break;
                    case 5: // مدیر سطح 4
                        $post_id = $post_user->post->parent->parent->parent->parent->id ?? null;
                        break;
                    default:
                        1 / 0;

                }

                if ($post_id) {
                    SpecialLicenseConfirmation::create([
                        "special_license_id" => $special_license->id,
                        "post_id" => $post_id,
                        "priority_number" => $expert->priority_number,
                        "committee_id" => null,
                        "status_id" => 6040201 // در انتظار تایید خبره
                    ]);
                }
            }

        }
        SpecialLicense::SendSmsToConfirmation($special_license);

        event(new SpecialLicenseEvent($special_license, 6040001, null, null, null, $request->description));
        SpecialLicense::SetLogForRefrence($special_license, "register");
        session(["description" => null]);
        return redirect($special_license_type->getBackUrl($reference, $param1, $param2, $param3, $param4, $param5))->with(["success" => "یک درخواست مجوز با موفقیت ثبت گردید."]);
    }
}
