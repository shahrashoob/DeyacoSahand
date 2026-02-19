<?php

namespace App\Http\Controllers\LineProductStation\GoodsKind;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class GoodsKindProductFaultController extends Controller {
    private $view_path = "line_product_station.goods_kind.product_fault.";
    private $route_path = "line_product_station.goods_kind.product_fault.";

    public function index( GoodsKind $goods_kind ) {

        $product_fault_option = Option::get( "product_fault" );

        return view( $this->view_path . "index", compact( "goods_kind", "product_fault_option" ) );
    }

    public function store( Request $request, GoodsKind $goods_kind ) {

        $packing_type = ProductFault::find( $request->product_fault_id );
        if ( ! $packing_type ) {
            return back()->withErrors( "نوع نقص معتبر نمی باشد." );
        }
        $exists = GoodsKind\GoodsKindProductFault::where( [
            "goods_kind_id"    => $goods_kind->id,
            "product_fault_id" => $request->product_fault_id
        ] )->exists();
        if ( $exists ) {
            return back()->withErrors( "این نقص قبلا به رسته کالایی اضافه شده است." );
        }
        GoodsKind\GoodsKindProductFault:: create( [
            "goods_kind_id"    => $goods_kind->id,
            "product_fault_id" => $request->product_fault_id
        ] );

        return redirect()->back()->with( [ "success" => "نقص با موفقیت اضافه شد" ] );

    }

    public function delete( GoodsKind $goods_kind, GoodsKind\GoodsKindProductFault $goods_kind_product_fault ) {

        $bom_fault_illegals = BOMFaultIllegal::
        join( "products", "products.id", "material_id" )->
        where( [
            "goods_kind_id" => $goods_kind_product_fault->goods_kind_id,
            "product_fault_id" => $goods_kind_product_fault->product_fault_id,
        ] )->get();

        if ( count( $bom_fault_illegals ) ) {
            $message = "با توجه به اینکه نقص در کالاهای زیر وجود دارد، امکان حذف آن امکان پذیر نیست.";
            foreach ( $bom_fault_illegals as $item ) {
                $message .= "<br/>" . $item->product->fullCaption();
            }

            return back()->withErrors( $message );
        }


        GoodsKind\GoodsKindProductFault:: where( [
            "goods_kind_id"    => $goods_kind->id,
            "product_fault_id" => $goods_kind_product_fault->product_fault_id,
            "id" => $goods_kind_product_fault->id
        ] )->delete();

        return redirect()->back()->with( [ "success" => "نقص با موفقیت حذف شد" ] );
    }


}
