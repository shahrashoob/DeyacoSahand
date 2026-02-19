<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BOMDegreeController extends Controller
{
    //
    var $view_path = "line_product_station.product.bom.bom_degree.";
    var $route_path = "line_product_station.product.bom.bom_degree.";

    public function index(Product $product, Product $material, BOMItem $bom_item, $product_creation_process = null)
    {


        $list = BOMDegree::
        where([
            "product_id" => $product->id,
            "material_id" => $material->id,
            "bill_of_material_item_id" => $bom_item->id
        ])->
        get();

        $degree_option = Option::get("degree", 0, $material->goods_kind_id);

        $input_line_code_from = session("input_line_code_from");
        $input_line_code_to = session("input_line_code_to");

        return view($this->view_path . "index", compact("product", "material", "list", "degree_option", "bom_item", "input_line_code_from", "input_line_code_to", "product_creation_process"));
    }


    public function store(Request $request, Product $product, Product $material, BOMItem $bom_item, $product_creation_process = null)
    {

        $degree_ids=[];
        if($request->all_degree_ids){
            $degree_ids=Degree::where("goods_kind_id", $material->goods_kind_id)->where("active_status_id", 1200)->pluck("id")->toArray();
        }
        else{
            $degree_ids[]=$request->degree_id;
        }

        if(count($degree_ids)==0){
            return back()->withErrors("لطفا حداقل یک درجه انتخاب نمایید");
        }
        switch ($bom_item->product->supply_type_id) {
            case 1:
                for ($input_line_code = $request->input_line_code_from; $input_line_code <= $request->input_line_code_to; $input_line_code++) {

                    $bom_item_degree = BOMItem::where([
                        "bill_of_material_id" => $bom_item->bill_of_material_id,
                        "product_id" => $bom_item->product->id,
                        "material_id" => $bom_item->material_id,
                        "station_id" => $bom_item->station_id,
                        "station_operation_id" => $bom_item->station_operation_id,
                        "station_sub_operation_id" => $bom_item->station_sub_operation_id,
                        "input_line_code" => $input_line_code
                    ])->first();

                    if ($bom_item_degree) {
                        foreach ($degree_ids as $degree_id) {
                            BOMDegree::firstOrCreate([
                                "bill_of_material_item_id" => $bom_item_degree->id,
                                "product_id" => $bom_item_degree->product->id,
                                "material_id" => $bom_item_degree->material->id,
                                "degree_id" => $degree_id
                            ]);
                        }

                    }


                }
                break;
            case 3:

                foreach ($degree_ids as $degree_id) {
                    BOMDegree::firstOrCreate([
                        "bill_of_material_item_id" => $bom_item->id,
                        "product_id" => $product->id,
                        "material_id" => $material->id,
                        "degree_id" => $degree_id
                    ]);
                }

                break;

        }

        session([
            "input_line_code_from" => null,
            "input_line_code_to" => null,
        ]);

        if ($request->back_to_bom == 1) {
            return back()->with(["success" => " درجه(ها) با موفقیت اضافه شد."]);
        } else {
            if ($product_creation_process) {
                return redirect()->route("line_product_station.product.product_creation.bom.index", [$product_creation_process,$bom_item->bom->product_route->code])->with(["success" => " درجه(ها) با موفقیت اضافه شد."]);

            } else {
                return redirect()->route("line_product_station.product.bom.index", [$product,$bom_item->bom->product_route->code])->with(["success" => " درجه(ها) با موفقیت اضافه شد."]);
            }
        }

    }

    public function delete(BOMDegree $BOM_degree, Product $product, Product $material, Degree $degree)
    {

        BOMDegree::where([
            "id" => $BOM_degree->id,
            "product_id" => $product->id,
            "material_id" => $material->id,
            "degree_id" => $degree->id
        ])->delete();

        return back()->with(["success" => "عملیات حذف با موفقیت انجام شد."]);
    }

}
