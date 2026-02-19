<?php

namespace App\Http\Controllers\LineProductStation\Machine\Fault;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Fault\MachineFaultSign;
use Illuminate\Http\Request;

class MachineFaultSignController extends Controller{

    private $view_path = "line_product_station.machine.fault.machine_fault_sign.";
    private $route_path = "line_product_station.machine.fault.machine_fault_sign.";

    public function index( Request $request ) {

        $list = MachineFaultSign::paginate( 20 );

        $order_by_Option = null;
        $search          = "";

        return view( $this->view_path . "index", compact( "list", "search", "order_by_Option" ) );
    }

    public function create() {

        $machine_fault_sign = new MachineFaultSign();

        return view( $this->view_path . "create", compact( "machine_fault_sign",  ) );
    }

    public function store( Request $request ) {

        if ( $request->caption == "" || MachineFaultSign::ExistsCaption( $request->caption ) ) {
            return back()->withErrors( "عنوان نمود نقص  تکراری است" );
        }
       MachineFaultSign::create( $request->all() );


        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک نمود گروه ماشین با موفقیت اضافه شد" ] );

    }

    public function edit( MachineFaultSign $machine_fault_sign ) {

        return view( $this->view_path . "edit", compact( "machine_fault_sign" ) );

    }

    public function update( Request $request,  MachineFaultSign $machine_fault_sign ) {


        if ( $request->caption == "" || MachineFaultSign::ExistsCaption( $request->caption, $machine_fault_sign->id ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }


        $machine_fault_sign->update( $request->all() );

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
}
