<?php

namespace App\Http\Controllers\GoodsKindProcess\General\ProductionCard;

use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class GeneralTerminateProductionController extends Controller {
    var $view_path = "goods_kind_process.general.production_card.terminate_production.";
    var $route_path;
    var $dashboard_route;

    public function __construct() {

    }

    public function index( Production $production ) {

        $message = $this->getErrorMessage( $production );
        if ( $message != "" ) {
            return back()->withErrors( $message );
        }

        view()->share( 'route_path', $this->route_path );
        view()->share( 'dashboard_route', $this->dashboard_route );

        return view( $this->view_path . "index", compact( "production" ) );
    }

    public function submit( Request $request, Production $production ) {
        $message = $this->getErrorMessage( $production );
        if ( $message != "" ) {
            return back()->withErrors( $message );
        }

        $production->status_id = 520; // خاتمه یافته
        //در انتظار تخصیص ماشین، .
        $production->waiting_status_id = $this->waiting_status_id;
        $production->save();
        event( new ProductionCardLogEvent( $production, $request->description,null,7008006 ) );


        return redirect()->route( $this->dashboard_route . "view_card", $production )->with( [ "success" => "کارت تولید با موفقیت خاتمه یافته شد." ] );

    }

    public function getErrorMessage( Production $production ) {
        $message = "";
        $k       = 1;
        foreach ( $production->machine_allocation()->groupBy( "machine_id" )->get() as $machine_allocation ) {
            $message .= $k . " - " . $machine_allocation->machine->caption . "<br/>";
        }
        foreach ( $production->machine_reserve()->groupBy( "machine_id" )->get() as $machine_allocation ) {
            $message .= $k . " - " . $machine_allocation->machine->caption . "<br/>";
        }
        if ( $message != "" ) {
            $message = "با توجه به اینکه کارت تولید بر روی ماشین های زیر رزرو شده یا کارت جاری آنها است، امکان خاتمه یافته کردن  وجود ندارد."
                       . "<br/>" . $message;
        }

        // اگر تخصیصی دارد که در انتظار تخصیص مجدد است، نباید بتواند کارت را خاتمه یافته کند.
        $list= MachineAllocation::where("production_id",$production->id)->
        whereIn("status_id",[5310050,5310060])->get();

        if(count($list) > 0){
            $message= "با توجه به اینکه کارت یک یا چند تخصیص دارد که در انتظار تخصیص مجدد به ماشین بعدی می باشد، امکان خاتمه یافته کردن کارت وجود ندارد."
            ;
        }

        return $message;
    }

}
