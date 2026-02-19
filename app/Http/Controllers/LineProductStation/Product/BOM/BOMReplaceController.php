<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BOMReplaceController extends Controller
{
    //
    var $view_path = "line_product_station.product.bom.bom_replace.";
    var $route_path = "line_product_station.product.bom.bom_replace.";

    public function index(Product $product, Product $material, BOMItem $bom_item, $product_creation_process = null)
    {


        $list = BOMReplace::
        where([
            "product_id" => $product->id,
            "material_id" => $material->id,
            "bill_of_material_item_id" => $bom_item->id
        ])->
        get();

        $replace_product_list = ReplaceProduct::where("product_id", $material->id)->get();


        return view($this->view_path . "index", compact("product", "material", "list", "replace_product_list", "bom_item", "product_creation_process"));
    }

    public function store(Request $request, Product $product, Product $material, BOMItem $bom_item, $product_creation_process = null)
    {

//return $request->replace_product;
        if (!isset($request->replace_product)) {
            return back()->withErrors("لطفا حداقل یک کالای جایگزین انتخاب نمایید. ");
        }

        $has_priority_one = false;
        $has_priority_one = $has_priority_one || ($request->priority_number_in_replace == 1);
        $priority_number[$request->priority_number_in_replace] = true;
        $bom_replace_ids = [];
        foreach ($request->replace_product as $replace_product_id => $val) {


            if (isset($priority_number[round($request["priority_number"][$replace_product_id])])) {

                return back()->withErrors(" اولویت " .
                    "انتخاب شده" .
                    " تکراری است.");
            } else {
                $priority_number[round($request["priority_number"][$replace_product_id])] = true;
            }

            $has_priority_one = $has_priority_one || (round($request["priority_number"][$replace_product_id]) == 1);


        }
        if (!$has_priority_one) {
            return back()->withErrors("لطفا اولویت 1 را مشخص نمایید.");
        }

        foreach ($request->replace_product as $replace_product_id => $val) {

            $bom_replace_ids[] = $replace_product_id;

            $bom_replace = BOMReplace::where([
                "bill_of_material_id" => $bom_item->bill_of_material_id,
                "bill_of_material_item_id" => $bom_item->id,
                "product_id" => $product->id,
                "material_id" => $material->id,
                "replace_product_id" => $replace_product_id,
            ])->first();

            if (!$bom_replace) {
//                if (
//                    BOMReplace::where([
//                        "bill_of_material_id" => $bom_item->bill_of_material_id,
//                        "bill_of_material_item_id" => $bom_item->id,
//                        "product_id" => $product->id,
//                        "material_id" => $material->id,
//                    ])->exists()
//                ) {
//                    return back()->withErrors("اولویت انتخاب تکراری است، لطفا برای هر اولویت یک عدد یکتا انتخاب نمایید.");
//                }


                BOMReplace::create([
                    "bill_of_material_id" => $bom_item->bill_of_material_id,
                    "bill_of_material_item_id" => $bom_item->id,
                    "product_id" => $product->id,
                    "material_id" => $material->id,
                    "replace_product_id" => $replace_product_id,
                    "amount" => $request["amount"][$replace_product_id],
                    "number" => $request["number"][$replace_product_id],
                    "percent_of_use" => $request["percent_of_use"][$replace_product_id],
                    "priority_number" => round($request["priority_number"][$replace_product_id]),
                    "waste_prediction" => round($request["waste_prediction"][$replace_product_id]),
                    "consumption_correction_factor" => round($request["consumption_correction_factor"][$replace_product_id]),
                    "consumption_correction_factor_prediction" => round($request["consumption_correction_factor_prediction"][$replace_product_id]),

                ]);



            } else {
                $bom_replace->update([
                    "amount" => $request["amount"][$replace_product_id],
                    "number" => $request["number"][$replace_product_id],
                    "percent_of_use" => $request["percent_of_use"][$replace_product_id],
                    "priority_number" => round($request["priority_number"][$replace_product_id]),
                    "waste_prediction" => round($request["waste_prediction"][$replace_product_id]),
                    "consumption_correction_factor" => round($request["consumption_correction_factor"][$replace_product_id]),
                    "consumption_correction_factor_prediction" => round($request["consumption_correction_factor_prediction"][$replace_product_id]),
                ]);


            }
        }


        // حذف آنهایی که انتخاب آنها حذف شده است.
        $bom_replace_ids[]=-1;
        $list = BOMReplace::
        where([
            "product_id" => $product->id,
            "material_id" => $material->id,
            "bill_of_material_item_id" => $bom_item->id
        ])->
        whereNotIn("replace_product_id", $bom_replace_ids)->
        delete();

        // بازسازی کالاهای جایگزین برای کالا
        Product\BOM\BOMPermutation::CreateBOMMood($bom_item->bom);

        if (
            BOMReplace::where([
                "bill_of_material_id" => $bom_item->bill_of_material_id,
                "bill_of_material_item_id" => $bom_item->id,
                "product_id" => $product->id,
                "material_id" => $material->id,
                "priority_number" => round($request->priority_number_in_replace)
            ])->exists()
        ) {
            return back()->withErrors("اولویت انتخاب تکراری است، لطفا برای هر اولویت یک عدد یکتا انتخاب نمایید.");
        }


        $bom_item->priority_number_in_replace = $request->priority_number_in_replace;
        $bom_item->save();

        if ($product_creation_process) {
            return redirect()->route("line_product_station.product.product_creation.bom.index", $product_creation_process)->with(["success" => " کالاهای جایگزین با موفقیت ثبت گردید."]);

        } else {
            return redirect()->route("line_product_station.product.bom.index", [$product])->with(["success" => " کالاهای جایگزین با موفقیت ثبت گردید."]);

        }
    }

    public function delete(BOMItem $bom_item, $product_id, $material_id, $replace_product_id)
    {

        $current_machine_input = CurrentMachineInput::join("allocations", "allocation_id", "allocations.id")->
        where([
            "product_id" => $product_id,
            "material_id" => $replace_product_id
        ])->whereIn("allocations.status_id", [5310010, 5310040])->first();

        if ($current_machine_input) {
            return back()->withErrors("کالای جایگزین مورد نظر در تخصیص شماره " . $current_machine_input->allocation_id .
                " در حال استفاده می باشد، لطفا پس از خاتمه یافته شدن تخصیص اقدام به حذف کالای جایگزین نمایید.");
        }

        BOMReplace::where([
            "bill_of_material_item_id" => $bom_item->id,
            "product_id" => $product_id,
            "material_id" => $material_id,
            "replace_product_id" => $replace_product_id
        ])->delete();

        // اگر هیچ کالای جایگزینی دیگر وجود ندارد، اولویت کالای اصلی را یک قرار بده
        $count = BOMReplace::where([
            "bill_of_material_item_id" => $bom_item->id,
            "product_id" => $product_id,
            "material_id" => $material_id,
        ])->count();
        if ($count == 0) {
            $bom_item->priority_number_in_replace = 1;
            $bom_item->save();
        }

        // بازسازی کالاهای جایگزین برای کالا
        Product\BOM\BOMPermutation::CreateBOMMood($bom_item->bom);

        return back()->with(["success" => "عملیات حذف با موفقیت انجام شد."]);
    }

}
