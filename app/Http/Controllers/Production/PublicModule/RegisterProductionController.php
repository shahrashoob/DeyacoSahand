<?php

namespace App\Http\Controllers\Production\PublicModule;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Events\Utility\TransportLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\SendingMaterialController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\CompleteInformationController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Production\DashboardController;
use App\Http\Controllers\Warehouse\Pallet\PrintPalletController;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind\LotNumberProperty;
use App\Models\LineProduct\GoodsKind\GoodsKindLotNumberPropertyValue;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineTypeOutputBandWarehouse;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\StationOperation;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderPackingForm;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Production\ProductionPackingType;
use App\Models\Utility\Car\Car;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportForm;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Warehouse\Pallet\PalletItem;
use App\Models\Worker;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;


class RegisterProductionController extends Controller
{

    // production/public_module/register_production/
    public static $info = [
        "route" => "production.public_module.register_production.",
        "view_path" => "production.public_module.register_production.",
    ];
    var $view_path;
    var $route_path;
    var $max_of_copy_packng_form = 50;

    //
    public function __construct()
    {
        $this->route_path = RegisterProductionController::$info["route"];
        $this->view_path = RegisterProductionController::$info["view_path"];
    }

    public function index(MachineAllocation $machine_allocation)
    {


        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $machine_packing_list = MachineAllocationPackingForm::
        whereIn("machine_allocation_id", $machine_allocation->allocation->items()->pluck("id")->toArray())->
        where([
            "status_id" => 7007006, // بسته های معلق
        ])->
        when($machine_allocation->machine, function ($query) use ($machine_allocation) {
            return $query->where("machine_id", $machine_allocation->machine_id);
        })->
        when($machine_allocation->contractor, function ($query) use ($machine_allocation) {
            return $query->where("contractor_id", $machine_allocation->contractor_id);
        })->
        when($machine_allocation->order_id, function ($query) use ($machine_allocation) {
            return $query->where("order_id", $machine_allocation->order_id);
        })->
        with("machine_allocation.product")->
        with("machine_allocation.production")->
        get();

        $can_production_terminate = self::checkProductionTerminate($machine_allocation)["result"];
        $route_path = $this->route_path;

        $form_general_item = FormGeneralItem::where([
            "status_id" => 5002001 // معلق
        ])->
        where(function ($query) use ($machine_allocation) {
            return $query->where(
                "machine_allocation_id", $machine_allocation->id)// ->            OrWhere("allocation_id", $machine_allocation->allocation_id)
                ;
        })->
        get();

        $source_production_form_item = null;
        if ($machine_allocation->parent_allocation_id) {

            $result_source_item = self::GetSourceProductionFormItem($machine_allocation);
            if (!$result_source_item["result"]) {
                return back()->withErrors($result_source_item["error"]);
            }
            $source_production_form_item = $result_source_item["source_production_form_item"];

            if ($source_production_form_item) { // ممکن است که نتیجه T باشد ولی چیزی فرم نداشته باشیم ( بسته بندی رز رنگ) .
                $lot_number_id = $source_production_form_item->lot_numbers()->first()->lot_number_id;

                session(["default_lot_number_" . $machine_allocation->id => $lot_number_id]);
            }

        }


        return view($this->view_path . "index", compact("machine_packing_list", "route_path", "can_production_terminate", "machine_allocation", "form_general_item", "source_production_form_item"));
    }

    public function submit_register_production(Request $request, MachineAllocation $machine_allocation, $source_production_form_item_id)
    {


        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $result_register = RegisterProductionController::FinalRegisterProduction($machine_allocation, false);
        if ($result_register["result"]) {
            return redirect()->route($this->route_path . "index", $machine_allocation)->with([
                "success" => $result_register["message"]
            ]);
        } else {

            return redirect()->back()->withErrors($result_register["error"]);
        }


    }

    public function register_production_and_send_to_warehouse(MachineAllocation $machine_allocation, $source_production_form_item_id)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        // اگر نیاز به بارگیری وجود دارد، نمی تواند ثبت نهایی تولید و ارسال را با هم انجام دهد.
        if ($machine_allocation->InputFromLoadingRequired()) {
            return back()->withErrors("لطفا محصولات تولید شده  را ثبت نهایی کنید.");
        }
        $result_register = RegisterProductionController::FinalRegisterProduction($machine_allocation, true);
        if ($result_register["result"]) {
            $machine_allocation_packing_form_ids = isset($result_register["machine_allocation_packing_form_ids"]) ? $result_register["machine_allocation_packing_form_ids"] : null;
            $machine_allocation_input_form_ids = isset($result_register["machine_allocation_input_form_ids"]) ? $result_register["machine_allocation_input_form_ids"] : null;

            $result_sending = RegisterProductionController::SendingToWarehouse($machine_allocation, $machine_allocation_packing_form_ids, $machine_allocation_input_form_ids);

            if ($result_sending["result"]) {

                return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => $result_sending["message"]]);

            } else {
                return back()->withErrors($result_sending["error"]);
            }
        } else {

            if (isset($result_register["warning_terminate"])) {
                if (isset($result_register["machine_allocation_packing_form_ids"])) {
                    session([
                        "machine_allocation_packing_form_ids" => $result_register["machine_allocation_packing_form_ids"],
                    ]);
                }
                return redirect()->route($this->route_path . "terminate_production", [
                    $machine_allocation,
                    "register_production_and_send_to_warehouse"
                ]);
            }

            return redirect()->back()->withErrors($result_register["error"]);
        }
    }

    public function sending_packing_form(MachineAllocation $machine_allocation)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $allow_get_details = MachineAllocation::allowGetPackingFormDetails($machine_allocation);

        if ($allow_get_details) {
            $list = MachineAllocationPackingForm::where([
                "status_id" => $this->getStatus($machine_allocation, "packing_form_sending"),
                // بسته های در انتظار تحویل به انبار/پیمانکار
            ])->
            when($machine_allocation->machine, function ($query) use ($machine_allocation) {
                return $query->where([
                    "machine_id" => $machine_allocation->machine_id,

                ])->whereIn("machine_allocation_id", $machine_allocation->allocation->items()->pluck("id")->toArray());
            })->
            when($machine_allocation->contractor, function ($query) use ($machine_allocation) {
                return $query->where([
                    "contractor_id" => $machine_allocation->contractor_id,
                ])->whereIn("machine_allocation_id", $machine_allocation->allocation->items()->pluck("id")->toArray());
            })->
            when($machine_allocation->order, function ($query) use ($machine_allocation) {
                return $query->where([
                    "order_id" => $machine_allocation->order_id,
                    "machine_allocation_id" => $machine_allocation->id,
                ]);
            })->
            get();

            if (count($list) == 0) {
                return back()->withErrors("هیچ بسته بندی جهت ارسال به انبار وجود ندارد.");
            }
        } else {

            // لیست فرم های در انتظار بارگیری
            $list = FormGeneralItem::
            join("forms", "forms.id", "form_id")->
            where([
                "machine_allocation_id" => $machine_allocation->id,
                "forms.status_id" => 500000720, // در انتظار ارسال به کارفرما,
            ])->
            get();

            if (count($list) == 0) {
                return back()->withErrors("هیچ فرمی جهت ارسال به انبار وجود ندارد.");
            }
        }

        $car_type_option = Option::get("car_types");
        $car_list = Car::get();
        $car_option["items"] = [];
        foreach ($car_list as $car) {
            if (!$car->car_type) {
                return back()->withErrors("اطلاعات خودور نامعتبر است." . $car->id);
            }
            $car_option["items"][] = [
                "id" => $car->id,
                "value" => $car->id,
                "text" => $car->driver_firstname . " " .
                    $car->driver_lastname . "-" .
                    ($car->car_type->caption) . "-" .
                    $car->car_plaque
            ];
        }

        return view($this->view_path . "sending_packing_form", compact("list", "machine_allocation", "allow_get_details", "car_option", "car_type_option", "car_list"));


    }

    public function submit_sending_packing_form(Request $request, MachineAllocation $machine_allocation)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        if (MachineAllocation::allowGetPackingFormDetails($machine_allocation)) {
            if (!isset($request->packing_form_ids) || count($request->packing_form_ids) == 0) {
                return back()->withErrors("لطفا حداقل یک بسته را انتخاب نمایید.");
            }
        } else {
            if (!isset($request->input_form_ids) || count($request->input_form_ids) == 0) {
                return back()->withErrors("لطفا حداقل یک فرم را انتخاب نمایید.");
            }
        }

        $car_id = null;
        if ($machine_allocation->InputFromLoadingRequired()) {
            $car_id = $request->car_id;
            if ($car_id) {
                $car = Car::find($car_id);
            } else {
                $request["user_id"] = Auth::id();
                $car = Car::create($request->all());
                $car_id = $car->id;
            }
        }

        $machine_allocation_packing_form_ids = isset($request->packing_form_ids) ? array_keys($request->packing_form_ids) : null;
        $machine_allocation_input_form_ids = isset($request->input_form_ids) ? array_keys($request->input_form_ids) : null;


        if (self::CheckProductionTerminate($machine_allocation)["result"]) {

            session([
                "machine_allocation_packing_form_ids" => $machine_allocation_packing_form_ids,
                "machine_allocation_input_form_ids" => $machine_allocation_input_form_ids,
                "car_id" => $car_id
            ]);

            return redirect()->route($this->route_path . "terminate_production", [
                $machine_allocation,
                "sending_packing_form"
            ]);

        } else {

            $result = RegisterProductionController::SendingToWarehouse($machine_allocation, $machine_allocation_packing_form_ids, $machine_allocation_input_form_ids, $car_id);

            if ($result["result"]) {
                return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => $result["message"]]);

            } else {
                return back()->withErrors($result["error"]);
            }
        }


    }

    // ثبت نهایی تولید
    public static function FinalRegisterProduction(MachineAllocation $machine_allocation, $check_between = true)
    {

        if (MachineAllocation::allowGetPackingFormDetails($machine_allocation)) {
            $controller = new self();
            // جاری
            $current_allocation_id = $controller->getStatus($machine_allocation, "current_allocation_reserve");
            // خاتمه یافته
            $allocation_terminated_id = $controller->getStatus($machine_allocation, "next_allocation_terminated");
            $machine_allocation_list = $machine_allocation->allocation->items()->whereIn("status_id", [$current_allocation_id, $allocation_terminated_id])->get();
            if (count($machine_allocation_list) > 0) {
                $message = "";
                $error = "";
                $warning_terminate = null;
                $result_all = true;
                $min_production = 0;
                $max_production = null;
                $machine_allocation_packing_form_ids = [];
                $no_packing_form = 0;
                $production_form = null; // فرم تولیدی که همه بسته بندی ها داخل آن قرار دارند.
                $k = 0;
                foreach ($machine_allocation_list as $machine_allocation_item) {

                    $result = self::FinalRegisterWithDetails($machine_allocation_item, $check_between, false);

                    if ($result["result"] || isset($result["warning_terminate"])) {

                        $message .= " ثبت موفق (بلامانع) برای کارت  " . $machine_allocation_item->production->serial . "<br/>";
                        $warning_terminate = isset($result["warning_terminate"]) ? $result["warning_terminate"] : null;


                        if (isset($result["machine_allocation_packing_form_ids"])) {
                            $machine_allocation_packing_form_ids = array_merge($machine_allocation_packing_form_ids, $result["machine_allocation_packing_form_ids"]);
                        }


                    } elseif (!isset($result["warning_terminate"])) {
                        if (isset($result["error_type"]) && $result["error_type"] == "no_packing_form") {
                            // خطایی وجود ندارد.
                            $no_packing_form++;
                        } else {
                            $result_all = false;
                            $error .= " ثبت تولید با خطا برای کارت  " . $machine_allocation_item->production->serial . "<br/>" . $result["error"];
                        }
                    }

                    if (isset($result["warning_terminate"])) {
                        $result_all = false;
                    }

                    if (isset($result["min_production"])) {
                        $min_production = min($min_production, $result["min_production"]);
                    }
                    if (isset($result["max_production"])) {
                        $max_production = max($max_production, $result["max_production"]);
                    }

                    $k++;
                }

                $result_all_array = [
                    "result" => $result_all,
                    "message" => $message,
                ];

                if ($result_all) {
                    $production_form=$result["production_form"];
                    // وقتی فرم تولید را خاتمه یافته می کنیم که همه تخصیص های آن با موفقتی ثبت شده باشد.
                    if ($production_form) {
                        $production_form->status_id = $controller->getStatus($machine_allocation, "production_form_terminated"); // خاتمه یافته
                        $production_form->save();

                        event(new ProductionFormLogEvent($production_form, 7002014));
                    }
                }

                $result_all_array["min_production"] = $min_production;

                if ($max_production) {
                    $result_all_array["max_production"] = $max_production;
                }
                if ($error != "") {
                    $result_all_array["error"] = $message . "<br/>" . $error;
                }
                if ($warning_terminate) {
                    $result_all_array["warning_terminate"] = $warning_terminate;
                }

                if (count($machine_allocation_packing_form_ids) > 0) {
                    $result_all_array["machine_allocation_packing_form_ids"] = $machine_allocation_packing_form_ids;
                }

                return $result_all_array;
            } else {
                return self::FinalRegisterWithDetails($machine_allocation, $check_between);
            }


        } else {
            return self::FinalRegisterGeneral($machine_allocation, $check_between);
        }

    }

    /***
     * @param MachineAllocation $machine_allocation
     * @param $check_between
     * @param $source_production_form_item_id
     * @return array|null
     * ثبت نهایی تولید با جزئیات
     */

    public static function FinalRegisterWithDetails(MachineAllocation $machine_allocation, $check_between = true, $terminate_production_form = true)
    {
        $controller = new RegisterProductionController();

        $list_zero_packing = MachineAllocationPackingForm::
        join("packing_form_item", "machine_allocation_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where([
            "machine_allocation_id" => $machine_allocation->id,
            "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
        ])->
        where("final_amount", "<=", 0)->
        get();

        if (count($list_zero_packing) > 0) {
            return ["result" => false, "error" => "امکان ثبت بسته بندی با مقدار نهایی کمتر مساوی صفر وجود ندارد. "];
        }

        $list_complete_packing = MachineAllocationPackingForm::
        where([
            "machine_allocation_id" => $machine_allocation->id,
            "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
        ])->
        where("need_to_complete_information", 1)->
        get();

        if (count($list_complete_packing) > 0) {
            return [
                "result" => false,
                "error" => count($list_complete_packing) . " بسته بندی تکمیل اطلاعات نشده است، لطفا پس از تکمیل اطلاعات همه بسته بندی اقدام نمایید.  "
            ];
        }

        $packing_form_ids = [];
        $list = MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 7007006, // بسته های معلق
        ])->
        get();

        if (count($list) == 0) {
            return [
                "result" => false,
                "error" => "همه بسته بندی ها ثبت نهایی شده اند و هیچ بسته بندی در انتظار ثبت نهایی وجود ندارد.",
                "error_type" => "no_packing_form"
            ];
        }

        $machine_allocation_packing_form_ids = [];
//        $current_amount                      = 0;
        // بررسی درست بودن اقلام و بسته های فرعی
        foreach ($list as $item) {

            $machine_allocation_packing_form_ids[] = $item->id;

//            if ( $item->packing_form->items()->count() == 0 ) {
//                return [ "result" => false, "error" => " اقلام بسته بندی " . $item->packing_form->code . " خالی است." ];
//            }
//            $current_amount     += $item->packing_form->getFinalAmount();
            $packing_form_ids[] = $item->packing_form_id;

        }

        // گرفتن مقدار جاری
        $current_final_and_sub_amounts = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
        selectRaw("sum(final_amount) as final_amount,sum(sub_amount) as sub_amount,production_form_item_id")->
        groupBy("production_form_item_id")->
        get()->
        keyBy("production_form_item_id");


        $current_final_amount = 0;
        foreach ($current_final_and_sub_amounts as $production_form_item_id => $current_final_and_sub_amount) {
            $current_final_amount += $current_final_and_sub_amount["final_amount"];
        }


        $result_check_max = self::CheckProductionTerminate($machine_allocation, "check_max", $current_final_amount);
        if (!$result_check_max["result"]) {
            return $result_check_max;
        }

        $result_check_between = self::CheckProductionTerminate($machine_allocation, "check_between", $current_final_amount);
        if (!$result_check_between["result"] && $check_between) {
            $result_check_between["machine_allocation_packing_form_ids"] = $machine_allocation_packing_form_ids;

            return $result_check_between;
        }


        if (!$machine_allocation->order) { // اگر کالا امانی است، نیاز به فرم تولید ندارد.
            #544
            // گرفتن فرم تولیدی که باید برای آن بسته بندی ایجاد شود.
//            $production_form_item_id = session("production_form_item_" . $machine_allocation->id);
            //گرفتن فرم تولید در حال تولید
            $production_form_select = ProductionForm::join("production_form_item", "production_forms.id", "production_form_id")->
            where([
                    "allocation_id" => $machine_allocation->allocation_id,
                    "production_forms.status_id" => $controller->getStatus($machine_allocation, "production_form_in_production"),
                ]
            )->
//            when($production_form_item_id > 0, function ($query) use ($production_form_item_id) {
//                return $query->where("production_form_item.id", $production_form_item_id);
//            })->
            select("production_forms.id")->
            first();

            if ($production_form_select) {
                $production_form = ProductionForm::find($production_form_select->id);
            } else {

                return ["result" => false, "error" => " فرم تولید جاری یافت نشده، لطفا با پشیتبانی تماس بگیرید."];
//            // تعریف فرم تولید جدید
//            $production_form = ProductionForm::AddNewForm(
//                $machine_allocation->machine_id,
//                $item->packing_form->carrier_id ?? null,
//                0,
//                $item->packing_form->packing_type->id,
//                $machine_allocation->contractor_id,
//                $controller->getStatus( $machine_allocation, "production_form_in_production" )
//            );
//
//            ProductionFormItem::AddNewItem(
//                $machine_allocation->allocation_id,
//                $production_form->id,
//                $machine_allocation->production_id,
//                $machine_allocation->product_id,
//                1,
//                $controller->getStatus( $machine_allocation, "production_form_in_production" ),// در حال تولید
//                0
//            );
            }


            if (!$production_form || count($production_form->items) == 0) {
                return ["result" => false, "error" => "ردیف فرم تولید یافت نشده، لطفا با پشیتبانی تماس بگیرید." . "--" . $production_form->id ?? "***"];
            }

            $production_form_item_list = $production_form->items()->where("production_id", $machine_allocation->production_id)->get();

            foreach ($production_form_item_list as $production_form_item) {
                $production_form_item->status_id = $controller->getStatus($machine_allocation, "production_form_terminated"); // خاتمه یافته
                $production_form_item->getCode();
                $production_form_item->save();

                //اگر  بسته بندی معلق api مانده که ثبت نهایی نشده است باید  حذف شود.
                // دیگر نیاز به حذف بسته بندی ها نیست
//                $packing_form_must_delete_list = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
//                where("production_form_item_id", $production_form_item->id)->
//                whereNotIn("packing_forms.id", $packing_form_ids)->
//                where("packing_forms.status_id", 7007011)->
//                select("packing_forms.id")->
//                get();
//
//                foreach ($packing_form_must_delete_list as $packing_form_must_delete) {
//                    $packing_form_must_delete->items()->delete();
//                    $packing_form_must_delete->delete();
//
//                }
            }


            if ($terminate_production_form) {
                // به صورت پیش فرض فرم های تولید خاتمه یافته می شوند مگر اینکه بسته بندی های چند کارت باهم ثبت تولید شوند.
                $production_form->status_id = $controller->getStatus($machine_allocation, "production_form_terminated"); // خاتمه یافته
                $production_form->save();

                event(new ProductionFormLogEvent($production_form, 7002014));
            }

        }

        // تغییر وضعیت تخصیص بسته بندی
        MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 7007006, // بسته های معلق
        ])->
        update(["status_id" => $controller->getStatus($machine_allocation, "packing_form_sending")]);

        // تغییر وضعیت فرم بسته بندی با وضعیت در انتظار ارسال محصول
        PackingForm::whereIn("id", $packing_form_ids)->update([
            "status_id" => $controller->getStatus($machine_allocation, "packing_form_sending"),
            "warehouse_status_id" => 4204,//بسته  در مسیر تحویل به انبار
        ]);

        // تغییر وضعیت آیتم های فرم بسته بندی
        PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->update([
            "status_id" => $controller->getStatus($machine_allocation, "packing_form_sending")
        ]);

        // تغییر وضعیت بسته بندی های فرعی اگر ایجاد شده بود.
        PackingForm::whereIn("packing_form_master_id", $packing_form_ids)->
        update(["status_id" => $controller->getStatus($machine_allocation, "packing_form_sending")]);


        foreach ($list as $item) {

            // ثبت لاگ
            event(new PackingLogEvent($item->packing_form, 7007009)); // 7007033

        }

        // کارت تامین چون از قبل بسته بندی ها مشخص نیست، باید مقدار آن بروز روسانی شود.
        if ($machine_allocation->order) {
            // اگر تخصیص از نوع تامین است، باید یک فرم تولید خاتمه یافته ایجاد کنیم.
            if (!isset($production_form)) {
                //            // تعریف فرم تولید جدید
                $production_form = ProductionForm::AddNewForm(
                    $machine_allocation->machine_id,
                    null,
                    0,
                    $item->packing_form->packing_type->id,
                    null,
                    $controller->getStatus($machine_allocation, "production_form_terminated"), // خاتمه یافته
                    $machine_allocation->supplier_id
                );

                $production_form_item_supplier = ProductionFormItem::AddNewItem(
                    $machine_allocation->allocation_id,
                    $production_form->id,
                    $machine_allocation->production_id,
                    $machine_allocation->product_id,
                    1,
                    $controller->getStatus($machine_allocation, "production_form_terminated"),// خاتمه یافته
                    0,
                    $machine_allocation->version_code ?? null
                );
                $production_form_item_supplier->getCode();
                $first_packing_form_item_supplier = PackingFormItem::where("packing_form_id", $packing_form_ids)->first();
                $production_form_item_lot_number_supplier = ProductionFormItemLotNumber::create([
                    "production_form_id" => $production_form->id,
                    "production_form_item_id" => $production_form_item_supplier->id,
                    "lot_number_id" => $first_packing_form_item_supplier->lot_number_id
                ]);
            }

            PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->update([
                "production_form_item_id" => $production_form_item_supplier->id,
                "production_form_item_lot_number_id" => $production_form_item_lot_number_supplier->id,
            ]);

            $current_final_and_sub_amounts = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
            selectRaw("sum(final_amount) as final_amount,sum(sub_amount) as sub_amount,production_form_item_id")->
            groupBy("production_form_item_id")->
            get()->
            keyBy("production_form_item_id");
        }

        // این خط باید بررسی شود وقتی که برای مشتری می خواهیم ثبت نهایی تولید بزنیم، فرم تولید براش وجود ندارد.
        // برای کالاهای امانی نیاز به فرم تولید نیست. #544
        foreach ($production_form_item_list as $production_form_item) {
            $production_form_item->update([
                "amount" => $current_final_and_sub_amounts[$production_form_item->id]["final_amount"],
                "amount_after_control" => $current_final_and_sub_amounts[$production_form_item->id]["final_amount"],
                "final_amount" => $current_final_and_sub_amounts[$production_form_item->id]["final_amount"],
                "sub_amount" => $current_final_and_sub_amounts[$production_form_item->id]["sub_amount"],
            ]);
        }


        $message = "اطلاعات تولید با موفقیت ثبت گردید، لطفا  نسبت به ارسال محصول به " . ($machine_allocation->machine ? "انبار" : "کارفرما") . " اقدام نمایید.";

        return [
            "result" => true,
            "message" => $message,
            "machine_allocation_packing_form_ids" => $machine_allocation_packing_form_ids,
            "production_form" => $production_form ?? null
        ];
    }

    public static function FinalRegisterGeneral(MachineAllocation $machine_allocation, $check_between = true)
    {

        $form_general_item_list = FormGeneralItem::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 5002001 // معلق
        ])->
        get();
        if (count($form_general_item_list) == 0) {
            return [
                "result" => false,
                "error" => "همه ردیف ها ثبت نهایی شده اند و هیچ ردیفی در انتظار ثبت نهایی وجود ندارد."
            ];
        }

        $form_general_item_ids = FormGeneralItem::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 5002001 // معلق
        ])->
        pluck("id", "id")->toArray();

        // گرفتن مقدار جاری
        $current_final_amount = FormGeneralItem::
        join("forms", "forms.id", "form_id")->
        where([
            "machine_allocation_id" => $machine_allocation->id,
        ])->
        whereIn("form_general_item.status_id", [5002003, 5002002])->
        where("forms.status_id", "!=", 500000100)-> // عدم تایید ها را حذف می کنیم.
        sum("form_general_item.amount");

        // بررسی مقدار حداکثر تولید
        $result_check_max = self::CheckProductionTerminate($machine_allocation, "check_max", $current_final_amount);
        if (!$result_check_max["result"]) {
            return $result_check_max;
        }

        $result_check_between = self::CheckProductionTerminate($machine_allocation, "check_between", $current_final_amount);
        if (!$result_check_between["result"] && $check_between) {
            $result_check_between["form_general_item_ids"] = $form_general_item_ids;

            return $result_check_between;
        }

        $controller = new RegisterProductionController();

        // انتخاب انبار تحویل کالا
        $warehouse_result = self::GetWarehouseId($machine_allocation, $form_general_item_list[0]->degree);
        if (!$warehouse_result["result"]) {
            return $warehouse_result;
        }
        $warehouse_id = $warehouse_result["warehouse_id"];


        // ایجاد فرم ورود به انبار با وضعیت در انتظار ارسال به کارخانه
        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "allocation_id" => $machine_allocation->allocation_id,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => $controller->getStatus($machine_allocation, "trans_kind"), // دریافت از پیمان کار
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000720, // در انتظار ارسال به کارفرما
            "ic" => $controller->getIC($machine_allocation)
        ]);
        $form->getCode("DCRF");

        event(new FormLogEvent($form, ""));

        FormGeneralItem::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 5002001 // معلق
        ])->
        update([
            "status_id" => 5002002, // در انتظار تفکیک
            "form_id" => $form->id
        ]);

        $message = "اطلاعات تولید با موفقیت ثبت گردید، لطفا  نسبت به ارسال محصول به " . ($machine_allocation->machine ? "انبار" : "کارفرما") . " اقدام نمایید.";

        return [
            "result" => true,
            "message" => $message,
            "machine_allocation_input_form_ids" => [$form->id]
        ];
    }

    // ارسال به انبار
    public static function SendingToWarehouse(MachineAllocation $machine_allocation, $machine_allocation_packing_form_ids = null, $machine_allocation_input_form_ids = null, $car_id = null)
    {

        if (MachineAllocation::allowGetPackingFormDetails($machine_allocation)) {
            $controller = new self();
            // جاری
            $current_allocation_id = $controller->getStatus($machine_allocation, "next_allocation_reserve");
            // خاتمه یافته
            $next_allocation_terminated = $controller->getStatus($machine_allocation, "next_allocation_terminated");
            $allocation_terminated_id = $controller->getStatus($machine_allocation, "allocation_terminated");

            $machine_allocation_ids = MachineAllocation::
            where("allocation_id", $machine_allocation->allocation_id)->
            whereIn("status_id", [$current_allocation_id, $allocation_terminated_id, $next_allocation_terminated])->pluck("id")->
            toArray();
            return self::SendingPackingFromToWarehouse($machine_allocation, $machine_allocation_packing_form_ids, $car_id, null, $machine_allocation_ids);

        } else {
            return self::SendingFormToWarehouse($machine_allocation, $machine_allocation_input_form_ids, $car_id);
        }

    }

    public static function SendingPackingFromToWarehouse(MachineAllocation $machine_allocation, $machine_allocation_packing_form_ids, $car_id, $machine_allocation_packing_list = null, $machine_allocation_ids = [])
    {

        if ((!$machine_allocation_packing_form_ids || count($machine_allocation_packing_form_ids) == 0) && $machine_allocation_packing_list == null) {

            return ["result" => false, "error" => "لطفا حداقل یک بسته را انتخاب نمایید."];
        }

        $machine_allocation_ids[] = $machine_allocation->id;
        $controller = new RegisterProductionController();

        if (!$machine_allocation_packing_list) {
            $machine_allocation_packing_list = MachineAllocationPackingForm::where([
                "status_id" => $controller->getStatus($machine_allocation, "packing_form_sending"),
                // بسته های در انتظار تحویل به انبار/پیمانکار
            ])->
            when($machine_allocation->machine, function ($query) use ($machine_allocation, $machine_allocation_ids) {
                return $query->where([
                    "machine_id" => $machine_allocation->machine_id,
                ])->
                whereIn("machine_allocation_id", $machine_allocation_ids);
            })->
            when($machine_allocation->contractor, function ($query) use ($machine_allocation, $machine_allocation_ids) {
                return $query->where([
                    "contractor_id" => $machine_allocation->contractor_id,
                ])->
                whereIn("machine_allocation_id", $machine_allocation_ids);
            })->
            when($machine_allocation->order, function ($query) use ($machine_allocation) {
                return $query->where([
                    "order_id" => $machine_allocation->order_id,
                    // "machine_allocation_id" => $machine_allocation->id,
                ]);
            })->
            with("machine_allocation")->
            whereIn("id", $machine_allocation_packing_form_ids)->
            get();
        }

        $applicant_type_id = null;
        $applicant_id = null;
        if ($machine_allocation->contractor) {
            $applicant_id = $machine_allocation->contractor_id;
            $applicant_type_id = 20;
        }
        $warehouse_list = [];
        foreach ($machine_allocation_packing_list as $machine_allocation_packing_item) {
            $packing_form_degree = $machine_allocation_packing_item->packing_form->getDegree();
            if ($packing_form_degree == null) {
                return [
                    "result" => false,
                    "error" => "انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد."
                ];
            }

            $warehouse_result = self::GetWarehouseId($machine_allocation, $packing_form_degree);
            if (!$warehouse_result["result"]) {
                return $warehouse_result;
            }
            $warehouse_id = $warehouse_result["warehouse_id"];
            $warehouse_list[$machine_allocation_packing_item->machine_allocation_id] = $warehouse_id;
        }
//$controller->getNextFormStatusId($machine_allocation, 0, false);

        $form_list = [];

        if ($car_id) {
            $transport = Transport::create([
                "car_id" => $car_id,
                "user_id" => Auth::id(),
                "transport_type_id" => 1, // ورود
                "status_id" => 6010104,// در انتظار ورود به سازمان
            ]);
            $transport->getCode();
            event(new TransportLogEvent($transport, 6010101));

        }


        foreach ($machine_allocation_packing_list as $machine_allocation_packing_item) {
            $sub_packing_form_list = $machine_allocation_packing_item->packing_form->packing_form_contents;

            // به دست آوردن فرم ورود
            if (!isset($form_list[$machine_allocation_packing_item->allocation_id])) {

                // ایجاد یک فرم تولید برای کل بسته های هر تخصیص
                $form_allocation = Form::CreateFrom([
                    "order_id" => 0,
                    "order_list_id" => 0,
                    "allocation_id" => $machine_allocation_packing_item->machine_allocation->allocation_id,
                    "user_id" => Auth::user()->id,
                    "form_type_id" => 304,
                    "trans_kind" => $controller->getStatus($machine_allocation, "trans_kind"), // دریافت از پیمان کار
                    "warehouse_id" => $warehouse_list[$machine_allocation_packing_item->machine_allocation_id],
                    "status_id" => $controller->getNextFormStatusId($machine_allocation, 0, false),
                    "ic" => $controller->getIC($machine_allocation),
                    "applicant_id" => $applicant_id,
                    "applicant_type_id" => $applicant_type_id
                ]);

                $form_allocation->getCode("DCRF");
                event(new FormLogEvent($form_allocation, ""));

                // اضافه کردن به بار
                if (isset($transport)) {
                    TransportForm::create(["transport_id" => $transport->id, "form_id" => $form_allocation->id]);
                }

                $form_list[$machine_allocation_packing_item->allocation_id] = $form_allocation;

            } else {
                $form_allocation = $form_list[$machine_allocation_packing_item->allocation_id];
            }

            if (count($sub_packing_form_list) == 0) // ثبت آیتم های بسته بندی
            {
                foreach ($machine_allocation_packing_item->packing_form->items as $item) {
                    FormItem::create([
                        "form_id" => $form_allocation->id,
                        "packing_form_item_id" => $item->id,
                        "product_id" => $item->product_id,
                        "amount" => $item->amount,
                        "sub_amount" => $item->sub_amount,
                        "carrier_id" => $item->packing_form->carrier_id,
                        "degree_id" => $item->degree_id,
                        "lot_number_id" => $item->lot_number_id,
                        "packing_type_id" => $item->packing_form->packing_type_id,
                        "description" => "دریافت کالا با کد بسته بندی " . ($item->getCode())
                    ]);
                    $item->status_id = $controller->getStatus($machine_allocation, "packing_form_waiting_for_conform"); //  در انتظار تایید دریافت محصول
                    $item->save();

                }
            } else {
                // ثبت بسته بندی های فرعی در فرم تولید
                foreach ($sub_packing_form_list as $sub_packing_form) {
                    foreach ($sub_packing_form->items as $sub_packing_form_item) {
                        FormItem::create([
                            "form_id" => $form_allocation->id,
                            "packing_form_item_id" => $sub_packing_form_item->id,
                            "product_id" => $sub_packing_form_item->product_id,
                            "amount" => $sub_packing_form_item->amount,
                            "sub_amount" => $sub_packing_form_item->sub_amount,
                            "carrier_id" => $sub_packing_form_item->packing_form->carrier_id,
                            "degree_id" => $sub_packing_form_item->degree_id,
                            "lot_number_id" => $sub_packing_form_item->lot_number_id,
                            "packing_type_id" => $sub_packing_form->packing_type_id,
                            "description" => "دریافت کالا با کد بسته بندی " . ($sub_packing_form_item->getCode())
                        ]);

                    }
                }
            }

            $machine_allocation_packing_item->packing_form->status_id = $controller->getStatus($machine_allocation, "packing_form_waiting_for_conform"); //  در انتظار تایید دریافت محصول
            $machine_allocation_packing_item->packing_form->form_id = $form_allocation->id;
            $machine_allocation_packing_item->packing_form->save();
            event(new PackingLogEvent($machine_allocation_packing_item->packing_form, 7007005));

            // تغییر وضعیت حامل
            if ($machine_allocation_packing_item->packing_form->carrier) {
                $machine_allocation_packing_item->packing_form->carrier->SetStatus(5320007, null, 5320102, null, $machine_allocation->machine_id, $machine_allocation->contractor_id);
            }

            $machine_allocation_packing_item->status_id = $controller->getStatus($machine_allocation, "packing_form_end_item"); // در انتظار تحویل به انبار
            $machine_allocation_packing_item->save();
        }

        foreach ($form_list as $form) {
            event(new FormLogEvent($form));
        }

        return [
            "result" => true,
            "message" => "بسته ها با موفقیت ارسال شد.",
            "form_list" => $form_list
        ];

    }

    public
    static function SendingFormToWarehouse(MachineAllocation $machine_allocation, $input_forms_ids, $car_id)
    {
        if (count($input_forms_ids) == 0) {
            return ["result" => false, "error" => "لطفا حداقل یک ردیف را انتخاب نمایید."];
        }
        $forms = Form::whereIn("id", $input_forms_ids)->get();
        foreach ($forms as $form) {
            if ($form->status_id != 500000720) { // در انتظار ارسال به کارفرما
                return back()->withErrors("فرم " . $form->code . " در انتظار ارسال به کارفرما نمی باشد.");
            }
        }

        $controller = new RegisterProductionController();
        if ($car_id) {
            $transport = Transport::create([
                "car_id" => $car_id,
                "user_id" => Auth::id(),
                "transport_type_id" => 1, // ورود
                "status_id" => 6010104,// در انتظار ورود به سازمان
            ]);
            $transport->getCode();
            event(new TransportLogEvent($transport, 6010101));

        }

        foreach ($forms as $item) {
            if ($car_id) {
                TransportForm::create(["transport_id" => $transport->id, "form_id" => $item->id]);
            }
            $item->status_id = $controller->getNextFormStatusId($machine_allocation, 0, true);
            $item->save();
            event(new FormLogEvent($item));
        }

        return ["result" => true, "message" => " کالا (ها) با موفقیت ارسال شد."];
    }

//خاتمه یافته کردن کارت
    public
    function terminate_production(MachineAllocation $machine_allocation, $confirm_type)
    {

        $list = MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "status_id" => 7007006, // بسته های معلق
        ])->
        get();

        $current_amount = 0;
        foreach ($list as $item) {
            $current_amount += $item->packing_form->getFinalAmount();
        }


        $result_can_production_terminate = self::CheckProductionTerminate($machine_allocation, "can_terminate", $current_amount);
        if ($result_can_production_terminate["result"]) {
            return view($this->view_path . "terminate_production", compact("machine_allocation", "result_can_production_terminate", "confirm_type"));
        } elseif (isset($result_can_production_terminate["warning"])) {
            // یعنی در ماژول ثبت تولید ( گزوه ماشین های عمومی) ثبت و ارسال به انبار را زدند ولی چون خاتمه یافته شدن آن باید در مازول پایان عمیلیات باشد، خاتمه یافته نمی شود.
            return $this->register_production_and_send_to_warehouse_action($machine_allocation, "no_teminate");
        }


        return back()->withErrors("با توجه به مقدار تولید شده امکان خاتمه یافته کردن وجود ندارد.");

    }


    public
    function submit_terminate_production(Request $request, MachineAllocation $machine_allocation)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }


        $confirm_type = $request->confirm_type;
        $answer_result = $request->answer_result;

        switch ($confirm_type) {
            case "terminate": // فقط خاتمه یافته کردن
                if ($answer_result == "yes") {
                    $machine_allocation_ids = $machine_allocation->allocation->items()->pluck("id")->toArray();
                    $list_count = MachineAllocationPackingForm::where([
                        "status_id" => 7007006, // بسته های معلق
                    ])->
                    whereIn("machine_allocation_id", $machine_allocation_ids)->
                    count();
                    if ($list_count > 0) {
                        return back()->withErrors("با توجه به اینکه " .
                            $list_count
                            . " بسته بندی در انتظار ثبت نهایی تولید هستند، امکان خاتمه یافته کردن کارت وجود ندارد."
                        );
                    }
                    // بسته بندی های در انتظار تحویل به انبار
                    $list_count = MachineAllocationPackingForm::where([
                        "status_id" => self::getStatus($machine_allocation, "packing_form_sending")
                    ])->
                    whereIn("machine_allocation_id", $machine_allocation_ids)->
                    count();
                    if ($list_count > 0) {
                        return back()->withErrors("با توجه به اینکه " .
                            $list_count
                            . " بسته بندی در انتظار " . ($machine_allocation->machine ? " ارسال به انبار " : " ارسال به کارفرما ") . " هستند، امکان خاتمه یافته کردن کارت وجود ندارد."
                        );
                    }
                    $form_general_item_count = FormGeneralItem::where([
                        "machine_allocation_id" => $machine_allocation->id,
                        "status_id" => 5002001, //  معلق
                    ])->
                    count();
                    if ($form_general_item_count > 0) {
                        return back()->withErrors("با توجه به اینکه " .
                            $form_general_item_count
                            . " ردیف در انتظار ثبت نهایی تولید هستند، امکان خاتمه یافته کردن کارت وجود ندارد."
                        );
                    }
                }

                return $this->terminate_result($machine_allocation, $answer_result, "");
                break;

            case "register_production":

                $result = RegisterProductionController::FinalRegisterProduction($machine_allocation, false, null);

                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }

                return $this->terminate_result($machine_allocation, $answer_result, "");
                break;

            case "register_production_and_send_to_warehouse":


                return $this->register_production_and_send_to_warehouse_action($machine_allocation, $answer_result);

                break;

            case "sending_packing_form":

                $machine_allocation_packing_form_ids = null;
                $machine_allocation_input_form_ids = null;
                if (MachineAllocation::allowGetPackingFormDetails($machine_allocation)) {
                    $machine_allocation_packing_form_ids = session("machine_allocation_packing_form_ids");
                    if (!$machine_allocation_packing_form_ids) {
                        return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
                    }
                } else {
                    $machine_allocation_input_form_ids = session("machine_allocation_input_form_ids");
                    if (!$machine_allocation_input_form_ids) {
                        return back()->withErrors("لطفا حداقل یک فرم را انتخاب نمایید.");
                    }
                }
                $car_id = session("car_id");

                $result = RegisterProductionController::SendingToWarehouse($machine_allocation, $machine_allocation_packing_form_ids, $machine_allocation_input_form_ids, $car_id);

                if ($result["result"]) {

                    return $this->terminate_result($machine_allocation, $answer_result, "");
                } else {
                    return back()->withErrors($result["error"]);
                }


                break;
        }


        return back()->withErrors("با توجه به مقدار تولید شده امکان خاتمه یافته کردن وجود ندارد.");

    }

    /*
     * این تابع فقط فسته بندی ها را ثبت نهایی و ارسال به انبار می کند، اگر جواب خاتمه یافته کردن yes باشد، کارت را هم خاتمه یافته می کند.
     */
    public function register_production_and_send_to_warehouse_action($machine_allocation, $answer_result)
    {
        $result = RegisterProductionController::FinalRegisterProduction($machine_allocation, false, null);
        if ($result["result"]) {

            $machine_allocation_packing_form_ids = isset($result["machine_allocation_packing_form_ids"]) ? $result["machine_allocation_packing_form_ids"] : null;
            $machine_allocation_input_form_ids = isset($result["machine_allocation_input_form_ids"]) ? $result["machine_allocation_input_form_ids"] : null;

            $result = RegisterProductionController::SendingToWarehouse($machine_allocation, $machine_allocation_packing_form_ids, $machine_allocation_input_form_ids);

            if ($result["result"]) {

                return $this->terminate_result($machine_allocation, $answer_result, $result["message"]);
            } else {
                return back()->withErrors($result["error"]);
            }
        } else {

            return redirect()->back()->withErrors($result["error"]);
        }
    }

    public
    function terminate_result($machine_allocation, $answer_result, $message = "")
    {

        if ($answer_result == "yes") {
            $result = self::CheckProductionTerminate($machine_allocation);
            if ($result["result"]) {
                $result_terminate = self::ProductionTerminate($machine_allocation);
                if (!$result_terminate["result"]) {
                    return redirect()->route("production.public_module.register_production.index", $machine_allocation)->withErrors($result_terminate["error"]);
                }
                $message1 = $machine_allocation->getTextOfThing("production_caption") .
                    " با موفقیت خاتمه یافته شد.";
                return redirect()->route("production.public_module.register_production.index", $machine_allocation)->with(["success" => $message1]);
            } else {
                return redirect()->route("production.public_module.register_production.index", $machine_allocation)->withErrors($result["error"]);

            }
        } else {
            return redirect()->route("production.public_module.register_production.index", $machine_allocation)->with(["success" => $message]);
        }
    }

    /**
     * @param MachineAllocation $machine_allocation
     * @param $type
     * @param $current_amount
     * @return array|void
     * چک کردن اینکه آیا کالت خاتمه یافته شود یا خیر
     */
    public
    static function CheckProductionTerminate(MachineAllocation $machine_allocation, $type = "can_terminate", $current_amount = 0, $check_module_type = true)
    {

        $controller = new RegisterProductionController();
        $production_amount = ProductionFormItem::
        where("production_id", $machine_allocation->production_id)->
        where("allocation_id", $machine_allocation->allocation_id)->
        where("status_id", "!=", 7302004)-> // // تزریق شده به ماشین
        sum("final_amount");
        $production_amount = $production_amount + $current_amount;

        $general_item_amount = FormGeneralItem::
        join("forms", "forms.id", "form_id")->
        where([
            "machine_allocation_id" => $machine_allocation->id,
        ])->
        whereIn("form_general_item.status_id", [5002003, 5002002])->
        where("forms.status_id", "!=", 500000100)-> // عدم تایید ها را حذف می کنیم.
        sum("form_general_item.amount");;

        $production_amount += $general_item_amount;

        // بررسی درصد مجاز اختلاف کالای تولید شده با مقدار تخصیص کارت در زمان ثبت نهایی تولید و ارسال به کارفرما
        $allow_diff_percent = $machine_allocation->production->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;
        $max_allow_diff_percent = $machine_allocation->production->product->goods_kind->max_diff_of_production_and_allocation_in_the_end_of_production;
        $max_production = $machine_allocation->allocation_amount * (1 + $max_allow_diff_percent / 100);
        $min_production = $machine_allocation->allocation_amount * (1 - $allow_diff_percent / 100);

        switch ($type) {
            // با توجه به حداکثر مقدار آیا می توان مقدار جدید ثبت تولید کرد یا خیر
            case "check_max":
                if ($production_amount > $max_production) {
                    return [
                        "result" => false,
                        "max_production" => $max_production,
                        "min_production" => $min_production,
                        "production_amount" => $production_amount,
                        "error" =>
                            "با توجه به اینکه حداکثر مقدار مجاز برای تولید این " .
                            $machine_allocation->getTextOfThing("production_caption") . " " .
                            $max_production . " " .
                            $machine_allocation->product->unit->capiton .
                            " می باشد، امکان ثبت  جدید وجود ندارد."
                    ];
                }

                return [
                    "result" => true,
                    "max_production" => $max_production,
                    "min_production" => $min_production,
                    "production_amount" => $production_amount,
                ];
                break;
            case "check_between":
                if ($production_amount < $max_production &&
                    $production_amount > $min_production
                ) {
                    return [
                        "result" => false,
                        "max_production" => $max_production,
                        "min_production" => $min_production,
                        "warning_terminate" => "تایید خاتمه یافته کردن کارت از کاربر دریافت شود.",
                        "error" => ""
                    ];
                }

                return [
                    "result" => true,
                    "max_production" => $max_production,
                    "min_production" => $min_production,
                ];
                break;
            case "can_terminate":
                if ($machine_allocation->status_id ==
                    $controller->getStatus($machine_allocation, "allocation_terminated")
                ) {
                    return [
                        "result" => false,
                        "production_amount" => $production_amount,
                        "allow_diff_percent" => $allow_diff_percent,
                        "max_production" => $max_production,
                        "min_production" => $min_production,
                        "error" => "این تخصیص قبلا خاتمه یافته شده است."
                    ];
                }

                // اگر مازول عمومی تکمیل است نیاز به خاتمه یافتن نیست
                if ($check_module_type && $machine_allocation->machine && $machine_allocation->machine->machine_type->machine_module_type_id == 4) {
                    return [
                        "result" => false,
                        "production_amount" => $production_amount,
                        "allow_diff_percent" => $allow_diff_percent,
                        "max_production" => $max_production,
                        "min_production" => $min_production,
                        "error" => "خاتمه یافته کردن تخصیل در ماژول عمومی ماشین باید انجام شود.",
                        "warning" => "خاتمه یافته کردن تخصیل در ماژول عمومی ماشین باید انجام شود.",
                    ];
                }

//                $general_item_not_register = FormGeneralItem::where( [
//                    "machine_allocation_id" => $machine_allocation->id,
//                ] )->
//                whereIn( "status_id", [ 5002003, 5002002 ] )->
//                sum( "amount" );;
//                if()


                if ($production_amount >= $min_production) {
                    return [
                        "result" => true,
                        "production_amount" => $production_amount,
                        "allow_diff_percent" => $allow_diff_percent,
                        "max_production" => $max_production,
                        "min_production" => $min_production,
                    ];
                }

                return [
                    "result" => false,
                    "production_amount" => $production_amount,
                    "allow_diff_percent" => $allow_diff_percent,
                    "max_production" => $max_production,
                    "min_production" => $min_production,
                    "error" => "با توجه به اینکه حداقل باید $min_production " . $machine_allocation->product->unit->caption . " از مقدار تخصیص تولید شود، امکان ثبت خاتمه یافته کردن (پایان عملیات تولید) وجود ندارد." . "
                    <br/>مقدار تولید شده:" . $production_amount . " " . $machine_allocation->product->unit->caption
                ];
                break;
        }

    }

    public
    static function ProductionTerminate(MachineAllocation $machine_allocation_default)
    {

        $controller = new RegisterProductionController();
        // بررسی درصد مجاز اختلاف کالای تولید شده با مقدار تخصیص کارت در زمان ثبت نهایی تولید و ارسال به کارفرما
        $allow_diff_percent = $machine_allocation_default->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;

        $result = self::CheckAllocationTerminate($machine_allocation_default->allocation, $allow_diff_percent);
        if (!$result["result"]) {
            return $result;
        }
        $production_amount_list = $result["production_amount_list"];

        // وقتی یک کارت را خاتمه یافته می کنیم، تمامی آیتم های آن را باید خاتمه یافته کنیم.
        foreach ($machine_allocation_default->allocation->items as $machine_allocation) {


            $allocation_terminate = true; //  اگر برای ماشین های تکمیل است، تخصیص را خاتمه یافته نمی کنیم.
            $production_amount = $production_amount_list[$machine_allocation->production_id];


            if ($production_amount >= $machine_allocation->production->number * (1 - $allow_diff_percent / 100)) {

                if ($machine_allocation->production->status_id != 520) {
                    $machine_allocation->production->status_id = 520;
                    $machine_allocation->production->waiting_status_id = $controller->getStatus($machine_allocation, "production_card_terminated");
                    $machine_allocation->production->save();
                    event(new ProductionCardLogEvent($machine_allocation->production));
                }
                //اگر ماژول ماشین عمومی تکمیل است، پایان عملیات را اجرا می کنیم و نیاز نیست که تخصیص را خاتمه یافته کنیم.
                if ($machine_allocation->machine && $machine_allocation->machine->machine_type->machine_module_type_id == 4) {
                    $allocation_terminate = false;
                }
            }

// اگر لازم است تخصیص هم خاتمه یافته شود، همه ردیف های تخصیص هم خاتمه یافته شوند.
            if ($allocation_terminate && $production_amount >= $machine_allocation->allocation_amount * (1 - $allow_diff_percent / 100)) {


                $machine_allocation->allocation->status_id = $controller->getStatus($machine_allocation, "allocation_terminated");
                $machine_allocation->allocation->save();

                $machine_allocation->status_id = $controller->getStatus($machine_allocation, "allocation_terminated");
                $machine_allocation->save();

                if ($machine_allocation->machine) {
                    event(new ProductionCardLogEvent($machine_allocation->production));
                } else {
                    $contractor_allocation = ContractorAllocation::find($machine_allocation->id);
                    event(new ContractorLogEvent($contractor_allocation->contractor, 5310109, $contractor_allocation->production, $contractor_allocation));

                }


            }

        }


        return [
            "result" => true
        ];

    }

    /**
     * @param Allocation $allocation
     * @return array|void
     * بررسی اینکه آیا می توان تمامی ردیف های تخصیص را خاتمه یافته کرد یا خیر
     */
    public
    static function CheckAllocationTerminate(Allocation $allocation, $allow_diff_percent)
    {
        $production_amount_list = [];
        $controller = new RegisterProductionController();

        // بررسی درصد مجاز اختلاف کالای تولید شده با مقدار تخصیص کارت در زمان ثبت نهایی تولید و ارسال به کارفرما
        // $allow_diff_percent

        $error_message = "";
        // وقتی یک کارت را خاتمه یافته می کنیم، تمامی آیتم های آن را باید خاتمه یافته کنیم.
        foreach ($allocation->items as $machine_allocation) {

            $result_machine_allocation = false; // نتیجه خاتمه یافته کردن یک آیتم تخصیص


            $production_amount = ProductionFormItem::
            where("production_id", $machine_allocation->production->id)->
            where("status_id", "!=", 7302004)-> // // تزریق شده به ماشین
            where("allocation_id", $machine_allocation->allocation_id)->
            sum("final_amount");

            if ($production_amount == 0) {
                $production_amount += FormGeneralItem::
                where("machine_allocation_id", $machine_allocation->id)->
                where("allocation_id", $allocation->id)->
                sum("amount");
            }

            $production_amount_list[$machine_allocation->production->id] = $production_amount;

            if ($production_amount >= $machine_allocation->allocation_amount * (1 - $allow_diff_percent / 100)) {

                $result_machine_allocation = true;
            }


            if ($result_machine_allocation == false) {
                if ($production_amount == 0) {
                    $error_message .= "با توجه به اینکه مقدار تولید شده از کارت " . $machine_allocation->production->serial() . " برابر با صفر می باشد، امکان خاتمه یافته کردن کارت وجود ندارد."
                        . "<br/>";
                } else {
                    $error_message .= "با توجه به اینکه " . $production_amount . " " . $machine_allocation->product->unit->caption . " از کارت، تولید شده است، امکان  خاتمه یافته کردن قبل از به حد نصاب رسیدن مقدار تولید  " . $machine_allocation->production->serial() . " وجود ندارد، " . "<br/>";
                }
            }
        }

        return [
            "result" => $error_message == "",
            "error" => $error_message,
            "production_amount_list" => $production_amount_list,
        ];

    }

    public
    function add_new_packing(MachineAllocation $machine_allocation, $packing_form = null, $source_production_form_item_id = 0)
    {

        $worker = Worker::find(Auth::id());
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیشفرض سیستم را انتخاب کنید.");
        }


        if ($machine_allocation->production->waiting_status_id == 5310106 || $machine_allocation->status_id == 5310020) {
            if ($machine_allocation->machine) {
                return redirect()->route("production.machine.index")->withErrors("کارت تولید خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            } else {
                return redirect()->route("contractor.panel.dashboard.index")->withErrors("دستور پیمان خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            }

        }

        $source_production_form_item = null;
        if ($machine_allocation->parent_allocation_id && $source_production_form_item_id != 0) {
            $source_production_form_item = ProductionFormItem::find($source_production_form_item_id);
            if (!$source_production_form_item || $source_production_form_item->allocation_id != $machine_allocation->parent_allocation_id) {
                return back()->withErrors("اطلاعات فرم تولید در انتظار تزریق ماشین نامعتبر است، لطفا با پشتیبانی تماس بگیرید.");
            }
        }

        // اگر لات پیش فرض را ارسال کرده بودیم، دیگر نیاز نیست که لات جدید دریافت کند.
        $default_lot_number_id = session("default_lot_number_" . $machine_allocation->id);
        $default_lot_number = null;
        if ($default_lot_number_id) {
            $default_lot_number = LotNumber::where([
                "id" => $default_lot_number_id,
                "product_id" => $machine_allocation->product_id
            ])->first();
        }

        // گرفتن فرم تولیدی که باید برای آن بسته بندی ایجاد شود.
        // $production_form_item_id = session("production_form_item_" . $machine_allocation->id);
        /************************************/
        // گرفتن باسکول
        if ($machine_allocation->machine) {
            $route_path = "production.machine.index";
        } else {
            $route_path = "contractor.panel.dashboard.index";
        }
        $url_scale = route("hr.personal.select_smart_object", [2, $route_path, 0]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $route_path, 0])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/

        $checking_form_not_delivered_at_register_production = $this->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production");
        if ($checking_form_not_delivered_at_register_production) {

            $checking_form_status_ids = $this->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production_status_ids");
            $packing_form_list = PackingForm::join("machine_allocation_packing_form", "packing_form_id", "packing_forms.id")->
            where("machine_allocation_id", $machine_allocation->id)->
            whereIn("packing_forms.status_id", $checking_form_status_ids)->
            select("packing_forms.*")->
            get();

            $count_packing_form = count($packing_form_list);

            if ($count_packing_form > 0) {

                $message_add_packing = "با توجه به اینکه بسته بندی های زیر توسط "
                    . ($machine_allocation->machine ? " انبار " : " پیمانکار ") .
                    "تایید نشده است امکان افزودن بسته بندی جدید وجود ندارد.";
                $k = 0;
                foreach ($packing_form_list as $item) {
                    $k++;
                    $message_add_packing .= "<br/> $k - " . $item->getCode() . "(" . $item->status->caption . ")";
                    if ($k > 4) {
                        break;
                    }
                }
                if ($count_packing_form > $k) {
                    $message_add_packing .= "<br/>" . " و " . ($count_packing_form - $k) . " بسته بندی دیگر";
                }


                return back()->withErrors($message_add_packing);

            }
        }


        if ($machine_allocation->status_id == $this->getStatus($machine_allocation, "allocation_terminated")) {
            return back()->withErrors("با توجه به اینکه " . ($machine_allocation->machine ? "کارت تولید" : "دستور پیمان") . " خاتمه یافته است، امکان ثبت تولید وجود ندارد.");
        }
        $user_id = $worker->id;

        $machine = $machine_allocation->machine;

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $result = RegisterProductionController::getProductionResult("new_packing", $machine_allocation, $packing_form->id ?? null);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $degree_option = $result["degree_option"];
        $packing_type_option = $result["packing_type_option"];
        $packing_type_layer_count = $result["packing_type_layer_count"];
        $get_carrier_code = $result["get_carrier_code"];
        $is_complete_information = -1;

        $agent = new Agent();
        $is_mobile = $agent->isMobile();
        $pallet = Pallet::CurrentPallet($machine_allocation->allocation);


        // بررسی حداکثر مقدار مجاز قابل تولید
        $current_amount = MachineAllocationPackingForm::
        join("packing_form_item", "packing_form_item.packing_form_id", "machine_allocation_packing_form.packing_form_id")->
        where([
            "machine_allocation_id" => $machine_allocation->id,
            "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
        ])->
        sum("final_amount");
        $result_check_max = self::CheckProductionTerminate($machine_allocation, "check_max", $current_amount);
        if (!$result_check_max["result"]) {
            $message_add_packing = $result_check_max["error"];
        }

        // بررسی اینکه اگر کارت از مقدار تخصیص بیشتر تولید کرده است و کارت بعدی دارد، پیام بدهد و برور روی تخصیص بعدی
        $next_machine_allocation = null;
        $controller = new self();
        $allocation_terminated_id = $controller->getStatus($machine_allocation, "next_allocation_terminated");
        $reserve_status_id = $controller->getStatus($machine_allocation, "next_allocation_reserve");

        if ($result_check_max["production_amount"] >= $machine_allocation->allocation_amount) {

            $next_machine_allocation = MachineAllocation::
            where(["allocation_id" => $machine_allocation->allocation_id,
                "status_id" => $reserve_status_id])->
            where("id", "!=", $machine_allocation->id)->
            first();

            if ($next_machine_allocation) {
                $controller = new self();
                $result_next = $controller->GoToNextMachineAllocation($machine_allocation);
                if (!$result_next["result"]) {
                    return back()->withErrors($result_next["error"]);
                }
                $machine_allocation = $result_next["next_machine_allocation"];

                return $this->add_new_packing($machine_allocation, $packing_form, $source_production_form_item_id);
            }

        }

        $barcode_algorithm_id = $this->getStatus($machine_allocation, "barcode_algorithm_id");

        return view($this->view_path . "add_new_packing", compact(
            "degree_option", "machine_allocation", "machine", "user_id", "barcode_algorithm_id",
            "packing_type_option", "packing_type_layer_count", "is_complete_information",
            "url_scale", "is_mobile", "allocation_terminated_id", "get_carrier_code",
            "smart_object", "default_lot_number", "source_production_form_item", "source_production_form_item_id", "pallet"
        ));
    }

    public
    static function getProductionResult($type, $machine_allocation, $packing_form_id, $degree_id = 0, $packing_type_id = 0)
    {

        $goods_kind_id = $machine_allocation->product->goods_kind_id;
        $production = $machine_allocation->production;

        switch ($type) {
            case "new_packing":// بسته بندی جدید
                $degree_option = Option::get("degree", $degree_id, $goods_kind_id);;
                $packing_type_option = Option::get("production_packing_type", $packing_type_id, 0, $production->packing_types);
                $first_packing_type = $production->packing_types()->first()->packing_type;
                if (count($production->packing_types) == 0) {
                    return [
                        "result" => false,
                        "error" => "نوع بسته بندی برای دستور پیمان ثبت نشده است، لطفا با پشتیبانی تماس بگیرید."
                    ];

                }
                $packing_type_layer_count = [];
                $get_carrier_code = false;
                foreach ($production->packing_types as $item) {
                    $layer_list = $item->packing_type->layers()->with("carrier_type")->get();
                    $packing_type_layer_count[$item->packing_type->id] = count($layer_list);
                    foreach ($layer_list as $item_layer) {
                        if ($item_layer->carrier_type && $item_layer->carrier_type->has_number_ability) {
                            $get_carrier_code = true;
                        }
                    }

                }

                return [
                    "result" => true,
                    "view" => "add_new_packing_1_layer",
                    "first_packing_type" => $first_packing_type,
                    "packing_type_option" => $packing_type_option,
                    "degree_option" => $degree_option,
                    "packing_type_layer_count" => $packing_type_layer_count,
                    "machine_allocation" => $machine_allocation,
                    "get_carrier_code" => $get_carrier_code
                ];


                break;


        }
    }

// اضافه کردن بسته بندی
    public
    function add_new_product_to_packing_api(Request $request)
    {

        $machine_allocation = MachineAllocation::find($request->machine_allocation_id);
        $lot_number_code = $request->lot_number_code;
        $amount = $request->amount;
        $sub_amount = $request->sub_amount;
        $gross_weight = $request->gross_weight;
        $action_type = $request->action_type;
        $number_of_sub_packing = $request->number_of_sub_packing ?? 1;
        $user_id = $request->user_id;
        $degree_id = $request->degree_id;
        $pin1 = $request->pin1;
        $is_complete_information = $request->is_complete_information;
        $new_item_added = $request->new_item_added;
        $keep_unit_value = $request->keep_unit_value;
        $packing_form_new_added = null;
        $smart_object = SmartObject::find($request->smart_object_id);
        $default_lot_number = LotNumber::find($request->default_lot_number_id ?? 0);
        $source_production_form_item_id = $request->source_production_form_item_id ?? null;
        $before_amount_is_gross_weight = null; // مقدار اصلی بسته بندی وزن ناخالص است؟
        $barcode_algorithm_id = $this->getStatus($machine_allocation, "barcode_algorithm_id");
        $carrier_code = $request->carrier_code ?? "";
        $get_carrier_code = $request->get_carrier_code ?? 0;

        $machine_packing_count = MachineAllocationPackingForm::
        whereIn("machine_allocation_id", $machine_allocation->allocation->items()->pluck("id")->toArray())->
        where("status_id", 7007006)-> // بسته های معلق
        when($machine_allocation->machine, function ($query) use ($machine_allocation) {
            return $query->where("machine_id", $machine_allocation->machine_id);
        })->
        when($machine_allocation->contractor, function ($query) use ($machine_allocation) {
            return $query->where("contractor_id", $machine_allocation->contractor_id);
        })->
        when($machine_allocation->order_id, function ($query) use ($machine_allocation) {
            return $query->where("order_id", $machine_allocation->order_id);
        })->
        count();

        $agent = new Agent();
        $is_mobile = $agent->isMobile();


        $degree_option = Option::get("degree", $request->degree_id, $machine_allocation->product->goods_kind_id);;

        $controller = new RegisterProductionController();
        $allocation_terminated_id = $controller->getStatus($machine_allocation, "next_allocation_terminated");

        $packing_type_layer_count = [];
        foreach ($machine_allocation->production->packing_types as $item) {
            $packing_type_layer_count[$item->packing_type->id] = $item->packing_type->layers()->count();
        }

        $worker = Worker::find($user_id);
        if (!$worker->default_printer_id) {
            return "<div class='alert alert-danger' >لطفا با کلیک بر روی  <a href='" . route("utility.printer.select_default_printer") . "'>این لینک</a> پرینتر پیش فرض را انتخاب نمایید..</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $machine_allocation) . "'> بارگشت</a>";

        }
        /*************************************/
        // گرفتن پالت جاری
        $current_pallet = Pallet::CurrentPallet($machine_allocation->allocation);;

        ////// ایجاد پالت , چاپ لیبل پالت و بستن پالت
        $result_pallet = $this->pallet_info($action_type, $current_pallet, $machine_allocation, $worker);

        if ($result_pallet["result"]) {

            $pallet = $result_pallet["pallet"];
            $packing_type_option = $result_pallet["packing_type_option"];
            $lot_number = $result_pallet["lot_number"];
            $final_amount = $result_pallet["final_amount"];
            $message_add_packing = $result_pallet["message_add_packing"];
            $success_message = $result_pallet["success_message"];
            $before_amount = null;
            if ($keep_unit_value) {
                $before_amount = $amount;
            }

            return view($this->view_path . "_add_packing_item", compact(
                "machine_allocation", "user_id", "packing_type_option", "number_of_sub_packing",
                "lot_number", "final_amount", "sub_amount", "lot_number_code", "get_carrier_code",
                "degree_option", "packing_type_layer_count", "barcode_algorithm_id", "allocation_terminated_id",
                "packing_form_new_added", "smart_object", "default_lot_number", "source_production_form_item_id",
                "message_add_packing", "keep_unit_value", "is_mobile", "machine_packing_count", "pallet", "before_amount", "success_message"));
        }

        $pallet = $current_pallet;


        /************************************/


        $product = $machine_allocation->product;
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return "<div class='alert alert-danger' >نوع بسته بندی نامعتبر است..</div>";
        }
        $degree = Degree::find($degree_id);
        if (!$degree) {
            return "<div class='alert alert-danger' >نوع درجه بندی نامعتبر است..</div>";
        }

        $packing_type_option = Option::get("production_packing_type", $packing_type->id, 0, $machine_allocation->production->packing_types);


        $last_layer = $packing_type->layers()->orderByDesc("layer_code")->first();
        if (!$last_layer) {
            return "<div class='alert alert-danger' >لایه های بسته بندی تعریف نشده است</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $machine_allocation) . "'> بارگشت</a>";
        }


//        if ( $product->sub_unit && $product->sub_unit->weight_conversion_rate == 0 ) {
//            return "<div class='alert alert-danger' >واحد اصلی و واحد فرعی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید..</div>";
//        }


        $packing_type_weight_result = PackingType::getWeight($packing_type, null, $carrier_code);
        if (!$packing_type_weight_result["result"]) {
            return "<div class='alert alert-danger' >" . $packing_type_weight_result["error"] . "</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $machine_allocation) . "'> بارگشت</a>";;

        }
        $packing_type_weight = $packing_type_weight_result["weight"];

        // اضافه کردن وزن بسته بندی های فرعی
        if ($packing_type_layer_count[$packing_type->id] > 1) {
            $packing_type_weight_result = PackingType::getWeight($packing_type->first_packing_type);
            if (!$packing_type_weight_result["result"]) {
                return "<div class='alert alert-danger' >" . $packing_type_weight_result["error"] . "</div>"
                    . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $machine_allocation) . "'> بارگشت</a>";


            }

            $packing_type_weight += $packing_type_weight_result["weight"] * $number_of_sub_packing;
        }

        if ($product->unit->weight_conversion_rate == 0) {
            //             واحد اصلی وزنی نیست
            //             مقدار داریم و وزن ناخالص نداریم
            $gross_weight = $gross_weight;

        } else {
            // واحد اصلی وزنی است
            $gross_weight = $amount;
            $before_amount_is_gross_weight = $gross_weight;
        }

        $get_amount_from_weight_result = Product::getAmountFromWeight($product, $gross_weight, $packing_type_weight, $amount, $sub_amount, !$is_complete_information);

        if (!$get_amount_from_weight_result["result"]) {
            return "<div class='alert alert-danger' >" . $get_amount_from_weight_result["error"] . "</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $machine_allocation) . "'> بارگشت</a>";

        }
        $gross_weight = $get_amount_from_weight_result["gross_weight"];
        $final_amount = $get_amount_from_weight_result["final_amount"];
        $sub_amount = $get_amount_from_weight_result["sub_amount"];
        $weight = $get_amount_from_weight_result["weight"];


        $lot_number = LotNumber::where([
            "product_id" => $machine_allocation->product_id,
            "code" => $request->lot_number_code
        ])->first();

        if (!$lot_number || ($lot_number && !$lot_number->isSetAllProperty() && $is_complete_information == -1)) {

            $default_property_value[1] = ($final_amount > 0 && $weight > 0) ? round($weight / $final_amount, 3) : 0;// مقدار پیش فرض مشخصه کیلوگرم بر متر کالا
            return view($this->view_path . "_add_new_lot_number", compact(
                "machine_allocation", "user_id", "barcode_algorithm_id", "allocation_terminated_id", "packing_type_option", "number_of_sub_packing", "is_complete_information", "degree_id"
                , "lot_number", "final_amount", "sub_amount", "lot_number_code", "degree_option", "get_carrier_code", "packing_type_layer_count", "packing_form_new_added",
                "action_type", "smart_object", "default_lot_number", "source_production_form_item_id", "default_property_value", "pallet"));

        }

        // اگر الکوریتم بارکد دارد چک شود که الگوریتم درست وارد شده و پین تکراری وارد نکند
        if ($barcode_algorithm_id) {
            $pin1 = trim($pin1);
            if (PackingForm::where("pin1", $pin1)->exists()) {

                $message_add_packing = " با توجه به اینکه بارکد ($pin1) قبلا برای بسته بسته بندی دیگر ثبت شده و امکان استفاده از آن وجود ندارد.";

                return view($this->view_path . "_add_packing_item", compact(
                    "machine_allocation", "user_id", "packing_type_option", "number_of_sub_packing",
                    "lot_number", "final_amount", "sub_amount", "lot_number_code", "allocation_terminated_id",
                    "barcode_algorithm_id", "degree_option", "packing_type_layer_count", "get_carrier_code",
                    "packing_form_new_added", "smart_object", "default_lot_number", "source_production_form_item_id",
                    "message_add_packing", "keep_unit_value", "is_mobile", "machine_packing_count", "pallet"));
            }
        }


        // آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار/ماشین چک شود، (در صورت T بودن، باید همه بسته بندی ها تحویل به انبار شده باشند.)
        $result_checking_form_not_delivered = self::CheckingFormNotDelivered($machine_allocation);
        if (!$result_checking_form_not_delivered["result"]) {
            $message_add_packing = $result_checking_form_not_delivered["error"];
            return view($this->view_path . "_add_packing_item", compact(
                "machine_allocation", "user_id", "packing_type_option", "number_of_sub_packing",
                "lot_number", "final_amount", "sub_amount", "lot_number_code", "allocation_terminated_id",
                "barcode_algorithm_id", "degree_option", "packing_type_layer_count", "get_carrier_code",
                "packing_form_new_added", "smart_object", "default_lot_number", "source_production_form_item_id",
                "message_add_packing", "keep_unit_value", "is_mobile", "machine_packing_count", "pallet"));
        }


        Auth::loginUsingId($user_id);


        //گرفتن فرم تولید در حال تولید
        $production_form_select = ProductionForm::join("production_form_item", "production_forms.id", "production_form_id")->
        where([
                "allocation_id" => $machine_allocation->allocation_id,
                "production_forms.status_id" => $this->getStatus($machine_allocation, "production_form_in_production"),
            ]
        )->
//        when($production_form_item_id > 0, function ($query) use ($production_form_item_id) {
//            return $query->where("production_form_item.id", $production_form_item_id);
//        })->
        select("production_forms.id")->
        first();

        if ($production_form_select) {
            $production_form = ProductionForm::find($production_form_select->id);
        } else {
//            if ($production_form_item_id) {
//                return "<div class='alert alert-danger' >ردیف فرم تولید به درستی مشخص نشده است، لطفا با پشتیبانی تماس بگیرید.</div>";
//
//            }

            // تعریف فرم تولید جدید
            $production_form = ProductionForm::AddNewForm(
                $machine_allocation->machine->id ?? null,
                null,
                0,
                $packing_type->id,
                $machine_allocation->contractor->id ?? null,
                $controller->getStatus($machine_allocation, "production_form_in_production"));


            $production_form = ProductionForm::find($production_form->id);
        }


        // اگر فرم در حال تکمیل، بیش از یک آیتم داشت، اخرین آیتم را بر می داریم.
        $production_form_item = $production_form->items()->where("production_id", $machine_allocation->production_id)->orderBy("id", "desc")->first();

        if (!$production_form_item) {
            $production_form_item = ProductionFormItem::AddNewItem(
                $machine_allocation->allocation_id,
                $production_form->id,
                $machine_allocation->production_id,
                $machine_allocation->product_id,
                1,
                $this->getStatus($machine_allocation, "production_form_in_production"),// در حال تولید
                0,
                $machine_allocation->version_code ?? null
            );
        }

        // اگر سورس آیتم مشخص نشده بود، و سورس آیتم وجود داشت، آن را ذخیره می کنیم.
        if (!$production_form_item->source_production_form_item_id && $source_production_form_item_id) {
            $production_form_item->source_production_form_item_id = $source_production_form_item_id;
            $production_form_item->save();
        }


        //تعریف لات برای فرم تولید در صورتی که قبلا تعریف نشده
        $production_form_item_lot_number = ProductionFormItemLotNumber::where([
            "production_form_id" => $production_form->id,
            "production_form_item_id" => $production_form_item->id,
            "lot_number_id" => $lot_number->id
        ])->first();

        if (!$production_form_item_lot_number) {
            $production_form_item_lot_number = ProductionFormItemLotNumber::create([
                "production_form_id" => $production_form->id,
                "production_form_item_id" => $production_form_item->id,
                "lot_number_id" => $lot_number->id
            ]);
        }


        $packing_form = PackingForm::find($request->packing_form_id);

        if (!$packing_form) {

            // نوع بسته بندی فاقد حامل نمی باشد.
            if ($request->carrier_code) {
                $result = Carrier::firstOrCreate($request->carrier_code, $last_layer->carrier_type_id, 5320001, null);
                if (!$result["result"]) {
                    return "<div class='alert alert-danger' >" . $result["message"] . "</div>";
                }
                $carrier = $result["carrier"];
            }

            $packing_form = PackingForm::create([
                "carrier_id" => $carrier->id ?? null,
                "status_id" => 7007011, // "معلق - api"
                "packing_type_id" => $request->packing_type_id,
                "weight" => $is_complete_information == -1 ? $weight : null,
                "gross_weight" => $is_complete_information == -1 ? $gross_weight : null,
                "applicant_type_id"=>$machine_allocation->allocation->applicant_type_id ?? null,
                "applicant_id"=>$machine_allocation->allocation->applicant_id ?? null,
            ]);


        }


        if ($packing_form->status_id != 7007011) {

            return "<div class='alert alert-danger' >وضعیت بسته جهت عملیات نامعتبر است.</div>";
        }

        $production_form_item_lot_number->amount = $production_form_item_lot_number->amount + $final_amount;
        $production_form_item_lot_number->save();

        $packing_form_item = PackingFormItem::create([
            "packing_form_id" => $packing_form->id,
            "final_amount" => $final_amount,
            "amount" => $final_amount,
            "amount_after_control" => $final_amount,
            "sub_amount" => $sub_amount ?? 0,
            "init_sub_amount" => $sub_amount ?? 0,
            "status_id" => $packing_form->status_id,
            "product_id" => $machine_allocation->product_id,
            "degree_id" => $degree->id,
            "lot_number_id" => $lot_number->id,
            "production_form_item_id" => $production_form_item->id,
            "band_code" => 1,
        ]);

        $add_new_lot = null;

        $packing_form_id_for_print_id = $packing_form->id;

        // اگر مالک کارت تولید الگوریتم پین دارد، کد پین وارد شده را ارسال می کنیم وگر نه که هیچ
        $packing_form->getRandom($barcode_algorithm_id != 0 ? $pin1 : null);

        if ($new_item_added == -1) {
            /** اگر تیک نگه داری بسته بندی دارد، بسته را نگه دارد، در غیر این صورت از این مرحله رد شود. **/
            $before_amount = null;
            if ($keep_unit_value) {
                $before_amount = is_null($before_amount_is_gross_weight) ? (
                    $packing_form_item->final_amount ?? null
                ) :
                    $before_amount_is_gross_weight;
            }
            return view($this->view_path . "_add_packing_item", compact(
                "machine_allocation", "packing_form", "user_id", "packing_type_option", "number_of_sub_packing", "is_complete_information"
                , "add_new_lot", "final_amount", "sub_amount", "lot_number_code", "allocation_terminated_id",
                "degree_option", "default_lot_number", "barcode_algorithm_id", "get_carrier_code",
                "packing_type_layer_count", "packing_form_new_added", "action_type", "smart_object", "source_production_form_item_id", "keep_unit_value",
                "is_mobile", "machine_packing_count", "before_amount", "pallet"));

        }


        $message_add_packing = "";
        $final_amount_packing_form = $packing_form->getFinalAmount();

        //             بررسی مشخصات لات
        if ($is_complete_information == -1 && $final_amount_packing_form > 0 && isset($product->sub_unit)) {

            // اگر واحد اصلی متر است.
            if (
                $product->unit->weight_conversion_rate == 0 &&
                $product->sub_unit->weight_conversion_rate != 0
            ) {
                $result_check_lot_number = LotNumber::checkLotProperty($lot_number, $machine_allocation->product, $final_amount_packing_form, $weight);
            } elseif ($product->unit->weight_conversion_rate != 0) {
                $final_sub_amount_packing_form = $packing_form->getSubAmount();
                $result_check_lot_number = LotNumber::checkLotProperty($lot_number, $machine_allocation->product, $final_sub_amount_packing_form, $weight);

            } else {
                $result_check_lot_number["result"] = false;
                $result_check_lot_number["error"] = "واحد اصلی یا واحد فرعی به درستی تعریف نشده است، لطفا با واحد پشتیبانی تماس بگیرید.";
            }

            if (!$result_check_lot_number["result"]) {
                $message_add_packing = $result_check_lot_number["error"];
            }
        }


        //
        //// بررسی اینکه مقدار بسته بندی با حداکثر ظرفیت نوع حامل بسته بندی مطابقت دارد یا خیر
        //

        $carrier_type = $packing_type_weight_result["carrier_type"];


        if (isset($carrier_type) && $product->unit_id == $carrier_type->unit_id && $final_amount_packing_form > 0) {
            $result_check_carrier_amount = CarrierType::checkAmount($carrier_type, $final_amount_packing_form, $number_of_sub_packing);
            if (!$result_check_carrier_amount["result"]) {
                $message_add_packing = $result_check_carrier_amount["error"];
            }
        }

        // بررسی حداکثر مقدار مجاز قابل تولید
        $current_amount = $final_amount_packing_form + MachineAllocationPackingForm::
            join("packing_form_item", "packing_form_item.packing_form_id", "machine_allocation_packing_form.packing_form_id")->
            where([
                "machine_allocation_id" => $machine_allocation->id,
                "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
            ])->
            sum("final_amount");
        $result_check_max = self::CheckProductionTerminate($machine_allocation, "check_max", $current_amount);
        if (!$result_check_max["result"]) {
            $message_add_packing = $result_check_max["error"];
        }


        // اگر خطایی دارد اجازه ثبت نمی دهد.
        if ($message_add_packing != "") {
            MachineAllocationPackingForm::where([
                "machine_allocation_id" => $machine_allocation->id,
                "packing_form_id" => $packing_form->id,
            ])->delete();

            $packing_form->items()->delete();
            $packing_form->delete();
            $packing_form = null;

            return view($this->view_path . "_add_packing_item", compact(
                "machine_allocation", "packing_form", "user_id", "packing_type_option", "number_of_sub_packing", "is_complete_information", "message_add_packing"
                , "add_new_lot", "final_amount", "sub_amount", "lot_number_code", "degree_option", "default_lot_number",
                "packing_type_layer_count", "packing_form_new_added", "smart_object", "allocation_terminated_id",
                "source_production_form_item_id", "pallet", "barcode_algorithm_id", "get_carrier_code",
                "keep_unit_value", "is_mobile", "machine_packing_count"));

        }
        MachineAllocationPackingForm::create([
            "machine_id" => $machine_allocation->machine_id,
            "contractor_id" => $machine_allocation->contractor_id,
            "machine_allocation_id" => $machine_allocation->id,
            "packing_form_id" => $packing_form->id,
            "need_to_complete_information" => $is_complete_information == -1 ? 0 : 1
        ]);


        // بسته بندی دو سطحی است
        if ($packing_type_layer_count[$packing_type->id] > 1) {

            // به ازای هر بسته بندی، به تعداد بسته بندی فرعی ایجاد می کنیم.
            PackingForm::CreateSubPacking($packing_form, $number_of_sub_packing, $packing_form->status_id);

            foreach ($packing_form->packing_form_contents as $content_packing_form) {

                $content_packing_form->getCode();
                event(new PackingLogEvent($content_packing_form, 7007001, null, "", null, $user_id));

                // به ازای هر آیتم بسته بندی یک ردیف ایجاد می کنیم
                $packing_form_item = PackingFormItem::create([
                    "packing_form_id" => $content_packing_form->id,
                    "production_form_item_id" => null,
                    "production_form_item_lot_number_id" => null,
                    "fabric_raw_grading_id" => null,
                    "product_id" => $machine_allocation->product_id,
                    "lot_number_id" => $lot_number->id,
                    "degree_id" => $request->degree_id,
                    "amount" => $final_amount / $number_of_sub_packing,
                    "amount_after_control" => $final_amount / $number_of_sub_packing,
                    "final_amount" => $final_amount / $number_of_sub_packing,
                    "sub_amount" => $sub_amount / $number_of_sub_packing,
                    "init_sub_amount" => $sub_amount / $number_of_sub_packing,
                    "band_code" => 1,
                    "status_id" => $packing_form->status_id
                ]);
                $packing_form_item->getCode(1, 1);

                // تغیر وضعیت انبار
                $content_packing_form->warehouse_status_id = 4205; // بسته داخل بسته بزرگتر است
                $content_packing_form->save();


            }


        } else {
            // بسته بندی یک سطحی است.
            if ($is_complete_information == -1) {

                // واحد اصلی وزنی نیست، واحد فرعی وزنی است.
                if (
                    $product->unit->weight_conversion_rate == 0 &&
                    isset($product->sub_unit) &&
                    $product->sub_unit->weight_conversion_rate != 0
                ) {

                    $sum_final_amount = $packing_form->getFinalAmount(-1);

                    //$sum_sub_amount = $packing_form->getSubAmount(-1);
                    //چون واحد فرعی وزنی است، پس اگر فرم بسته بندی یک/چند آیتم داشته باشد هر بار وزن کل ارسال می شود که باید یکی ازآنها را بگیریم.
                    $sum_sub_amount = $sub_amount;

                    foreach ($packing_form->items as $packing_form_item) {
                        $packing_form_item->sub_amount = $packing_form_item->final_amount * $sum_sub_amount / $sum_final_amount;
                        $packing_form_item->save();
                    }
                } elseif (
                    $product->unit->weight_conversion_rate != 0 &&
                    isset($product->sub_unit)
                ) {
                    // واحد اصلی وزنی است و واحد فرعی غیر وزنی
                    $sum_final_amount = $final_amount;

                    $sum_sub_amount = $packing_form->getSubAmount(-1);
                    foreach ($packing_form->items as $packing_form_item) {
                        $packing_form_item->amount = $packing_form_item->sub_amount * $sum_final_amount / $sum_sub_amount;
                        $packing_form_item->final_amount = $packing_form_item->amount;
                        $packing_form_item->amount_after_control = $packing_form_item->amount;
                        $packing_form_item->save();
                    }
                } else {
                    // کالا فقط واحد اصلی دارد و نیاز نیست چیزی را حساب کنیم
                    $result_update_weight = PackingForm::UpdateWeight($packing_form);
                    if ($result_update_weight["result"]) {
                        $gross_weight = $result_update_weight["gross_weight"];
                        $weight = $result_update_weight["weight"];
                    } else {
                        $gross_weight = null;
                        $weight = null;
                    }
                }


                // ثبت وزن ناخالص و خالص
                $packing_form->gross_weight = $gross_weight;
                $packing_form->weight = $weight;
                $packing_form->save();
            }
        }


        if ($action_type == "register_print_continue" || $action_type == "register_print_back") {
            $packing_form_print = PackingForm::find($packing_form_id_for_print_id);

            PrintQRController::direct_print($packing_form_print, $worker);

        }

        $packing_form_new_added = PackingForm::find($packing_form->id);
        $packing_form = null;


        /*
         * اضافه کردن به پالت
         */
        Pallet::AddPackingForm($pallet, $packing_form_new_added);


        /** اگر تیک نگه داری بسته بندی دارد، بسته را نگه دارد، در غیر این صورت از این مرحله رد شود. **/
        $before_amount = null;
        if ($keep_unit_value) {
            $before_amount = is_null($before_amount_is_gross_weight) ? (
                $packing_form_item->final_amount ?? null
            ) :
                $before_amount_is_gross_weight;
        }


        // بررسی اینکه اگر کارت از مقدار تخصیص بیشتر تولید کرده است و کارت بعدی دارد، پیام بدهد و برور روی تخصیص بعدی
        $next_machine_allocation = null;
        if ($result_check_max["production_amount"] >= $machine_allocation->allocation_amount) {
            $reserve_status_id = $controller->getStatus($machine_allocation, "next_allocation_reserve");

            $next_machine_allocation = MachineAllocation::
            where(["allocation_id" => $machine_allocation->allocation_id,
                "status_id" => $reserve_status_id])->
            where("id", "!=", $machine_allocation->id)->
            first();

            if ($next_machine_allocation) {
                $result_next = self::GoToNextMachineAllocation($machine_allocation);
                if (!$result_next["result"]) {
                    return back()->withErrors($result_next["error"]);
                }
                $machine_allocation = $result_next["next_machine_allocation"];
                $packing_type_option = Option::get("production_packing_type", 0, 0, $machine_allocation->production->packing_types);
            }

        }

        return view($this->view_path . "_add_packing_item", compact(
            "machine_allocation", "packing_form", "user_id", "packing_type_option", "number_of_sub_packing", "is_complete_information"
            , "add_new_lot", "final_amount", "sub_amount", "lot_number_code", "get_carrier_code",
            "degree_option", "default_lot_number", "barcode_algorithm_id", "allocation_terminated_id",
            "packing_type_layer_count", "packing_form_new_added", "action_type", "smart_object", "source_production_form_item_id", "pallet", "keep_unit_value", "before_amount", "is_mobile", "machine_packing_count"));


    }

// اضافه کردن بسته بندی از طریف API سامانه جامع
// وقتی مشتری ثبت تولید می زند، و اطلاعات بسته بندی را ارسال می کند، این طرف هم فقط چک می کنیم اگر اوکی بود، ثبت می کنیم.
    public
    static function AddNewPackingFormFromAPI($packing_form_data, $packing_form_item_data, $production_code, $user_id)
    {

        $production = Production::where("serial", $production_code)->first();
        if (!$production) {
            return [
                "result" => false,
                "error" => "کارت تولید با کد $production_code در سامانه یافت نشد. "
            ];
        }
        $machine_allocation = MachineAllocation::
        where("production_id", $production->id)->
        where("status_id", 5310104)-> // در حال تولید توسط پیمانکار
        first();
        if (!$machine_allocation) {
            return [
                "result" => false,
                "error" => "تخصیص متناظر برای کارت تولید با کد $production_code با وضعیت در حال تولید توسط پیمانکار در سامانه یافت نشد. "
            ];
        }
        if (!$machine_allocation->contractor) {
            return [
                "result" => false,
                "error" => "پیمانکار متناظر برای کارت تولید با کد $production_code با وضعیت در حال تولید توسط پیمانکار در سامانه یافت نشد. "
            ];
        }

        $machine_allocation_count = MachineAllocation::
        where("production_id", $production->id)->
        where("status_id", 5310104)-> // در حال تولید توسط پیمانکار
        count();
        if ($machine_allocation_count != 1) {
            return [
                "result" => false,
                "error" => "تخصیص متناظر برای کارت تولید با کد $production_code با وضعیت در حال تولید توسط پیمانکار بیش از یک مورد است و امکان تشخیص آن وجود ندارد. "
            ];
        }

        $controller = new RegisterProductionController();

        $packing_type = PackingType::find($packing_form_data["packing_type_id"]);

        // آیا نوع بسته بندی مجاز است
        $production_packing_type_exists = ProductionPackingType::
        where(["production_id" => $production->id, "packing_type_id" => $packing_form_data["packing_type_id"]])->
        exists();
        if (!$production_packing_type_exists) {
            return [
                "result" => false,
                "error" => "نوع بسته بندی ارسال شده با کد " . ($packing_type->id ?? 0) . " جزء بسته بندی های مجاز برای کارت " . $production->code . " نمی باشد."
            ];
        }


//        $production_form_item_id = 0;

        // ایجاد لات کالا: فرض بر این است که آیتم های داخل بسته بندی از یک نوع هستند.
        $lot_number = LotNumber::where([
            "product_id" => $machine_allocation->product_id,
            "code" => $packing_form_item_data[0]["lot_number_code"]
        ])->first();

        if (!$lot_number) {

            $lot_number = LotNumber::create([
                "product_id" => $machine_allocation->product_id,
                "code" => $packing_form_item_data[0]["lot_number_code"],
                "user_id" => $user_id
            ]);
        }


        // آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار/ماشین چک شود، (در صورت T بودن، باید همه بسته بندی ها تحویل به انبار شده باشند.)

        $checking_form_not_delivered_at_register_production = $controller->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production");
        if ($checking_form_not_delivered_at_register_production) {

            $checking_form_status_ids = $controller->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production_status_ids");
            $packing_form_list = PackingForm::join("machine_allocation_packing_form", "packing_form_id", "packing_forms.id")->
            where("machine_allocation_id", $machine_allocation->id)->
            whereIn("packing_forms.status_id", $checking_form_status_ids)->
            select("packing_forms.*")->
            get();

            $count_packing_form = count($packing_form_list);

            if ($count_packing_form > 0) {

                $message_add_packing = "با توجه به اینکه بسته بندی های زیر توسط "
                    . ($machine_allocation->machine ? " انبار " : " پیمانکار ") .
                    "تایید نشده است امکان افزودن بسته بندی جدید وجود ندارد.";
                $k = 0;
                foreach ($packing_form_list as $item) {
                    $k++;
                    $message_add_packing .= "<br/> $k - " . $item->getCode() . "(" . $item->status->caption . ")";
                    if ($k > 4) {
                        break;
                    }
                }
                if ($count_packing_form > $k) {
                    $message_add_packing .= "<br/>" . " و " . ($count_packing_form - $k) . " بسته بندی دیگر";
                }


                return [
                    "result" => false,
                    "error" => $message_add_packing
                ];

            }
        }

        // کپی
        // از اینجا تا 200 خط کد پایین تر کپی شده از کدهای تابع دیگر می باشد، که باید با هم یکی شوند.

        //گرفتن فرم تولید در حال تولید
        $production_form_select = ProductionForm::join("production_form_item", "production_forms.id", "production_form_id")->
        where([
                "allocation_id" => $machine_allocation->allocation_id,
                "production_forms.status_id" => $controller->getStatus($machine_allocation, "production_form_in_production"),
            ]
        )->
//        when($production_form_item_id > 0, function ($query) use ($production_form_item_id) {
//            return $query->where("production_form_item.id", $production_form_item_id);
//        })->
        select("production_forms.id")->
        first();

        if ($production_form_select) {
            $production_form = ProductionForm::find($production_form_select->id);
        } else {
//            if ($production_form_item_id) {
//                return [
//                    "result" => false,
//                    "error" => "ردیف فرم تولید به درستی مشخص نشده است، لطفا با پشتیبانی تماس بگیرید"
//                ];
//
//            }

            // تعریف فرم تولید جدید
            $production_form = ProductionForm::AddNewForm(
                $machine_allocation->machine->id ?? null,
                null,
                0,
                $packing_type->id,
                $machine_allocation->contractor->id ?? null,
                $controller->getStatus($machine_allocation, "production_form_in_production"));

            ProductionFormItem::AddNewItem(
                $machine_allocation->allocation_id,
                $production_form->id,
                $machine_allocation->production_id,
                $machine_allocation->product_id,
                1,
                $controller->getStatus($machine_allocation, "production_form_in_production"),// در حال تولید
                0,
                $machine_allocation->version_code ?? null
            );

            $production_form = ProductionForm::find($production_form->id);
        }


        if (count($production_form->items) == 0) {
            return [
                "result" => false,
                "error" => "ردیف فرم تولید یافت نشده، لطفا با پشیتبانی تماس بگیرید" . $production_form->id
            ];
        }

        $production_form_item = $production_form->items()->first();


        //تعریف لات برای فرم تولید در صورتی که قبلا تعریف نشده
        $production_form_item_lot_number = ProductionFormItemLotNumber::where([
            "production_form_id" => $production_form->id,
            "production_form_item_id" => $production_form_item->id,
            "lot_number_id" => $lot_number->id
        ])->first();

        if (!$production_form_item_lot_number) {
            $production_form_item_lot_number = ProductionFormItemLotNumber::create([
                "production_form_id" => $production_form->id,
                "production_form_item_id" => $production_form_item->id,
                "lot_number_id" => $lot_number->id
            ]);
        }


        $packing_form_data["status_id"] = $controller->getStatus($machine_allocation, "packing_form_sending");
        $packing_form = PackingForm::create($packing_form_data);

        $final_amount = 0;

        foreach ($packing_form_item_data as $packing_form_item_array) {
            $packing_form_item_array["packing_form_id"] = $packing_form->id;
            $packing_form_item_array["status_id"] = $packing_form->status_id;
            $packing_form_item_array["product_id"] = $machine_allocation->product_id;
            $packing_form_item_array["lot_number_id"] = $lot_number->id;
            PackingFormItem::create($packing_form_item_array);

            $final_amount += $packing_form_item_array["final_amount"];
        }

        $packing_form->getRandom();

        $production_form_item_lot_number->amount = $production_form_item_lot_number->amount + $final_amount;
        $production_form_item_lot_number->save();


        $message_add_packing = "";
        $final_amount_packing_form = $packing_form->getFinalAmount();


        //
        //// بررسی اینکه مقدار بسته بندی با حداکثر ظرفیت نوع حامل بسته بندی مطابقت دارد یا خیر
        //

        // انواع حامل ها حذف شد.
//        $carrier_type = $packing_type_weight_result["carrier_type"];
//        if ($product->unit_id == $carrier_type->unit_id && $final_amount_packing_form > 0) {
//            $result_check_carrier_amount = CarrierType::checkAmount($carrier_type, $final_amount_packing_form, $number_of_sub_packing);
//            if (!$result_check_carrier_amount["result"]) {
//                $message_add_packing = $result_check_carrier_amount["error"];
//            }
//        }

        // بررسی حداکثر مقدار مجاز قابل تولید
        $current_amount = $final_amount_packing_form + MachineAllocationPackingForm::
            join("packing_form_item", "packing_form_item.packing_form_id", "machine_allocation_packing_form.packing_form_id")->
            where([
                "machine_allocation_id" => $machine_allocation->id,
                "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
            ])->
            sum("final_amount");
        $result_check_max = self::CheckProductionTerminate($machine_allocation, "check_max", $current_amount);
        if (!$result_check_max["result"]) {
            $message_add_packing = $result_check_max["error"];
        }

        // اگر خطایی دارد اجازه ثبت نمی دهد.
        if ($message_add_packing != "") {
            MachineAllocationPackingForm::where([
                "machine_allocation_id" => $machine_allocation->id,
                "packing_form_id" => $packing_form->id,
            ])->delete();

            $packing_form->items()->delete();
            $packing_form->delete();
            $packing_form = null;
            return [
                "result" => false,
                "error" => $message_add_packing
            ];
        }
        $need_to_complete_information = 0;// اطلاعات بسته بندی کامل است یا نیاز به وزن کردن دارد؟

        MachineAllocationPackingForm::create([
            "machine_id" => $machine_allocation->machine_id,
            "contractor_id" => $machine_allocation->contractor_id,
            "machine_allocation_id" => $machine_allocation->id,
            "packing_form_id" => $packing_form->id,
            "need_to_complete_information" => $need_to_complete_information
        ]);

        return [
            "result" => true,
            "new_packing_form_code" => $packing_form->getCode()
        ];
    }

// اضافه کردن مشخصلات لات
    public
    function add_new_lot_number_to_product_api(Request $request)
    {

        $machine_allocation = MachineAllocation::find($request->machine_allocation_id);
        $error = null;
        $is_complete_information = $request->is_complete_information;
        $source_production_form_item_id = $request->source_production_form_item_id;

        $lot_number_code = $request->lot_number_code;
        $smart_object = SmartObject::find($request->smart_object_id);

        $default_lot_number = LotNumber::find($request->default_lot_number_id ?? 0);
        $lot_number = LotNumber::where([
            "product_id" => $machine_allocation->product_id,
            "code" => $request->lot_number_code
        ])->first();

        if (!$lot_number) {
            $lot_number = LotNumber::create([
                "product_id" => $machine_allocation->product_id,
                "code" => $request->lot_number_code,
                "user_id" => Auth::id() ?? 0,
            ]);
        }

        if (!$lot_number->isSetAllProperty() && $is_complete_information == -1) {

            if (!LotNumberProperty::check_property_check_for_getting($lot_number, 1)) {
                $product = $machine_allocation->product;
                $allowed_percentage = $product->goods_kind->allowed_percentage_in_all_lot_number / 100;
                $kgInMeterLot = $request->lot_number_property_1;
                if ($product->weight > $kgInMeterLot * (1 + $allowed_percentage) || $product->weight < $kgInMeterLot * (1 - $allowed_percentage)) {
                    $error = "با توجه به  وزن کالای " . $product->caption
                        . " (" . $product->weight . " kg" . " )  امکان ثبت مقدار  " . $kgInMeterLot .
                        " برای مشخصه کیلوگرم بر متر کالا امکان پذیر نمی باشد. <br/>
                        لطفا اطلاعات وارد شده را بررسی کرده و در صورت نیاز با واحد پشتیبانی تماس بگیرید. ";
                } else {
                    GoodsKindLotNumberPropertyValue::create([
                        "product_id" => $machine_allocation->product_id,
                        "lot_number_id" => $lot_number->id,
                        "goods_kind_lot_number_property_id" => 1,
                        "value" => $request->lot_number_property_1
                    ]);
                }

            }


        }

        $barcode_algorithm_id = $this->getStatus($machine_allocation, "barcode_algorithm_id");
        /*********************/
        $user_id = $request->user_id;
        $degree_id = $request->degree_id ?? 0;
        $add_new_lot = 0;
        $final_amount = 0;
        $sub_amount = 0;


        $result = RegisterProductionController::getProductionResult("new_packing", $machine_allocation, $packing_form->id ?? null, $degree_id);

        if (!$result["result"]) {
            echo $result["error"];
            return;
        }

        $degree_option = $result["degree_option"];
        $packing_type_option = $result["packing_type_option"];
        $packing_type_layer_count = $result["packing_type_layer_count"];
        $get_carrier_code = $result["get_carrier_code"];
        $message_add_packing = $error;

        $agent = new Agent();
        $is_mobile = $agent->isMobile();
        $pallet = Pallet::CurrentPallet($machine_allocation->allocation);

        $controller = new self();
        $allocation_terminated_id = $controller->getStatus($machine_allocation, "next_allocation_terminated");

        return view($this->view_path . "_add_packing_item", compact(
            "machine_allocation", "user_id", "packing_type_option", "is_complete_information"
            , "add_new_lot", "final_amount", "sub_amount", "lot_number_code", 'allocation_terminated_id',
            "degree_option", "message_add_packing", "barcode_algorithm_id", "get_carrier_code",
            "packing_type_layer_count", "smart_object", "default_lot_number", "source_production_form_item_id", "is_mobile", "pallet"));


    }

    public
    function delete_packing_form(MachineAllocation $machine_allocation, PackingForm $packing_form)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $packing_form_machine_allocation = MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "packing_form_id" => $packing_form->id
        ])->first();

        if (!$packing_form_machine_allocation) {
            return back()->withErrors("بسته بندی مورد نظر برای تخصیص یافت نشد.");
        }

        if (!in_array($packing_form->status_id, [7007011, 7007006])) {
            return back()->withErrors("امکان حذف بسته بندی با توجه به وضعیت آن وجود ندارد.");
        }

        foreach ($packing_form->packing_form_contents as $sub_packing_form) {
            $sub_packing_form->items()->delete();
            $sub_packing_form->delete();
        }

        // حذف از پالت
        PalletItem::where("packing_form_id", $packing_form->id)->delete();
        $packing_form->items()->delete();
        $packing_form->delete();

        MachineAllocationPackingForm::where("packing_form_id", $packing_form->id)->delete();
        if ($machine_allocation->order_id) { // اگر از سفارش آمده آن را حذف می کنیم.
            OrderPackingForm::where([
                "packing_form_id" => $packing_form->id,
                "order_id" => $machine_allocation->order_id
            ])->update(["packing_form_id" => null, "status_id" => 6070001]);
        }


        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }

    public
    function delete_form_general_item(MachineAllocation $machine_allocation, FormGeneralItem $form_general_item)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }


        if (!in_array($form_general_item->status_id, [5002001])) {
            return back()->withErrors("امکان حذف آیتم با توجه به وضعیت آن وجود ندارد.");
        }

        $form_general_item->delete();

        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }

    public
    function print_packing_form(Request $request, MachineAllocation $machine_allocation, PackingForm $packing_form)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $request["back_url_route"] = $this->route_path . "index";
        $request["id"] = $machine_allocation->id;

        $prC = new PrintQRController();

        return $prC->submit($request, $packing_form, false);
    }

    public
    function download_packing_form(MachineAllocation $machine_allocation, PackingForm $packing_form)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $prC = new PrintQRController();

        return $prC->download($packing_form, false);
    }

    /*
     * $source_production_form_item_id : شناسه کارت مبدا
     */
    public
    function complete_information(MachineAllocation $machine_allocation, MachineAllocationPackingForm $machine_allocation_packing_form, $source_production_form_item_id)
    {

        //
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        $packing_form = $machine_allocation_packing_form->packing_form;

        if ($machine_allocation_packing_form->need_to_complete_information == 0) {
            return back()->withErrors("برای این بسته بندی نیاز به تکمیل اطلاعات ندارید.");
        }

        $packing_form_item = $packing_form->items()->first();
        $lot_number_property_1 = $packing_form_item->lot_number->getPropertyValue(1, "value");

        return view($this->view_path . "complete_information",
            compact("packing_form", "machine_allocation", "machine_allocation_packing_form", "lot_number_property_1", "packing_form_item", "source_production_form_item_id"));
    }

    public
    function submit_complete_information(Request $request, MachineAllocation $machine_allocation, MachineAllocationPackingForm $machine_allocation_packing_form, $source_production_form_item_id)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }


        if ($machine_allocation_packing_form->need_to_complete_information == 0) {
            return back()->withErrors("برای این بسته بندی نیاز به تکمیل اطلاعات ندارید.");
        }
        $packing_form = $machine_allocation_packing_form->packing_form;

        $packing_form_item = $packing_form->items()->first();
        $lot_number_property_1 = $packing_form_item->lot_number->getPropertyValue(1, "value");
        if (!$lot_number_property_1) {
            if ($request->lot_number_property_1 <= 0) {
                return back()->withErrors("لطفا کیلوگرم بر متر کالا را برای لات وارد نمایید.");
            }

            GoodsKindLotNumberPropertyValue::create([
                "product_id" => $machine_allocation->product_id,
                "lot_number_id" => $packing_form_item->lot_number->id,
                "goods_kind_lot_number_property_id" => 1,
                "value" => $request->lot_number_property_1
            ]);
        }

        $result = CompleteInformationController::CompleteInformation($packing_form, $request->gross_weight, 1);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        event(new PackingLogEvent($packing_form, "7007027"));
        $machine_allocation_packing_form->need_to_complete_information = 0;
        $machine_allocation_packing_form->save();

        return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => "اطلاعات با موفقیت ثبت گردید."]);

    }

    /*******************************************************************************************/
    /* افزودن بدون وارد کردن جزئیات بسته بندی ها */

    public
    function add_without_details(MachineAllocation $machine_allocation, $other_machine_allocation_id = 0)
    {
        if ($machine_allocation->order) {
            return redirect()->route($this->route_path . "add_from_order_packing_form", $machine_allocation);
        }
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        if ($machine_allocation->production->waiting_status_id == 5310106) {
            if ($machine_allocation->machine) {
                return redirect()->route("production.machine.index")->withErrors("کارت تولید خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            } else {
                return redirect()->route("contractor.panel.dashboard.index")->withErrors("دستور پیمان خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            }

        }

        if (MachineAllocation::allowGetPackingFormDetails($machine_allocation)) {
            return back()->withErrors("امکان ثبت تولید وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
        }

        if ($machine_allocation->production->waiting_status_id == 5310106) {
            if ($machine_allocation->machine) {
                return redirect()->route("production.machine.index")->withErrors("کارت تولید خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            } else {
                return redirect()->route("contractor.panel.dashboard.index")->withErrors("دستور پیمان خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");
            }

        }


        if ($machine_allocation->status_id == $this->getStatus($machine_allocation, "allocation_terminated")) {
            return back()->withErrors("با توجه به اینکه " . ($machine_allocation->machine ? "کارت تولید" : "دستور پیمان") . " خاتمه یافته است، امکان ثبت تولید وجود ندارد.");
        }


        $amount = session("amount");
        $sub_amount = session("sub_amount");
        $lot_number_code = session("lot_number_code");
        $degree_id = session("degree_id");
        $packing_type_id = session("packing_type_id");
        $packing_form_number = session("packing_form_number");
        $tax_price = session("tax_price");
        $price = session("price");


        $other_machine_allocation = MachineAllocation::find($other_machine_allocation_id);
        if (!$other_machine_allocation) {
            $other_machine_allocation = $machine_allocation;
        }
        $result = RegisterProductionController::getProductionResult("new_packing", $other_machine_allocation, null, $degree_id, $packing_type_id);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $degree_option = $result["degree_option"];
        $packing_type_option = $result["packing_type_option"];


        $machine_allocation_option = Option::get("machine_allocation_list", $other_machine_allocation->id, 0, $machine_allocation->allocation->items);

        return view($this->view_path . "general.add_without_details", compact(
            "degree_option", "machine_allocation", "other_machine_allocation", "packing_type_option", "amount",
            "sub_amount", "packing_form_number", "price", "tax_price", "lot_number_code", "machine_allocation_option"
        ));
    }

    public
    function submit_without_details(Request $request, MachineAllocation $machine_allocation, $other_machine_allocation_id = 0)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        $other_machine_allocation = MachineAllocation::find($other_machine_allocation_id);
        if (!$other_machine_allocation) {
            $other_machine_allocation = $machine_allocation;
        }

        if ($other_machine_allocation->production->waiting_status_id == 5310106) {
            return redirect()->route("contractor.panel.dashboard.index")->withErrors($machine_allocation->getTextOfThing("production_caption") . " خاتمه یافته شده است و امکان ثبت تولید وجود ندارد.");

        }


        if (MachineAllocation::allowGetPackingFormDetails($other_machine_allocation)) {
            return back()->withErrors("امکان ثبت تولید وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
        }


        if ($other_machine_allocation->status_id == $this->getStatus($other_machine_allocation, "allocation_terminated")) {
            return back()->withErrors("با توجه به اینکه " . $machine_allocation->getTextOfThing("production_caption") . " خاتمه یافته است، امکان ثبت تولید وجود ندارد.");
        }

        $result = RegisterProductionController::getProductionResult("new_packing", $other_machine_allocation, ($packing_form->id ?? null), $request->degree_id, $request->packing_type_id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $amount = $request->amount;
        $sub_amount = $request->sub_amount;
        $lot_number_code = $request->lot_number_code;
        $packing_form_number = $request->packing_form_number;
        $price = $request->price;
        $tax_price = $request->tax_price;
        $degree_id = $request->degree_id;
        $packing_type_id = $request->packing_type_id;

        $lot_number = LotNumber::where([
            "product_id" => $other_machine_allocation->product_id,
            "code" => $lot_number_code
        ])->
        first();

        if (!$lot_number && !isset($request->create_new_lot_number)) {
            session([
                "amount" => $amount,
                "sub_amount" => $sub_amount,
                "lot_number_code" => $lot_number_code,
                "degree_id" => $degree_id,
                "packing_type_id" => $packing_type_id,
                "packing_form_number" => $packing_form_number,
                "tax_price" => $tax_price,
                "price" => $price
            ]);

            return redirect()->route($this->route_path . "add_without_details", [$machine_allocation, $other_machine_allocation_id]);
        }

        $current_final_amount = FormGeneralItem::where([
            "machine_allocation_id" => $other_machine_allocation->id,
        ])->
        whereNotIn("status_id", [5002003, 5002002])->
        sum("amount");
        // چک کردن حداکثر مقدار قابل تولید
        $result = self::CheckProductionTerminate($other_machine_allocation, "check_max", $current_final_amount);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if (!$lot_number) {
            $lot_number = LotNumber::create([
                "product_id" => $other_machine_allocation->product_id,
                "code" => $lot_number_code,
                "user_id" => Auth::id()
            ]);
        }

        FormGeneralItem::create([
            "form_id" => null,
            "production_form_item_id" => null,
            "allocation_id" => $other_machine_allocation->allocation_id,
            "machine_allocation_id" => $other_machine_allocation->id,
            "product_id" => $other_machine_allocation->product_id,
            "degree_id" => $degree_id,
            "lot_number_id" => $lot_number->id,
            "packing_type_id" => $packing_type_id,
            "packing_form_number" => $packing_form_number,
            "amount" => $amount,
            "sub_amount" => $sub_amount,
            "price" => $price,
            "tax_price" => $tax_price,
            "total_price_with_tax" => $price + $tax_price,
            "status_id" => 5002001, //معلق
            "warehouse_storage_type_id" => 2
        ]);

        session()->forget("amount");
        session()->forget("sub_amount");
        session()->forget("lot_number_code");
        session()->forget("degree_id");
        session()->forget("packing_type_id");
        session()->forget("packing_form_number");
        session()->forget("tax_price");
        session()->forget("price");

        return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => "ثبت تولید با موفقیت انجام شد."]);

    }

// اضافه کردن بسته بندی، با توجه به بسته بندی های کالای تامین در بخش سفارش ها
    public
    function add_from_order_packing_form(MachineAllocation $machine_allocation)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        if (!$machine_allocation->order_id) {
            return back()->withErrors("اطلاعات سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $order = $machine_allocation->order;

        if ($order->customer->get_packing_form_details) {

            $order_packing_form_list = OrderPackingForm::where([
                "order_id" => $machine_allocation->order_id,
                "material_id" => $machine_allocation->product_id,
                "order_list_id" => $machine_allocation->production->parent_production->order_list_id ?? 0
            ])->get();

            return view($this->view_path . "general.add_from_order_packing_form", compact(
                "machine_allocation", "order_packing_form_list", "order"
            ));
        } else {

            $machine_allocation_option = Option::get("machine_allocation_list", $machine_allocation->id, 0, $machine_allocation->allocation->items);
            $other_machine_allocation = $machine_allocation;


            $order_consumed_product = OrderConsumedProduct::where([
                "order_id" => $order->id,
                "material_id" => $machine_allocation->product_id
            ])->first();

            if (!$order_consumed_product) {
                return back()->withErrors("مشخصات نوع تامین مواد اولیه برای مواد یافت نشد، لطفا با واحد پشتیبانی تماس بگیرید.");
            }
            $form_general_item = $order_consumed_product->form_general_item;

            // اضافه کردن نوع بسته بندی به کارت تامین.
            ProductionPackingType::firstOrCreate([
                "production_id" => $machine_allocation->production_id,
                "packing_type_id" => $form_general_item->packing_type_id,
            ]);

            $result = RegisterProductionController::getProductionResult("new_packing", $other_machine_allocation, null, $form_general_item->degree_id, $form_general_item->packing_type_id);

            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
            $degree_option = $result["degree_option"];
            $packing_type_option = $result["packing_type_option"];
            $lot_number_code = $form_general_item->lot_number->code ?? "";
            // اگر مشتری اطلاعات بسته بندی ها را به صورت کلی ثبت می کند،
            return view($this->view_path . "general.add_without_details", compact(
                "machine_allocation", "order", "machine_allocation_option", "other_machine_allocation", "degree_option", "packing_type_option", "lot_number_code"
            ));
        }
    }

    public
    function submit_add_from_order_packing_form(Request $request, MachineAllocation $machine_allocation)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        if (!$machine_allocation->order_id) {
            return back()->withErrors("اطلاعات سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $order_packing_form_list = OrderPackingForm::where([
            "order_id" => $machine_allocation->order_id,
            "material_id" => $machine_allocation->product_id
        ])->
        get();

        foreach ($order_packing_form_list as $order_packing_form) {
            if (!isset($request["order_packing_form"][$order_packing_form->id])) {
                continue;
            }
            if ($order_packing_form->packing_form) {
                return back()->withErrors("بسته بندی " . $order_packing_form->packing_form_code .
                    " قبلا با کد بسته بندی (" .
                    ($order_packing_form->packing_form->code ?? "") .
                    ") ارسال شده است و امکان ارسال مجدد آن وجود ندارد.");
            }

            $packing_form = PackingForm::create([
                "carrier_id" => null,
                "status_id" => 7007011, // "معلق - api"
                "packing_type_id" => $order_packing_form->packing_type_id,
                "weight" => $order_packing_form->weight,
                "gross_weight" => $order_packing_form->gross_weight,
                "sub_packing_form_number" => $order_packing_form->sub_packing_form_number
            ]);

            $lotNumber = LotNumber::firstOrCreate([
                "product_id" => $order_packing_form->product_id,
                "code" => $order_packing_form->lot_number_code,
            ], [
                "user_id" => Auth::id(),
            ]);


            PackingFormItem::create([
                "packing_form_id" => $packing_form->id,
                "final_amount" => $order_packing_form->amount,
                "amount" => $order_packing_form->amount,
                "amount_after_control" => $order_packing_form->amount,
                "sub_amount" => $order_packing_form->sub_amount ?? 0,
                "init_sub_amount" => $order_packing_form->sub_amount ?? 0,
                "status_id" => $packing_form->status_id,
                "product_id" => $machine_allocation->product_id,
                "degree_id" => $order_packing_form->degree_id,
                "lot_number_id" => $lotNumber->id,
                "band_code" => 1,
            ]);


            // وزن بسته بندی اصلی وقتی بسته های فرعی ایجاد شده اند را بروز می کنیم.
            $result_weight = PackingForm::UpdateWeight($order_packing_form);
            if ($result_weight["result"]) {
                $order_packing_form->weight = $result_weight["weight"];;
                $order_packing_form->gross_weight = $result_weight["gross_weight"];
            }

            $order_packing_form->packing_form_id = $packing_form->id;
            $order_packing_form->status_id = 6070003;
            $order_packing_form->save();


            MachineAllocationPackingForm::create([
                "order_id" => $machine_allocation->order_id,
                "contractor_id" => $machine_allocation->contractor_id,
                "machine_allocation_id" => $machine_allocation->id,
                "packing_form_id" => $packing_form->id,
                "need_to_complete_information" => 0
            ]);

        }

        return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => "اطلاعات بسته بندی ها با موفقیت ثبت گردید."]);
    }

    public
    function checkPermission(MachineAllocation $machine_allocation)
    {

        if ($machine_allocation->machine) {
            // ماشین
            $machine = $machine_allocation->machine;
            $allowed_status_ids = PostStatus::getAllowedStatus(3);
            if (!in_array($machine->production_status_id, $allowed_status_ids)) {
                return [
                    "result" => false,
                    "message" => "شما اجازه مشاهده ماشین را ندارید.",
                ];
            }

            $allowed_machine_ids = Line::getAllowedMachine();
            if (!in_array($machine->id, $allowed_machine_ids)) {
                return [
                    "result" => false,
                    "message" => "شما اجازه مشاهده ماشین را ندارید.",
                ];
            }
        } elseif ($machine_allocation->contractor) {
            // پیمانکار
            $register_production_controller = new \App\Http\Controllers\Contractor\Panel\RegisterProductionController();

            return $register_production_controller->checkPermission($machine_allocation);
        } elseif ($machine_allocation->order) {
            // مشتری
            $user_id = \Auth::user()->id;
            $is_customer = Customer::where("user_id", \Auth::id())->exists();
            if ($is_customer) {
                return SendingMaterialController::checkPermission($machine_allocation->order);
            } else {
                return \App\Http\Controllers\Sales\SendingMaterialController::checkPermission($machine_allocation->order);
            }

        }


    }

    public
    function getStatus($machine_allocation, $status_en, $check_software_system = true)
    {

        if ($machine_allocation->machine) {
            switch ($status_en) {
                case "production_form_in_production": // وضعیت فرم در حال تولید
                    return $machine_allocation->machine->machine_type->machine_module_type->production_form_in_production; // در حال تولید
                case "production_form_terminated": // وضعیت کارت خاتمه یافته فرم تولید
                    return $machine_allocation->machine->machine_type->machine_module_type->production_form_terminated; // خاتمه یافته

                case "production_card_terminated": // وضعیت کارت خاتمه یافته شدن کارت
                    return $machine_allocation->machine->machine_type->machine_module_type->production_card_terminated; // خاتمه یافته

                case "packing_form_sending":
                    return 7007005; // در انتظار تحویل به انبار
                case "packing_form_end_item":
                    // اگر سامانه جامع دارد، در انتظار ثبت در سامانه جامع پیمانکار
                    if (
                        $check_software_system &&
                        $machine_allocation->production->order &&
                        $machine_allocation->production->order->customer &&
                        $machine_allocation->production->order->customer->software_system_id
                    ) {
                        return 7007024; // در انتظار ثبت در سامانه جامع (تامین کننده/پیمانکار)
                    }
                    return 7007003; // تحویل شده به انبار
                case "packing_form_waiting_for_conform":
                    return 7007002; // در انتظار تایید انبار

                case "trans_kind":// دریافت از تولید
                    return 2;

                case "checking_form_not_delivered_at_register_production":
                    return $machine_allocation->machine->checking_form_not_delivered_at_register_production;

                case "checking_form_not_delivered_at_register_production_status_ids":
                    return [7007002, 7007005]; // در انتظار تایید انبار و در انتظار تحویل به انبار

                case "allocation_terminated":
                    return 5310020; // تخصیصی خاتمه یافته
                case "barcode_algorithm_id":
                    return 0;
                case "next_allocation_reserve":
                    return 5310040; // تخصیص رزرو بعدی
                case "current_allocation_reserve":
                    return 5310010; // تخصیص جاری
                case "next_allocation_terminated":
                    return 5310020; // تخصیصی خاتمه یافته
                    break;
                default:
                    1 / 0;

            }
        } elseif ($machine_allocation->contractor) {
            switch ($status_en) {
                case "production_form_in_production":
                    return 7002001; // در حال تولید
                case "production_form_terminated":
                    return 5310106; // خاتمه یافته Script1008

                case "production_card_terminated": // وضعیت کارت خاتمه یافته شدن کارت
                    return 7008005; // خاتمه یافته

                case "packing_form_sending":
                    return 7007008; // در انتظار ارسال محصول
                case "packing_form_end_item":
                    return 7007016; // تحویل شده به پیمانکار
                case "packing_form_waiting_for_conform":
                    return 7007009; // در انتظار تایید دریافت محصول

                case "trans_kind":
                    return 3;// دریافت از پیمانکار


                case "checking_form_not_delivered_at_register_production":
                    return $machine_allocation->contractor->checking_form_not_delivered_at_register_production;

                case "checking_form_not_delivered_at_register_production_status_ids":
                    return [7007008, 7007009]; // در انتظار تایید تحویل محصول و در انتظار ارسال محصول


                case "allocation_terminated":
                    return 5310106; // تخصیصی خاتمه یافته
                case "next_allocation_reserve":
                    return 5310104; // در حال تولید توسط پیمانکار
                case "current_allocation_reserve":
                    return 5310104; // در حال تولید توسط پیمانکار
                    break;
                case "next_allocation_terminated":
                    return 5310109; // ردیف تخصیص خاتمه یافته

                case "barcode_algorithm_id":
                    return $machine_allocation->contractor->barcode_algorithm_id ?? 0;
                default:
                    1 / 0;
            }
        } elseif ($machine_allocation->order) {
            // سفارش هایی که ارسال مواد اولیه دارند، بسته بندی هایشان را از این طریق ثبت می کنند.
            // دریافت امانی کالا از مشتری
            switch ($status_en) {
                case "production_form_in_production":
                    return 1 / 0;
                case "production_form_terminated":
                    return 7002005; // خاتمه یافته، چون هیچ کار دیگری انجام نمی دهد.


                case "production_card_terminated": // وضعیت کارت خاتمه یافته شدن کارت
                    return 7011003; // خاتمه یافته

                case "packing_form_sending":
                    return 7007008; // در انتظار ارسال محصول
                case "packing_form_end_item":
                    return 7007003; // تحویل شده به انبار
                case "packing_form_waiting_for_conform":
                    return 7007009; // در انتظار تایید دریافت محصول

                case "trans_kind":
                    return 4;// دریافت امانی

// آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار چک شود، (در صورت T بودن، باید همه بسته بندی ها تحویل به انبار شده باشند.)
                case "checking_form_not_delivered_at_register_production":
                    return false;

                case "checking_form_not_delivered_at_register_production_status_ids":
                    return [0]; // در انتظار تایید تحویل محصول و در انتظار ارسال محصول


                case "allocation_terminated":
                    return 5312102; // تخصیصی خاتمه یافته

                case "next_allocation_reserve":
                    return 0; // مشخص نیست
                case "current_allocation_reserve":
                    return 0; // مشخص نیست
                    break;
                case "barcode_algorithm_id":
                    return 0;
                default:
                    1 / 0;
            }
        }
    }

    /**
     * @return
     *  گرفتن انبار تحویل کالا
     */
    public static function GetWarehouseId(MachineAllocation $machineAllocation, $degree)
    {
        if (!$degree) {
            return [
                "result" => false,
                "error" => "درجه جهت انتخاب انبار کالا نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        }

        if ($machineAllocation->machine) {
            // خروجی هر ماشین با توجه به درجه آن کالا مشخص است که به کدام انبار باید تحویل شود.
            $output_band_warehouse = MachineTypeOutputBandWarehouse::where([
                "machine_type_id" => $machineAllocation->machine->machine_type_id,
                "goods_kind_id" => $degree->goods_kind_id,
                "degree_id" => $degree->id
            ])->first();
            if (!$output_band_warehouse) {
                return [
                    "result" => false,
                    "error" =>
                        "انبار تحویل کالا در گروه ماشین " . ($machineAllocation->machine->machine_type->caption ?? "***") .
                        " برای خروجی رسته کالایی " . ($degree->goods_kind->caption ?? "***") .
                        " و درجه " . ($degree->caption ?? "***") . " نامعتبر است،" . "<br/>" .
                        "لطفا با واحد پشتیبانی تماس بگیرید."
                ];

            }
            return [
                "result" => true,
                "warehouse_id" => $output_band_warehouse->warehouse_id
            ];
        } elseif ($machineAllocation->order) {
            $line_product_station = LineProductStation::where([
                "product_id" => $machineAllocation->product_id,
                "customer_id" => $machineAllocation->order->customer_id
            ])->first();
            if (!$line_product_station) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه مسیر محصول کالا (" . $machineAllocation->product->capiton . ") برای " . $machineAllocation->order->customer->caption . " تعریف نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید."
                ];
            }
            if ($line_product_station && !$line_product_station->applicant_warehouse_id) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه انبار تحویل کالا در مسیر محصول  (" . $machineAllocation->product->capiton . ") برای " . $machineAllocation->order->customer->caption . " مشخص نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید."
                ];
            }
            return [
                "result" => true,
                "warehouse_id" => $line_product_station->applicant_warehouse_id
            ];

        } else {
            return [
                "result" => true,
                "warehouse_id" => $degree->warehouse_id
            ];
        }
    }

    public
    function getIC(MachineAllocation $machine_allocation)
    {
        if ($machine_allocation->machine) {
            return $machine_allocation->machine->machine_type->cost_center->code;
        } elseif ($machine_allocation->contractor) {
            return $machine_allocation->contractor->getIC();
        } elseif ($machine_allocation->order) {
            return $machine_allocation->order->customer->getIC();
        }
        1 / 0;
    }

    public
    function getNextFormStatusId(MachineAllocation $machine_allocation, $current_status_id = 0, $has_general_item = false)
    {

        if ($machine_allocation->machine) {

            // در مواردی هست که بسته بندی خروجی ماشین نیاز دارد تا نرمالایز شود برای همین باید برریس کنیم که نیاز به نرمالاز می باشد یا خیر
            if ($machine_allocation->machine->machine_type->machine_module_type_id == 4) {
                $v_210 = MachineModuleTypePropertyValue::
                where("machine_module_type_property_id", 73030011210)->
                where("machine_type_id", $machine_allocation->machine->machine_type_id)->first();
                if ($v_210 && $v_210->value == 2) {
                    return 500000455; // نرمال کردن مقدار بسته بندی ها بعد از خاتمه یافته شدن تخصیص
                }
            }
            return 500000410; // در انتظار تایید انبار


        } elseif ($machine_allocation->contractor) {
            return $machine_allocation->contractor->nextStatusForInputForm($current_status_id, $has_general_item);
        } elseif ($machine_allocation->order) {
            return $machine_allocation->order->customer->nextStatusForInputForm($current_status_id, $has_general_item);
        } else {
            1 / 0;
        }
    }

    public function end_of_source_production_form_item(MachineAllocation $machine_allocation, $source_production_form_item_id)
    {
        $result = self::EndOfSourceProductionFormItem($machine_allocation, $source_production_form_item_id, 0);
        if ($result["result"]) {

            return redirect()->route($this->route_path . "index", [$machine_allocation])->with([
                "success" => "پایان آیتم ثبت گردید، لطفا برای آیتم بعدی ثبت تولید انجام دهید."
            ]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function EndOfSourceProductionFormItem(MachineAllocation $machine_allocation, $source_production_form_item_id, $is_end_of_item)
    {
        $source_production_form_item = null;
        if ($machine_allocation->parent_allocation_id) {
            $source_production_form_item = ProductionFormItem::find($source_production_form_item_id);
            if (!$source_production_form_item || $source_production_form_item->allocation_id != $machine_allocation->parent_allocation_id) {
                [
                    "result" => false,
                    "error" => "اطلاعات فرم تولید در انتظار تزریق ماشین نامعتبر است، لطفا با پشتیبانی تماس بگیرید."
                ];
            }
        }

//        $count_packing_form_temp = MachineAllocationPackingForm::where([
//            "machine_allocation_id" => $machine_allocation->id,
//            "status_id" => 7007006 // معلق
//        ])->count();
//        if ($count_packing_form_temp > 0) {
//            return [
//                "result" => false,
//                "error" => "لطفا قبل از ثبت پایان آیتم، بسته بندی های ثبت شده را ثبت نهایی کنید.  "
//            ];
//        }

        if ($is_end_of_item == 0) {
            // نوع تخلیه ماشین فرم تولید
            // فرض می کنیم نوع تخلیه برای همه عملیات ها یکسان است بنابراین اولین آن را برمی داریم.
            $first_station_operation =
                StationOperation::where("station_id", $source_production_form_item->production_form->machine->station_id)->first();

            $discharge_type_id = $first_station_operation->discharge_type_id;
            // پیدا کردن آیتم بعدی
            $next_form_item = ProductionFormItem::
            where("id", "!=", $source_production_form_item_id)->
            where("status_id", 7302004)-> // تزریق شده به ماشین
            where("allocation_id", $source_production_form_item->allocation_id)->
            orderBy("id", DischargeType::GetOrderReverse($discharge_type_id))->
            first();

            if (!$next_form_item) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه در حال تبت تولید برای آخرین آیتم می باشید، لطفا با ورود به صفحه ماشین، پایان عملیات را ثبت نمایید. "
                ];

            }
        }

        $count_production_form_item = ProductionFormItem::where("source_production_form_item_id", $source_production_form_item_id)->count();
        if ($count_production_form_item <= 0) {
//            return [
//                "result" => false,
//                "error" => "با توجه به اینکه هیچ بسته بندی برای آیتم فرم تولید " . $source_production_form_item->code . " ثبت نشده است، امکان ثبت پایان تولید برای آن وجود ندارد. "
//            ];
        }


        $source_production_form_item->status_id = 7302005; // پایان ثبت تولید برای آیتم.
        $source_production_form_item->save();

        $controller = new RegisterProductionController();

        if ($is_end_of_item == 0) {

            // گرفتن فرم جاری
            $production_form_select = ProductionForm::join("production_form_item", "production_forms.id", "production_form_id")->
            where([
                    "allocation_id" => $machine_allocation->allocation_id,
                    "production_forms.status_id" => $controller->getStatus($machine_allocation, "production_form_in_production"),
                ]
            )->
            select("production_forms.id")->
            first();
            // آز آنجایی که آیتم آخر نیست و آیتم پارچه عوض شده است، بنابراین، یک آیتم جدید اضافه می کنیم.
            ProductionFormItem::AddNewItem(
                $machine_allocation->allocation_id,
                $production_form_select->id,
                $machine_allocation->production_id,
                $machine_allocation->product_id,
                1,
                $controller->getStatus($machine_allocation, "production_form_in_production"),// در حال تولید
                0,
                $machine_allocation->version_code ?? null
            );
        }

        return [
            "result" => true,
            "source_production_form_item" => $next_form_item ?? null
        ];

    }

    public static function GetSourceProductionFormItem(MachineAllocation $machine_allocation)
    {
        // آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟
        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine_allocation->machine->machine_type_id);
        // آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine_allocation->machine->machine_type_id);

        if ($value_202 == 1 && $value_201 == 0) {
            // برای ماشین بسته بندی در رز رنگ این اقدام انجام شد.
            return [
                "result" => true,
                "source_production_form_item" => null,
            ];
        }

        $source_production_forms = ProductionFormItem::
        where("allocation_id", $machine_allocation->parent_allocation_id)->
        where("status_id", 7302004)-> // تزریق شده به ماشین
        where("product_id", $machine_allocation->product_id)->
        groupBy("production_form_id")->
        with("production_form", "production_form.packing_type")->
        get();

        if (count($source_production_forms) != 1) {
            return [
                "result" => false,
                "error" => "هیچ فرم تولید به ماشین تزریق نشده است، لطفا ابتدا شروع عملیات را در ماشین ثبت نمایید."
            ];
        }

        $source_production_form = $source_production_forms[0];

        $discharge_type_id = $source_production_form->production_form->packing_type->discharge_type_id ?? false;

        if (!$discharge_type_id) {
            return [
                "result" => false,
                "error" => "نوع تخلیه برای فرم تولید " . $source_production_form->code . " نامشخص است، لطفا با واحد پشیتبانی تماس بگیرید."
            ];
        }

        $order_type = DischargeType::GetOrder($discharge_type_id);
        $source_production_form_item = ProductionFormItem::
        where("allocation_id", $machine_allocation->parent_allocation_id)->
        where("production_form_id", $source_production_form->production_form_id)->
        where("status_id", 7302004)-> // تزریق شده به ماشین
        where("product_id", $machine_allocation->product_id)->
        orderBy("id", $order_type)->
        first();

        if (!$source_production_form_item) {
            return [
                "result" => false,
                "error" => "فرم تولید تزریق شده به ماشین نامعتبر است.",
            ];
        }

        return [
            "result" => true,
            "source_production_form_item" => $source_production_form_item,
        ];
    }

    /******************************* Copy of Packing form *************************/

    public
    function copy_packing_form(Request $request, MachineAllocation $machine_allocation, PackingForm $packing_form)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        $machine_allocation_packing_form = MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "packing_form_id" => $packing_form->id
        ])->first();
        if (!$machine_allocation_packing_form) {
            return back()->withErrors("کد بسته بندی و شناسه تخصیص نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.");
        }
        if ($machine_allocation_packing_form->need_to_complete_information == 1) {
            return back()->withErrors(" کپی از بسته بندی هایی که نیاز به تکمیل اطلاعات دارند، امکان پذیر نیست، لطفا بسته یندی دیگری را انتخاب نمایید ویا اطلاعات تکمیلی این بسته بندی را کامل کنید. ");
        }

        if ($packing_form->status_id != 7007011) {
            return back()->withErrors("امکان ثبت کپی از بسته بندی هایی که در وضعیتی به غیز از وضعیت معلق هستند امکان پذیر نیست");
        }

        $max_of_copy_packing_form = $this->max_of_copy_packng_form;
        return view($this->view_path . "copy_packing_form", compact("machine_allocation", "packing_form", "max_of_copy_packing_form"));


    }

    public
    function submit_copy_packing_form(Request $request, MachineAllocation $machine_allocation, PackingForm $packing_form)
    {

        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        $max_of_copy_packing_form = $this->max_of_copy_packng_form;
        $number_of_copy = $request->number_of_copy;
        $number_of_copy = $number_of_copy < 1 ? 0 : $number_of_copy;
        $number_of_copy = $number_of_copy > $max_of_copy_packing_form ? 0 : $number_of_copy;

        if ($number_of_copy == 0) {
            return back()->withErrors("لطفا عدد تعداد کپی از بسته بندی را اصلاح فرمایید.");
        }

        $machine_allocation_packing_form = MachineAllocationPackingForm::where([
            "machine_allocation_id" => $machine_allocation->id,
            "packing_form_id" => $packing_form->id
        ])->first();
        if (!$machine_allocation_packing_form) {
            return back()->withErrors("کد بسته بندی و شناسه تخصیص نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.");
        }
        if ($machine_allocation_packing_form->need_to_complete_information == 1) {
            return back()->withErrors(" کپی از بسته بندی هایی که نیاز به تکمیل اطلاعات دارند، امکان پذیر نیست، لطفا بسته یندی دیگری را انتخاب نمایید ویا اطلاعات تکمیلی این بسته بندی را کامل کنید. ");
        }
        if ($packing_form->status_id != 7007011) {
            return back()->withErrors("امکان ثبت کپی از بسته بندی هایی که در وضعیتی به غیز از وضعیت معلق هستند امکان پذیر نیست");
        }
        $result_max = self::CheckProductionTerminate($machine_allocation, 'check_max', $number_of_copy * $packing_form->getFinalAmount(7));
        if (!$result_max["result"]) {
            return back()->withErrors($result_max["error"]);
        }


        $packing_form_for_copy = $packing_form;
        $packing_form_ids = [];
//return $packing_form_for_copy;
        for ($k = 0; $k < $number_of_copy; $k++) {


            $packing_form_new = PackingForm::create([
                "packing_type_id" => $packing_form_for_copy->packing_type_id,
                "carrier_id" => null, //
                "status_id" => 7007011, // معلق
                "sub_packing_form_number" => $packing_form_for_copy->sub_packing_form_number,
                "applicant_type_id"=>$packing_form_for_copy->applicant_type_id??null,
                "applicant_id"=>$packing_form_for_copy->applicant_id??null,
            ]);

            $packing_form_new->weight = $packing_form_for_copy->weight;
            $packing_form_new->gross_weight = $packing_form_for_copy->gross_weight;

            foreach ($packing_form_for_copy->items as $packing_form_item_copy) {
                $new_master_packing_form_item = PackingFormItem::create([
                    "packing_form_id" => $packing_form_new->id,
                    "product_id" => $packing_form_item_copy->product_id,
                    "lot_number_id" => $packing_form_item_copy->lot_number_id,
                    "degree_id" => $packing_form_item_copy->degree_id,
                    "amount" => $packing_form_item_copy->amount,
                    "amount_after_control" => $packing_form_item_copy->amount_after_control,
                    "final_amount" => $packing_form_item_copy->final_amount,
                    "sub_amount" => $packing_form_item_copy->sub_amount,
                    "init_sub_amount" => $packing_form_item_copy->sub_amount,
                    "status_id" => $packing_form_item_copy->status_id, // بسته بندی شده
                    "band_code" => $packing_form_item_copy->band_code,
                    "production_form_item_id" => $packing_form_item_copy->production_form_item_id,
                    "production_form_item_lot_number_id" => $packing_form_item_copy->production_form_item_lot_number_id,
                ]);
                $new_master_packing_form_item->getCode();
            }
            $packing_form_new->getCode();

            event(new PackingLogEvent($packing_form_new, 7007001));

            MachineAllocationPackingForm::create([
                "machine_id" => $machine_allocation->machine_id,
                "contractor_id" => $machine_allocation->contractor_id,
                "machine_allocation_id" => $machine_allocation->id,
                "packing_form_id" => $packing_form_new->id,
                "need_to_complete_information" => 0
            ]);

            $packing_form_ids[] = $packing_form_new->id;
        }


// ثبت درخواست پرینت بسته بندی ها
        $data = [
            "packing_form_ids" => $packing_form_ids,
            "worker_id" => Auth::id(),
        ];

        QueueOfLargeOperation::AddToQueue($data, 300);

        return redirect()->route($this->route_path . "index", $machine_allocation)->with(["success" => "درخواست ثبت کپی از بسته بندی ثبت گردید و چاپ لیبل بسته بندی ها در انتظار پردازش قرار گرفت"]);

    }

    public static function CopyPackingFrom(QueueOfLargeOperation $largeOperation)
    {
        $data = json_decode($largeOperation->data, true);
        $machine_allocation = MachineAllocation::find($data["machine_allocation_id"]);
        $packing_form_for_copy = PackingForm::find($data["packing_form_item_id"]);
        $number_of_copy = $data["number_of_copy"];


        if (!$machine_allocation) {

            return [
                "result" => false,
                "error" => "شماره تخصیص با شناسه " . $data["machine_allocation_id"] . " یافت نشد، امکان پردازش درخواست " . $largeOperation->id . " وجود ندارد."
            ];
        }

        if (!$packing_form_for_copy) {

            return [
                "result" => false,
                "error" => "بسته بندی با شناسه " . $data["packing_form_item_id"] . " یافت نشد، امکان پردازش درخواست " . $largeOperation->id . " وجود ندارد."
            ];
        }

        if ($number_of_copy < 1) {

            return [
                "result" => false,
                "error" => "تعداد کپی از بسته بندی نباید کوچکتر از 1 باشد، تعداد " . $data["number_of_copy"] . " نامعتبر است، امکان پردازش درخواست " . $largeOperation->id . " وجود ندارد."
            ];
        }


    }

    public function pallet_info($action_type, $current_pallet, MachineAllocation $machine_allocation, Worker $worker)
    {
        $packing_type_option = Option::get("production_packing_type", 0, 0, $machine_allocation->production->packing_types);
        $lot_number = null;
        $final_amount = "";
        $default_property_value = [];
        $message_add_packing = null;
        $success_message = null;
        $pallet = null;

        switch ($action_type) {

            case "create_new_pallet": // ایجاد پالت جدید
                // اگر قبلا پالت وجود داشته آن را پر شده می کند  و یگ پالت جدید ایجاد می کند.
                // پالت قبلی باید پر شده باشد.
                if ($current_pallet) {
                    $pallet = $current_pallet;
                    $message_add_packing = "یک پالت در حال تکمیل وجود دارد، لطفا ابتدا آن را تکمیل نمایید";
                    $pallet = $current_pallet;
                } else {
                    $pallet = Pallet::CreateNewPallet($machine_allocation->allocation);
                    $success_message = "یک پالت با موفقیت ایجاد گردید";
                }
                break;
            case "create_new_pallet_and_print_a4": // بستن پالت قبلی و ایجاد پالت جدید
            case "create_new_pallet_and_print": // بستن پالت قبلی و ایجاد پالت جدید
                if ($current_pallet) {
                    if ($current_pallet->items()->count() > 0) {
                        $packing_type_label_printing_type_id = $action_type == "create_new_pallet_and_print" ? 302 : 8;
                        PrintPalletController::direct_print($current_pallet, $worker, $packing_type_label_printing_type_id);

                        $current_pallet->status_id = 6080002; // پر شده
                        $current_pallet->save();

                        $pallet = Pallet::CreateNewPallet($machine_allocation->allocation);

                        $success_message = "پالت شماره " . $current_pallet->id . " پرینت گردید و یک پالت جدید ایجاد شد.";

                    } else {
                        $message_add_packing = "برای پایان پالت حداقل باید یک بسته بندی انتخاب کنید.";

                        $pallet = $current_pallet;
                    }

                } else {
                    $message_add_packing = "هیچ پالت در حال تکمیلی وجود ندارد، لطفا ابتدا یک پالت ایجاد نمایید.";
                    $pallet = $current_pallet;
                }
                break;

            case "end_of_pallet_a4": // بستن پالت و چاپ
            case "end_of_pallet": // بستن پالت و چاپ
                if (isset($current_pallet)) {
                    if ($current_pallet->items()->count() > 0) {
                        $packing_type_label_printing_type_id = $action_type == "end_of_pallet" ? 302 : 8;
                        PrintPalletController::direct_print($current_pallet, $worker, $packing_type_label_printing_type_id);

                        $current_pallet->status_id = 6080002; // پر شده
                        $current_pallet->save();

                        $success_message = "پالت شماره " . $current_pallet->id . " پرینت گردید .";

                    } else {
                        $message_add_packing = "برای پایان پالت حداقل باید یک بسته بندی انتخاب کنید.";
                        $pallet = $current_pallet;
                    }

                } else {
                    $message_add_packing = "هیچ پالت در حال تکمیلی وجود ندارد، لطفا ابتدا یک پالت ایجاد نمایید.";
                    $pallet = $current_pallet;
                }
                break;

            default:
                return [
                    "result" => false,
                ];
                break;

        }


        return [
            "result" => true,
            "current_pallet" => $current_pallet,
            "pallet" => $pallet,
            "message_add_packing" => $message_add_packing,
            "success_message" => $success_message,
            "packing_type_option" => $packing_type_option,
            "lot_number" => $lot_number,
            "final_amount" => $final_amount,
            "default_property_value" => $default_property_value,
        ];

    }

    /**
     * @param MachineAllocation $machine_allocation
     * @param $source_production_form_item_id
     * @return array
     * اگر یک تخصیص شامل چند کارت تولید باشد، و بخواهند همه آنها را با هم تولید کنند لازم است تا وقتی یک کارت به پایان می رسد بتوانند کارت بعدی را تولید کنند
     */
    public static function GoToNextMachineAllocation(MachineAllocation $machine_allocation)
    {
        $controller = new  self();
// رزرو بعدی
        $reserve_status_id = $controller->getStatus($machine_allocation, "next_allocation_reserve");
        // جاری
        $current_allocation_id = $controller->getStatus($machine_allocation, "current_allocation_reserve");
        // خاتمه یافته
        $allocation_terminated_id = $controller->getStatus($machine_allocation, "next_allocation_terminated");

//        if ($machine_allocation->status_id != $current_allocation_id) {
//            return [
//                "result" => false,
//                "error" => " برای ثبت ادامه با کارت تولید بعدی، باید تخصیص انتخاب شده جاری باشد، لطفا یکبار دیگر تلاش کنید."
//            ];
//
//        }


        $next_machine_allocation = MachineAllocation::
        where([
            "allocation_id" => $machine_allocation->allocation_id,
            "status_id" => $reserve_status_id])->
        where("id", "!=", $machine_allocation->id)->
        first();

        if (!$next_machine_allocation) {
            return [
                "result" => false,
                "error" => "کارت تولید رزرو بعدی بر روی تخصیص یافت نشد، لطفا یکبار دیگر تلاش کنید."
            ];

        }

        $machine_allocation->status_id = $allocation_terminated_id; //خاتمه یافت
        $machine_allocation->save();


        $next_machine_allocation->status_id = $current_allocation_id; // جاری
        $next_machine_allocation->save();

        if ($machine_allocation->machine) {
            $machineLog = new MachineLog();
            $machineLog->machine_event_type_id = 5310911; // پایان عملیات و ادامه ثبت
            $machineLog->allocation_id = $machine_allocation->allocation_id;

            event(new MachineLogEvent($machine_allocation->machine, $machineLog));
        } elseif ($machine_allocation->contractor) {

        }
        return [
            "result" => true,
            "next_machine_allocation" => $next_machine_allocation,
            "message" => "تغییرات انجام شد، لطفا ادامه ثبت را برای کارت تولید" . $next_machine_allocation->production->serial . " انجام دهید."
        ];

    }

    /**
     * چک کردن اینکه اجازه ثبت بسته بندی جدید داریم یا خیر
     */
    public static function CheckingFormNotDelivered(MachineAllocation $machine_allocation)
    {

        $controller = new RegisterProductionController();
        $checking_form_not_delivered_at_register_production = $controller->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production");
        if ($checking_form_not_delivered_at_register_production) {

            $checking_form_status_ids = $controller->getStatus($machine_allocation, "checking_form_not_delivered_at_register_production_status_ids");
            $packing_form_list = PackingForm::join("machine_allocation_packing_form", "packing_form_id", "packing_forms.id")->
            where("machine_allocation_id", $machine_allocation->id)->
            whereIn("packing_forms.status_id", $checking_form_status_ids)->
            select("packing_forms.*")->
            get();

            $count_packing_form = count($packing_form_list);

            if ($count_packing_form > 0) {

                $message_add_packing = "با توجه به اینکه بسته بندی های زیر توسط "
                    . ($machine_allocation->machine ? " انبار " : " پیمانکار ") .
                    "تایید نشده است امکان افزودن بسته بندی جدید وجود ندارد.";
                $k = 0;
                foreach ($packing_form_list as $item) {
                    $k++;
                    $message_add_packing .= "<br/> $k - " . $item->getCode() . "(" . $item->status->caption . ")";
                    if ($k > 4) {
                        break;
                    }
                }
                if ($count_packing_form > $k) {
                    $message_add_packing .= "<br/>" . " و " . ($count_packing_form - $k) . " بسته بندی دیگر";
                }


                return [
                    "result" => false,
                    "error" => $message_add_packing
                ];

            }
        }

        return [
            "result" => true
        ];

    }
}