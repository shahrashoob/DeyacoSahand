<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BOMController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.bom.";
    public $view_path = "line_product_station.product.product_creation.product_show.bom.";

    public function index(ProductCreationProcess $product_creation_process)
    {
        $show_route_code = "01";
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process, $show_route_code);
    }

    public function bom_degree(ProductCreationProcess $product_creation_process, Product $material, BOMItem $bom_item)
    {

        $product = $product_creation_process->product;
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

        return view($this->view_path . "bom_degree", compact("product", "material", "list", "degree_option", "bom_item", "input_line_code_from", "input_line_code_to", "product_creation_process"));
    }

    public function bom_replace(ProductCreationProcess $product_creation_process, Product $material, BOMItem $bom_item)
    {

        $product = $product_creation_process->product;
        $list = BOMReplace::
        where([
            "product_id" => $product->id,
            "material_id" => $material->id,
            "bill_of_material_item_id" => $bom_item->id
        ])->
        get();

        $replace_product_list = ReplaceProduct::where("product_id", $material->id)->get();


        return view($this->view_path . "bom_replace", compact("product", "material", "list", "replace_product_list", "bom_item", "product_creation_process"));
    }
    public function bom_fault_illegal(ProductCreationProcess $product_creation_process, Product $material, BOMItem $bom_item){

        $product = $product_creation_process->product;
        $product_fault_list = $bom_item->material->goods_kind->product_fault()->get();

        $bom_item_fault_illegals = $bom_item->bom_fault_illegals()->pluck( "product_fault_id", "product_fault_id" )->toArray();

        return view( $this->view_path . "bom_fault_illegal", compact( "product", "material", "bom_item_fault_illegals", "product_fault_list", "bom_item","product_creation_process" ) );
    }

}
