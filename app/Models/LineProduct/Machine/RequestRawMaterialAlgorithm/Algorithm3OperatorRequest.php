<?php

namespace App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Script\Script;

class Algorithm3OperatorRequest extends Controller {
    //
    public static function RequestForMachine( Machine $machine, Script $script, $special_goods_kind_id, $user_id, $emergency_time , $production_type_id  ) {

        $log_data                       = [];
        $log_data["emergency_time"]     = $emergency_time;
        $log_data["algorithm"]          = "Algorithm3OperatorRequest";
        $log_data["machine"]["id"]      = $machine->id;
        $log_data["machine"]["caption"] = $machine->caption;
        $log_data["message"]="الگوریتم 3 برای درخواست های اتوماتیک وجود ندارد.";
        $log                 = AlgorithmFunction::log( $script, 0, 41003, 0, $log_data, $user_id, $machine->id );

        return [ "result" => false, "log_data" => $log_data ];
    }
}
