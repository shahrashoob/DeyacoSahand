<?php

namespace App\Http\Controllers\LineProductStation\Machine\Fault;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\Fault\MachineFaultMachineFaultSign;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class MachineFaultController extends Controller {

    private $view_path = "line_product_station.machine.fault.machine_fault.";
    private $route_path = "line_product_station.machine.fault.machine_fault.";

    public function index( Request $request ) {

        $list = MachineFault::paginate( 20 );

        $order_by_Option = null;
        $search          = "";

        return view( $this->view_path . "index", compact( "list", "search", "order_by_Option" ) );
    }

    public function create() {

        $machine_fault             = new MachineFault();
        $machine_fault_sign_option = Option::get( "machine_fault_sign", 0, $machine_fault->id, [] );

        return view( $this->view_path . "create", compact( "machine_fault", "machine_fault_sign_option" ) );
    }

    public function store( Request $request ) {

        if ( $request->caption == "" || MachineFault::ExistsCaption( $request->caption ) ) {
            return back()->withErrors( "عنوان نقص  تکراری است" );
        }
        $request["need_to_confirmation"]         = isset( $request->need_to_confirmation ) ? 1 : 0;
        $request["need_to_confirmation_for_fix"] = isset( $request->need_to_confirmation_for_fix ) ? 1 : 0;
        $machine_fault                           = MachineFault::create( $request->all() );
        $machine_fault->getCode();
        if ( $request->machine_fault_machine_fault_sign ) {
            foreach ( $request->machine_fault_machine_fault_sign as $item ) {
                MachineFaultMachineFaultSign::
                create( [ "machine_fault_id" => $machine_fault->id, "machine_fault_sign_id" => $item ] );
            }
        }

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک نقص با موفقیت اضافه شد" ] );

    }

    public function edit( MachineFault $machine_fault ) {

        $values                    = MachineFaultMachineFaultSign::
        where( "machine_fault_id", $machine_fault->id )->
        pluck( "machine_fault_sign_id" )->
        toArray();
        $machine_fault_sign_option = Option::get( "machine_fault_sign", 0, $machine_fault->id, $values );

        return view( $this->view_path . "edit", compact( "machine_fault", "machine_fault_sign_option" ) );

    }

    public function update( Request $request, MachineFault $machine_fault ) {


        if ( $request->caption == "" || MachineFault::ExistsCaption( $request->caption, $machine_fault->id ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }

        $request["need_to_confirmation"]         = isset( $request->need_to_confirmation ) ? 1 : 0;
        $request["need_to_confirmation_for_fix"] = isset( $request->need_to_confirmation_for_fix ) ? 1 : 0;
        $machine_fault->update( $request->all() );

        MachineFaultMachineFaultSign::
        where( "machine_fault_id", $machine_fault->id )->
        delete();
        if ( $request->machine_fault_machine_fault_sign ) {
            foreach ( $request->machine_fault_machine_fault_sign as $item ) {
                MachineFaultMachineFaultSign::
                create( [ "machine_fault_id" => $machine_fault->id, "machine_fault_sign_id" => $item ] );
            }
        }

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
}
