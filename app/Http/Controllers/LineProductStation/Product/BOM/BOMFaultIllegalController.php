<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BOMFaultIllegalController extends Controller {
    //
    var $view_path = "line_product_station.product.bom.bom_fault_illegal.";
    var $route_path = "line_product_station.product.bom.bom_fault_illegal.";

    public function index( Product $product, Product $material, BOMItem $bom_item ,$product_creation_process=null) {


        $product_fault_list = $bom_item->material->goods_kind->product_fault()->get();

        $bom_item_fault_illegals = $bom_item->bom_fault_illegals()->pluck( "product_fault_id", "product_fault_id" )->toArray();

        return view( $this->view_path . "index", compact( "product", "material", "bom_item_fault_illegals", "product_fault_list", "bom_item","product_creation_process" ) );
    }

    public function store( Request $request, Product $product, Product $material, BOMItem $bom_item ,$product_creation_process=null) {

        $bom_item->bom_fault_illegals()->delete();
        if ( $request->bom_item_fault_illegal ) {
            foreach ( $request->bom_item_fault_illegal as $product_fault_id => $value ) {

                Product\BOM\BOMFaultIllegal::create( [
                    "bill_of_material_id"      => $bom_item->bom->id,
                    "bill_of_material_item_id" => $bom_item->id,
                    "product_id"               => $bom_item->product_id,
                    "material_id"              => $material->id,
                    "product_fault_id"         => $product_fault_id,
                ] );
            }
        }

        if($product_creation_process){
            return redirect()->route("line_product_station.product.product_creation.bom.index", $product_creation_process)->with(["success" => " نقض های غیر مجاز با موفقیت ثبت گردید."]);
        }
        else {
            return redirect()->route("line_product_station.product.bom.index", $product)->with(["success" => " نقض های غیر مجاز با موفقیت ثبت گردید."]);
        }

    }


}
