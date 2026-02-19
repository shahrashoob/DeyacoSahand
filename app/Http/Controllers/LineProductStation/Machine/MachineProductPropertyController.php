<?php

namespace App\Http\Controllers\LineProductStation\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class MachineProductPropertyController extends Controller
{
    protected $route_path="";
    protected $view_path="line_product_station.station.machine_product_property.";
    public function index( Station $station ) {

        return view( $this->view_path."index", compact( "station" ) );
    }
}
