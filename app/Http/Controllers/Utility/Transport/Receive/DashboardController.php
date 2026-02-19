<?php

namespace App\Http\Controllers\Utility\Transport\Receive;

use App\Http\Controllers\Controller;
use App\Models\Utility\Transport\Transport;
use Illuminate\Http\Request;


class DashboardController extends Controller {

    var $view_path = "utility.transport.receive.dashboard.";
    var $route_path = "utility.transport.receive.dashboard.";

    public function index() {

        $transport_list = Transport::where("transport_type_id",1)->where( "car_id", ">", 0 )->orderByDesc( "id" )->paginate();

        return view( $this->view_path . "index", compact(  "transport_list" ) );

    }

}
