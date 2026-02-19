<?php

namespace App\Http\Controllers\LineProductStation\Reservoir;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\MergerController;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductReservoir;
use App\Models\LineProduct\Reservoir\Reservoir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InjectToReservoirController extends Controller
{
    //
    public $view_path = "line_product_station.reservoir.inject_to_reservoir.";
    public $route_path = "line_product_station.reservoir.inject_to_reservoir.";
    public $route_dashboard = "line_product_station.reservoir.dashboard.";

    public function index(Reservoir $reservoir)
    {
        return view($this->view_path . "index", compact('reservoir'));

    }

    public function submit(Request $request, Reservoir $reservoir)
    {

        $packing_form = PackingForm::where("code", "DCPK/" . $request->packing_form_code)->first();
        if (!$packing_form) {
            return back()->withErrors("کد بسته بندی نامتعبر است.");
        }

        $inject_to_reservoir_result = self::InjectToReservoir($reservoir, $packing_form);

        if ($inject_to_reservoir_result["result"]) {
            return redirect()->route($this->route_dashboard . "index", $reservoir)->with(["success" => "تزریق با موفقیت انجام شد."]);
        } else {
            return back()->withErrors($inject_to_reservoir_result["error"]);
        }


    }


    public static function InjectToReservoir(Reservoir $reservoir, $packing_form)
    {
        if ($packing_form->items()->count() != 1) {
            return [
                "result" => false,
                "error" => "تعداد آیتم های بسته بندی نامعتبر است."
            ];
        }
        if ($packing_form->status_id != 7007003 || !$packing_form->warehouse) {
            return [
                "result" => false,
                "error" => "وضعیت بسته بندی جهت ادغام باید تحویل شده به انبار باشد."
            ];
        }
        if (!$reservoir->warehouse_id) {
            return [
                "result" => false,
                "error" => "انبار مخزن مشخص نشده است."
            ];
        }


        foreach ($packing_form->items as $packing_form_item) {

            // چک کردن کالای مجاز
            $exist_product = ProductReservoir::
            where("product_id", $packing_form_item->product_id)->
            where("reservoir_id", $reservoir->id)->
            exists();
            if (!$exist_product) {

                return [
                    "result" => false,
                    "error" => "کالای " . $packing_form_item->product->fullCaption() . " جزء کالاهای مجاز جهت ورود به مخزن نمی باشد."
                ];
            }

            // چک کردن لات و درجه
            if ($reservoir->packing_form) {
                $packing_form_item_reservoir = $reservoir->packing_form->items()->where("product_id", $packing_form_item->product_id)->first();
                if (
                    $packing_form_item_reservoir &&
                    $packing_form_item_reservoir->degree_id != $packing_form_item->degree_id
                ) {
                    return [
                        "result" => false,
                        "error" => "درجه کالای در حال تزریق با درجه کالای موجود در مخزن متفاوت است و امکان تزریق برای " . $packing_form_item->product->fullCaption() . " وجود ندارد."
                    ];
                }
                if (
                    $packing_form_item_reservoir &&
                    $packing_form_item_reservoir->lot_number_id != $packing_form_item->lot_number_id
                ) {
                    return [
                        "result" => false,
                        "error" => "لات(همبافت) کالای در حال تزریق با لات(همبافت) کالای موجود در مخزن متفاوت است و امکان تزریق برای " . $packing_form_item->product->fullCaption() . " وجود ندارد."
                    ];
                }
            }


        }

        // چک کردن ظرفیت مخزن
        $current_capacity = $reservoir->packing_form ? $reservoir->packing_form->getAmount() : 0;
        $capacity = ($current_capacity) + $packing_form->getAmount();
        $packing_form_item = $packing_form->items()->first();
        if ($capacity > $reservoir->capacity) {
            return [
                "result" => false,
                "error" => "با توجه به ظرفیت مخزن امکان تزریق بسته بندی به مخزن وجود ندارد." .
                    "<br/>موجودی مخزن:" . $current_capacity . " " . $packing_form_item->product->unit->caption .
                    "<br/>" . "ظرفیت مخزن:" . $reservoir->capacity . " " . $packing_form_item->product->unit->caption];
        }

        // ثبت ورود و خروج

        return $result = MergerController::NormalMerge($reservoir->packing_form, $packing_form, "AddToReservoir");


    }

}