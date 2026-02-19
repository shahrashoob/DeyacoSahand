<?php

namespace App\Http\Controllers\LineProductStation\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller {
    //
    public function index( Request $request ) {
        if ( $request->isMethod( 'post' ) ) {
            $search          = $request->search;
            $line_id   = $request->line_id;
            $machine_type_id = $request->machine_type_id;
        } else {
            $search          = session( "search_machine" );
            $line_id   = session( "line_id" );
            $machine_type_id = session( "machine_type_id" );
        }
        session( [
            "search_machine"   => $search,
            "line_id" => $line_id,
            "machine_type_id"        => $machine_type_id,
        ] );


        $list = Machine::join("stations","stations.id","station_id")->
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "machines.code", "like", "%" . $search . "%" )->
                orWhere( "machines.caption", "like", "%" . $search . "%" );
            } );

        } )->
        when( $line_id != 0, function ( $query ) use ( $line_id ) {
            return $query->where( "line_id", $line_id );
        } )->
        when( $machine_type_id != 0, function ( $query ) use ( $machine_type_id ) {
            return $query->where( "machine_type_id", $machine_type_id );
        } )
           ->select("machines.id","machines.code","machines.caption","machines.station_id","machine_type_id","production_status_id")->
        paginate( 50 );;

        $line_option=Option::get("line",$line_id);
        $machine_type_option=Option::get("machine_type",$machine_type_id);
        return view( "line_product_station.machine.dashboard.index", compact( "list","search","machine_type_option","line_option" ) );
    }
    public function view(Machine $machine){
        // Check Permission
        $this->checkPermission($machine);
        return redirect()->route(
            Str::lower($machine->station->line->goods_kind->caption_en).".machine.dashboard.view",
            $machine
        );
    }

    public function checkPermission($machine){

    }
}
