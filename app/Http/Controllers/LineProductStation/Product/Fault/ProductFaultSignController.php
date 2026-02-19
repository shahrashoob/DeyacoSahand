<?php

namespace App\Http\Controllers\LineProductStation\Product\Fault;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\LineProduct\Product\Fault\ProductFaultSign;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class   ProductFaultSignController extends Controller{

    private $view_path = "line_product_station.product.fault.product_fault_sign.";
    private $route_path = "line_product_station.product.fault.product_fault_sign.";

    public function index( Request $request ) {

        $list = ProductFaultSign::paginate( 20 );

        $order_by_Option = null;
        $search          = "";

        return view( $this->view_path . "index", compact( "list", "search", "order_by_Option" ) );
    }

    public function create() {
        $product_fault_sign                         = new ProductFaultSign();
        return view( $this->view_path . "create", compact( "product_fault_sign"  ) );
    }

    public function store( Request $request ) {

        if ( $request->caption == "" || ProductFaultSign::ExistsCaption( $request->caption ) ) {
            return back()->withErrors( "عنوان نمود  تکراری است" );
        }
        $product_fault_sign           = ProductFaultSign::create( $request->all() );


        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک نمود نقص کالا با موفقیت اضافه شد" ] );

    }

    public function edit( ProductFaultSign $product_fault_sign ) {

        return view( $this->view_path . "edit", compact( "product_fault_sign") );

    }

    public function update( Request $request,  ProductFaultSign $product_fault_sign ) {
        if ( $request->caption == "" || ProductFaultSign::ExistsCaption( $request->caption, $product_fault_sign->id ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }

        $product_fault_sign->update( $request->all() );

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
}
