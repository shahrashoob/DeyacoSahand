<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;


class MachineController extends Controller {
    public static $perfix_production_status_code = "7203";
    var $view_path = "goods_kind_process.warps.machine.";
    var $route_path = "warps.machine.";

    public function __construct() {

        View::share( "perfix_status_code", MachineController::$perfix_production_status_code );
    }




}
