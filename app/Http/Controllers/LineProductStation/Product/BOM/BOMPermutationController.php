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

class BOMPermutationController extends Controller {
    //
    var $view_path = "line_product_station.product.bom.bom_permutation.";
    var $route_path = "line_product_station.product.bom_permutation.";

    public function index( Product $product ) {

        return self::GetIndex($product,$this->view_path,$this->route_path,null);
    }
    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        foreach ( $product->route()->where( "active_status_id", 1200 )->get() as $route ) {
            foreach ( $route->bom as $bom ) {
                Product\BOM\BOMPermutation::CreateBOMMood( $bom );
            }
        }

        return view($view_path . "index", compact("product", "product_creation_process",        "view_path","route_path"));
    }

    public function submit( Request $request ,Product $product) {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route( "line_product_station.product.replace_product.index", [ $product ] )->with( [ "success" => $result["message"] ] );
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public static function PostSubmit(Request $request, Product $product)
    {
        if ( isset( $request->permutation ) ) {
            $permutation = $request->permutation;

            $permutation_ids = array_keys( $permutation );
            $list            = Product\BOM\BOMPermutation::whereIn( "id", $permutation_ids )->get();
            foreach ( $list as $item ) {
                if ( isset( $permutation[ $item->id ] ) ) {
                    $item->weight = $permutation[ $item->id ];
                    $item->save();
                }
            }
        }

        return [
            "result" => true,
            "message" => " اطلاعات کالای جایگزین تولید با موفقیت ثبت گردید."
        ];


    }

}
