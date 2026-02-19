<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineTypeOutputBandWarehouse;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;

class DeliveryToWarehouseController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.packing_form.delivery_to_warehouse.",
        "enable_status" => ["005", "010"],
        "button" => ["caption" => "تکمیل و تحویل به انبار", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از تایید و تحویل به انبار اطمینان دارید؟"],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.delivery_to_warehouse.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";


    public function __construct()
    {
        $this->route_path = DeliveryToWarehouseController::$info["route"];
    }

    public function get_nosa_code(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        return view($this->view_path . "get_nosa_code", compact("packing_form"));
    }

    public function submit_nosa_code(Request $request, PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }
        foreach ($packing_form->items as $item) {
            if ($item->lot_number->nosa_code == null) {
                $nosa_code = "nosa_code_" . $item->id;
                if (!$request->$nosa_code != "") {
                    return back()->withErrors("لطفا کد نوسا برای همه کالا ها را وارد نمایید.");
                } else {
                    $item->lot_number->nosa_code = $request->$nosa_code;
                    $item->lot_number->save();
                }
            }
        }

        return redirect()->route($this->dashboard_route . "view", $packing_form)->with(["success" => "کد نرم افزار مالی ثبت گردید."]);

    }

    public function submit(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }
        if (count($packing_form->items) == 0) {
            return back()->withErrors("اقلام بسته بندی خالی می باشد.");
        }

// اگر بسته بندی در مازول ثبت تولید 1 ایجاد شده است باید همانجا تحویل به انبار را بزنند.

        $machine_allocation_packing_forms=MachineAllocationPackingForm::where("packing_form_id",$packing_form->id)->first();
        if($machine_allocation_packing_forms ){
            return back()->withErrors("با توجه به اینکه بسته بندی در ماژول ثبت تولید و در تخصیص شماره ".$machine_allocation_packing_forms->machine_allocation->allocation_id." ایجاد شده است، لازم است تا برای تحویل کالا (ارسال کالا)  به انبار از منو ارسال به انبار در ماژول ثبت تولید اقدام نمایید.");
        }


        $lot_nomber_is_allowed_setting = Setting::find(5)->integer_value;
        foreach ($packing_form->items as $item) {

            //چک کردن کد نوسا
            if ($item->lot_number->nosa_code == null && !$lot_nomber_is_allowed_setting) {
                return redirect()->route($this->route_path . "get_nosa_code", $packing_form);
            }

            if (!$item->product->has_product_type_permission($packing_form->packing_type_id)) {
                return back()->withErrors("نوع بسته بندی " . $packing_form->packing_type->caption . " برای کالای " . $item->product->fullCaption() . " معتبر نمی باشد.");
            }
        }

        $warehouse_id = 0;
        foreach ($packing_form->items as $item) {

            // اگر از یک دستگاه تولید شده باشد و فرم تولید داشته باشد، انبار فرم ورود از خروجی های دستگاه می باشد، در غیر این صورت از انیار پیش فرض درجه در رسته کالایی استفاده می کنیم.

            if ($item->production_form_item && $item->production_form_item->production_form && $item->production_form_item->production_form->machine) {

                $output_band_warehouse = MachineTypeOutputBandWarehouse::where([
                    "machine_type_id" => $item->production_form_item->production_form->machine->machine_type_id??0,
                    "goods_kind_id" => $item->product->goods_kind_id,
                    "degree_id" => $item->degree_id
                ])->first();
                if (!$output_band_warehouse) {
                    return back()->withErrors("انبار تحویل کالا در گروه ماشین " . ($item->production_form_item->production_form->machine->machine_type->caption??"***")
                        . " برای خروجی رسته کالایی " . ($item->product->goods_kind->caption??"***") .
                        " و درجه " . ($item->degree->caption??"***") . " نامعتبر است،" ."<br/>".
                        "لطفا با واحد پشتیبانی تماس بگیرید.");
                }


                if ($warehouse_id != 0 && $output_band_warehouse && $warehouse_id != $output_band_warehouse->warehouse_id) {
                    return back()->withErrors("از آنجایی که درجه های کالاو انبار هر درجه در تنظیمات گروه ماشین ".($item->production_form_item->production_form->machine->machine_type->caption??"***").
                        " برای رسته کالایی ".($item->product->goods_kind->caption??"***").
                        " متفاوت است، امکان تشخیص انبار ورودی وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
                }
                $warehouse_id = $output_band_warehouse->warehouse_id;
            }
            else{

                if (!$item->degree->warehouse) {
                    return back()->withErrors("انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد.");
                }
                if ($warehouse_id != 0 && $warehouse_id != $item->degree->warehouse_id) {
                    return back()->withErrors("از آنجایی که درجه های کالا و انبار هر درجه جهت تحویل در تنظیمات رسته کالایی متفاوت است، امکان تشخیص انبار ورودی وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
                }
                $warehouse_id = $item->degree->warehouse_id;
            }


        }


        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => 2,
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000410, // در انتظار تایید انبار
        ]);
        $form->getCode();
        $ic = null;
        foreach ($packing_form->items as $item) {
            FormItem::create([
                "form_id" => $form->id,
                "packing_form_item_id" => $item->id,
                "packing_type_id" => $packing_form->packing_type_id,
                "product_id" => $item->product_id,
                "amount" => $item->final_amount,
                "sub_amount" => $item->sub_amount,
                "carrier_id" => $packing_form->carrier_id,
                "degree_id" => $item->degree_id,
                "lot_number_id" => $item->lot_number_id,
                "description" => "دریافت کالا با کد بسته بندی " . ($item->code ?? "")
            ]);
            $item->status_id = 500000410; //  در انتظار تایید انبار
            $item->save();

            if ($ic == null) {
                //محاسبه مرکز هزینه
                // بسته بندی یا توسط پیمانکار تولید شده و یا توسط ماشین

                if (isset($item->production_form_item->production_form->machine)) {
                    $machine = $item->production_form_item->production_form->machine;
                    $ic = $machine->machine_type->cost_center->code ?? -1;
                } elseif (isset($item->production_form_item->production_form->contractor)) {
                    $contractor = $item->production_form_item->production_form->contractor;
                    $ic =$contractor? $contractor->getIC():"";
                }
            }
        }

        $form->ic = $ic;
        $form->save();

        event(new FormLogEvent($form));

        $packing_form->status_id = 7007002; //  در انتظار تایید انبار
        $packing_form->form_id = $form->id;
        $packing_form->save();
        event(new PackingLogEvent($packing_form, 7007005));

        // تغییر وضعیت حامل
        if ($packing_form->carrier) {
            $packing_form->carrier->SetStatus(5320007, null, 5320107, null, null);
        }

        return redirect()->route($this->dashboard_route . "index");
    }


    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, DeliveryToWarehouseController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
