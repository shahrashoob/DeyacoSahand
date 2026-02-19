<?php

namespace App\Http\Controllers\LineProductStation\Product\Fault;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultProperty;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultSign;
use App\Models\LineProduct\Product\Fault\ProductFaultPropertyValue;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductFaultController extends Controller {

    private $view_path = "line_product_station.product.fault.product_fault.";
    private $route_path = "line_product_station.product.fault.product_fault.";

    public function index( Request $request ) {

        $list = ProductFault::paginate( 20 );

        $order_by_Option = null;
        $search          = "";

        return view( $this->view_path . "index", compact( "list", "search", "order_by_Option" ) );
    }

    public function create() {

        $product_fault             = new ProductFault();
        $product_fault_sign_option = Option::get( "product_fault_sign", 0, $product_fault->id, [] );

        $product_fault_property_id_option=Option::get("product_fault_property");

        return view( $this->view_path . "create", compact( "product_fault", "product_fault_sign_option","product_fault_property_id_option" ) );
    }

    public function store( Request $request ) {

        if ( $request->caption == "" || ProductFault::ExistsCaption( $request->caption ) ) {
            return back()->withErrors( "عنوان نقص  تکراری است" );
        }
        $request["need_to_move_shift"]   = isset( $request->need_to_move_shift ) ? 1 : 0;
        $request["need_to_confirmation"] = isset( $request->need_to_confirmation ) ? 1 : 0;
        $product_fault                   = ProductFault::create( $request->all() );
        $product_fault->getCode();

        if ( $request->product_fault_product_fault_sign ) {
            foreach ( $request->product_fault_product_fault_sign as $item ) {
                ProductFaultProductFaultSign::
                create( [ "product_fault_id" => $product_fault->id, "product_fault_sign_id" => $item ] );
            }
        }

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک نقص کالا با موفقیت اضافه شد" ] );

    }

    public function edit( ProductFault $product_fault ) {
        $values = ProductFaultProductFaultSign::
        where( "product_fault_id", $product_fault->id )->
        pluck( "product_fault_sign_id" )->
        toArray();
        $values_properties = ProductFaultProductFaultProperty::
        where( "product_fault_id", $product_fault->id )->
        pluck( "product_fault_property_id" )->
        toArray();

        $product_fault_property_id_option=Option::get("product_fault_property",0,0, $values_properties);

        $product_fault_sign_option = Option::get( "product_fault_sign", 0, $product_fault->id, $values );

        return view( $this->view_path . "edit", compact( "product_fault", "product_fault_sign_option","product_fault_property_id_option" ) );

    }

    public function update( Request $request, ProductFault $product_fault ) {
        if ( $request->caption == "" || ProductFault::ExistsCaption( $request->caption, $product_fault->id ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }

        $request["need_to_move_shift"]   = isset( $request->need_to_move_shift ) ? 1 : 0;
        $request["need_to_confirmation"] = isset( $request->need_to_confirmation ) ? 1 : 0;
        $product_fault->update( $request->all() );

        ProductFaultProductFaultSign::
        where( "product_fault_id", $product_fault->id )->
        delete();

        if ( $request->product_fault_product_fault_sign ) {
            foreach ( $request->product_fault_product_fault_sign as $item ) {
                ProductFaultProductFaultSign::
                create( [ "product_fault_id" => $product_fault->id, "product_fault_sign_id" => $item ] );
            }
        }

        ProductFaultProductFaultProperty::
        where( "product_fault_id", $product_fault->id )->
        delete();
        if ( $request->product_fault_property_ids ) {
            foreach ( $request->product_fault_property_ids as $value ) {
                ProductFaultProductFaultProperty::
                create( [ "product_fault_id" => $product_fault->id, "product_fault_property_id" => $value ] );
            }
        }



        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
}
